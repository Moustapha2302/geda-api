<?php

// app/Http/Controllers/Api/SignatureController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SignatureController extends Controller
{
    public function sign(Request $request, string $service, string $id)
    {
        // Vérif chef
        $serviceModel = Service::findOrFail($service);
        $this->authorize('sign', $serviceModel); // ServicePolicy::sign

        $doc = Document::where('service_id', $service)
                       ->where('id', $id)
                       ->firstOrFail();

        // TODO : appeler le service SES/SEA (stub pour l’instant)
        return response()->json([
            'success' => true,
            'data'    => [
                'document_id' => $doc->id,
                'status'      => 'signed',
                'signed_at'   => now()->toIso8601String(),
                'signature_id'=> uniqid('sig_'),
            ],
            'message' => 'Document signé électroniquement'
        ], 200);
    }
}