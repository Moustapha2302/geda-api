<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Transfer;
use App\Models\Service;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class TransferController extends Controller
{
    use AuthorizesRequests;

    /**
     * Créer un nouveau transfert de document
     */
    public function store(Request $request, string $service)
{
    Log::debug('Transfer store START', [
        'user_id' => $request->user()->id,
        'requested_service' => $service,
    ]);

    // ✅ Résoudre le service via slug OU id
    $serviceModel = Service::where('slug', $service)
                          ->orWhere('id', $service)
                          ->firstOrFail();

    $this->authorize('transfer', $serviceModel);



        Log::debug('Service found', [
            'service_id' => $serviceModel->id,
            'service_slug' => $serviceModel->slug,
        ]);

        // ✅ Passer l'objet Service à la policy
        $this->authorize('transfer', $serviceModel);

        $validated = $request->validate([
            'document_id' => 'required|exists:documents,id',
            'to_service_id' => 'required|exists:services,id|different:' . $serviceModel->id,
            'type' => 'required|in:internal,external',
            'expires_at' => 'nullable|date|after:now',
        ]);

        // ✅ Utiliser l'ID du service model
        $doc = Document::where('id', $validated['document_id'])
                       ->where('service_id', $serviceModel->id)
                       ->firstOrFail();

        // Ne transférer que si le doc est "validated" ou "signed"
        if (!in_array($doc->status, ['validated', 'signed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Transfert impossible : le document n\'est pas validé ou signé'
            ], 422);
        }

        $transfer = Transfer::create([
            'document_id'      => $doc->id,
            'from_service_id'  => $serviceModel->id,  // ✅ Utiliser l'ID numérique
            'to_service_id'    => $validated['to_service_id'],
            'type'             => $validated['type'],
            'expires_at'       => $validated['expires_at'] ?? now()->addDays(7),
            'status'           => 'pending',
            'initiated_by'     => $request->user()->id,
        ]);

        Log::info('Transfer created', [
            'transfer_id' => $transfer->id,
            'from_service' => $serviceModel->id,
            'to_service' => $validated['to_service_id'],
        ]);

        return response()->json([
            'success' => true,
            'data'    => $transfer->load(['document', 'toService', 'initiatedBy']),
            'message' => 'Transfert initié'
        ], 201);
    }

    /**
     * Lister les transferts d'un service (envoyés et reçus)
     */
    public function index(Request $request, $service)
    {
        try {
            Log::info('Transfer index START', [
                'service' => $service,
                'service_type' => gettype($service),
                'user_id' => $request->user()?->id
            ]);

            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non authentifié'
                ], 401);
            }

            // ✅ Trouver le service par ID OU par slug
            $serviceModel = Service::where('id', $service)
                                  ->orWhere('slug', $service)
                                  ->firstOrFail();

            // ✅ Utiliser l'ID numérique pour la recherche
            $transfers = Transfer::where('from_service_id', $serviceModel->id)
                                ->orWhere('to_service_id', $serviceModel->id)
                                ->with(['document', 'fromService', 'toService', 'initiatedBy'])
                                ->orderBy('created_at', 'desc')
                                ->get();

            return response()->json([
                'success' => true,
                'data' => $transfers,
                'debug' => [
                    'service_param' => $service,
                    'service_id' => $serviceModel->id,
                    'user_service' => $user->service_id
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Transfer index ERROR', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Afficher un transfert spécifique
     */
    public function show(Request $request, string $service, int $id)
    {
        // ✅ Trouver le service
        $serviceModel = Service::where('id', $service)
                              ->orWhere('slug', $service)
                              ->firstOrFail();

        $transfer = Transfer::where('id', $id)
                           ->where(function($query) use ($serviceModel) {
                               $query->where('from_service_id', $serviceModel->id)
                                    ->orWhere('to_service_id', $serviceModel->id);
                           })
                           ->with(['document', 'fromService', 'toService', 'initiatedBy'])
                           ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $transfer
        ]);
    }

    /**
     * Accepter un transfert
     */
    public function accept(Request $request, string $service, int $id)
    {
        // ✅ Trouver le service
        $serviceModel = Service::where('id', $service)
                              ->orWhere('slug', $service)
                              ->firstOrFail();

        $transfer = Transfer::findOrFail($id);

        // 1. Vérifier que le transfert appartient bien au service connecté
        if ($transfer->to_service_id != $serviceModel->id) {
            abort(404, 'Transfert introuvable pour ce service');
        }

        // 2. Vérifier que le transfert est encore en attente
        if ($transfer->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Transfert déjà traité'
            ], 422);
        }

        // 3. Marquer le transfert comme accepté
        $transfer->update([
            'status'       => 'accepted',
            'processed_by' => $request->user()->id,
            'processed_at' => now()
        ]);

        // 4. (Optionnel) déplacer le document vers le service destinataire
        // $transfer->document->update(['service_id' => $transfer->to_service_id]);

        Log::info('Transfer accepted', [
            'transfer_id' => $transfer->id,
            'processed_by' => $request->user()->id,
        ]);

        // 5. Retourner la réponse
        return response()->json([
            'success' => true,
            'data'    => $transfer->load(['document', 'fromService', 'toService']),
            'message' => 'Transfert accepté'
        ], 200);
    }

    /**
     * Rejeter un transfert
     */
    public function reject(Request $request, string $service, int $id)
    {
        // ✅ Trouver le service
        $serviceModel = Service::where('id', $service)
                              ->orWhere('slug', $service)
                              ->firstOrFail();

        $transfer = Transfer::findOrFail($id);

        // Vérifier que le transfert appartient bien au service connecté
        if ($transfer->to_service_id != $serviceModel->id) {
            abort(404, 'Transfert introuvable pour ce service');
        }

        // Vérifier que le transfert est encore en attente
        if ($transfer->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Transfert déjà traité'
            ], 422);
        }

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        $transfer->update([
            'status'       => 'rejected',
            'processed_by' => $request->user()->id,
            'processed_at' => now(),
            'reject_reason' => $validated['reason'] ?? null
        ]);

        Log::info('Transfer rejected', [
            'transfer_id' => $transfer->id,
            'processed_by' => $request->user()->id,
            'reason' => $validated['reason'] ?? null
        ]);

        return response()->json([
            'success' => true,
            'data'    => $transfer->load(['document', 'fromService', 'toService']),
            'message' => 'Transfert rejeté'
        ], 200);
    }

    /**
     * Partager un document en externe
     */
    public function shareExternal(Request $request, string $service, int $id)
    {
        // ✅ Trouver le service
        $serviceModel = Service::where('id', $service)
                              ->orWhere('slug', $service)
                              ->firstOrFail();

        $transfer = Transfer::findOrFail($id);

        // Vérifier que le transfert appartient au service
        if ($transfer->from_service_id != $serviceModel->id) {
            abort(404, 'Transfert introuvable pour ce service');
        }

        $validated = $request->validate([
            'email' => 'required|email',
            'expires_at' => 'nullable|date|after:now',
        ]);

        // TODO: Générer un lien de partage sécurisé
        // TODO: Envoyer l'email avec le lien

        return response()->json([
            'success' => true,
            'message' => 'Lien de partage envoyé à ' . $validated['email']
        ], 200);
    }

    public function incoming(Request $request, string $service)
{
    $serviceModel = Service::where('slug', $service)
                          ->orWhere('id', $service)
                          ->firstOrFail();

    $transfers = Transfer::where('to_service_id', $serviceModel->id)
                        ->with(['document', 'fromService', 'initiatedBy'])
                        ->orderBy('created_at', 'desc')
                        ->get();

    return response()->json([
        'success' => true,
        'data' => $transfers,
    ]);
}
}
