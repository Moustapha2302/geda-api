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
    public function index()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
