<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Transfer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // ← ajout
use Illuminate\Support\Facades\Gate;

class TransferController extends Controller
{

    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function store(Request $request, string $service)
{
    $this->authorize('transfer', $service); // chef uniquement

    $validated = $request->validate([
        'document_id' => 'required|exists:documents,id',
        'to_service_id' => 'required|exists:services,id|different:' . $service,
        'type' => 'required|in:internal,external',
        'expires_at' => 'nullable|date|after:now',
    ]);

    $doc = Document::where('id', $validated['document_id'])
                   ->where('service_id', $service)
                   ->firstOrFail();

    // Ne transférer que si le doc est “validated” ou “signed”
    if (!in_array($doc->status, ['validated', 'signed'])) {
        return response()->json([
            'success' => false,
            'message' => 'Transfert impossible : le document n’est pas validé ou signé'
        ], 422);
    }

    $transfer = Transfer::create([
        'document_id'      => $doc->id,
        'from_service_id'  => $service,
        'to_service_id'    => $validated['to_service_id'],
        'type'             => $validated['type'],
        'expires_at'       => $validated['expires_at'] ?? now()->addDays(7),
        'status'           => 'pending',
        'initiated_by'     => $request->user()->id, // ✅ Solution propre
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
     * Store a newly created resource in storage.
     */


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
