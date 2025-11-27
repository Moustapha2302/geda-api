<?php

// app/Http/Controllers/Api/WorkflowController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

class WorkflowController extends Controller
{
    use AuthorizesRequests;
    public function show(Request $request, string $service, Document $document)
    {
        // S’assurer que le doc appartient bien au service
        if ($document->service_id != $service) {
            abort(404, 'Document introuvable pour ce service');
        }

        // Stub BPMN
        return response()->json([
            'success' => true,
            'data'    => [
                'document_id' => $document->id,
                'workflow'    => [
                    'id'    => 'wf_' . $document->id,
                    'name'  => 'Circuit de validation service ' . $service,
                    'steps' => [
                        ['order' => 1, 'role' => 'chef', 'action' => 'validation'],
                        ['order' => 2, 'role' => 'admin', 'action' => 'signature'],
                    ],
                ],
            ],
            'message' => 'Étapes BPMN du workflow'
        ]);
    }

    public function start(Request $request, string $service, Document $document)
{
    set_time_limit(0); // pas de timeout
    try {
        $serviceModel = \App\Models\Service::findOrFail($service);
        $this->authorize('start', $serviceModel);

        if ($document->status !== 'ocr_done') {
            return response()->json([
                'success' => false,
                'message' => 'Le workflow ne peut être lancé que sur un document OCRisé'
            ], 422);
        }

        $document->update(['status' => 'in_review']);

        return response()->json([
            'success' => true,
            'data'    => [
                'document_id' => $document->id,
                'status'      => 'in_review',
                'started_at'  => now()->toIso8601String(),
            ],
            'message' => 'Circuit BPMN lancé'
        ], 200);

    } catch (\Throwable $e) {
        Log::error('Workflow start error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Erreur interne : ' . $e->getMessage()
        ], 500);
    }


    // Stub : on passe le statut à “in_review”
    $document->update(['status' => 'in_review']);

    return response()->json([
        'success' => true,
        'data'    => [
            'document_id' => $document->id,
            'status'      => 'in_review',
            'started_at'  => now()->toIso8601String(),
        ],
        'message' => 'Circuit BPMN lancé'
    ], 200);
}

public function validate(Request $request, string $service, Document $document)
{
    $serviceModel = \App\Models\Service::findOrFail($service);
    $this->authorize('validate', $serviceModel);

    // Ne valider que si le workflow est “in_review”
    if ($document->status !== 'in_review') {
        return response()->json([
            'success' => false,
            'message' => 'Validation impossible : le document n’est pas en review'
        ], 422);
    }

    // Stub : on passe le statut à “validated”
    $document->update(['status' => 'validated']);

    return response()->json([
        'success' => true,
        'data'    => [
            'document_id' => $document->id,
            'status'      => 'validated',
            'validated_at'=> now()->toIso8601String(),
        ],
        'message' => 'Étape validée'
    ], 200);
}

public function reject(Request $request, string $service, Document $document)
{
    $serviceModel = \App\Models\Service::findOrFail($service);
    $this->authorize('reject', $serviceModel);

    $request->validate([
        'reason' => 'required|string|min:10|max:1000'
    ]);

    if ($document->status !== 'in_review') {
        return response()->json([
            'success' => false,
            'message' => 'Rejet impossible : le document n’est pas en review'
        ], 422);
    }

    $document->update([
        'status' => 'rejected',
        'rejection_reason' => $request->reason,
        'rejected_at' => now()
    ]);

    return response()->json([
        'success' => true,
        'data' => [
            'document_id' => $document->id,
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
            'rejected_at' => now()->toIso8601String()
        ],
        'message' => 'Étape rejetée'
    ], 200);
}
public function pending(Request $request, string $service)
{
    // Seul le chef peut voir la liste
    $serviceModel = \App\Models\Service::findOrFail($service);
    $this->authorize('pending', $serviceModel); // ServicePolicy::pending

    // Docs en review + service + non encore validés/rejetés
    $docs = Document::where('service_id', $service)
                    ->where('status', 'in_review')
                    ->with('user:id,name,email') // auteur
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);

    return response()->json([
        'success' => true,
        'data'    => $docs,
        'message' => 'Documents en attente de validation pour le service ' . $service
    ]);
}
}
