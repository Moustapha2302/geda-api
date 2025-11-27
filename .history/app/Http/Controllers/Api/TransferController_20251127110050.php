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
        Log::debug('Policy check', [
            'user_id' => $request->user()->id,
            'user_service' => $request->user()->service_id,
            'user_role' => $request->user()->role,
            'requested_service' => $service,
        ]);

        // ✅ Récupérer l'objet Service au lieu d'utiliser le string
        $serviceModel = Service::findOrFail($service);

        // ✅ Passer l'objet Service à la policy
        $this->authorize('transfer', $serviceModel);

        $validated = $request->validate([
            'document_id' => 'required|exists:documents,id',
            'to_service_id' => 'required|exists:services,id|different:' . $service,
            'type' => 'required|in:internal,external',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $doc = Document::where('id', $validated['document_id'])
                       ->where('service_id', $service)
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
            'from_service_id'  => $service,
            'to_service_id'    => $validated['to_service_id'],
            'type'             => $validated['type'],
            'expires_at'       => $validated['expires_at'] ?? now()->addDays(7),
            'status'           => 'pending',
            'initiated_by'     => $request->user()->id,
        ]);

        // Notifier le chef du service destinataire (mail ou notification)
        // TODO : queue job

        return response()->json([
            'success' => true,
            'data'    => $transfer->load(['document', 'toService', 'initiatedBy']),
            'message' => 'Transfert initié'
        ], 201);
    }

    /**
     * Display a listing of the resource.
     */
/**
 * Lister les transferts d'un service (envoyés et reçus)
 */
public function index(Request $request, $service)
{
    try {
        \Log::info('Transfer index START', [
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

        // Test simple sans query
        $transfers = Transfer::where('from_service_id', $service)
                            ->orWhere('to_service_id', $service)
                            ->limit(10)
                            ->get();

        return response()->json([
            'success' => true,
            'data' => $transfers,
            'debug' => [
                'service' => $service,
                'user_service' => $user->service_id
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Transfer index ERROR', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Erreur serveur',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}
    /**
     * Display the specified resource.
     */
    public function accept(Request $request, string $service, Transfer $transfer)
{
    // 1. Vérifier que le transfert appartient bien au service connecté
    if ($transfer->to_service_id != $service) {
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
        'status'         => 'accepted',
        'processed_by'   => auth()->id(),
        'processed_at'   => now()
    ]);

    // 4. (Optionnel) déplacer le document vers le service destinataire
    // $transfer->document->update(['service_id' => $transfer->to_service_id]);

    // 5. Retourner la réponse
    return response()->json([
        'success' => true,
        'data'    => $transfer->load(['document', 'fromService']),
        'message' => 'Transfert accepté'
    ], 200);
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
