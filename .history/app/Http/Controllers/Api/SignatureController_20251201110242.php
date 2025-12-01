<?php

// app/Http/Controllers/Api/SignatureController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SignatureController extends Controller
{
    use AuthorizesRequests;
    public function sign(Request $request, string $service, string $id)
{
    // 1. Résoudre le service via slug ou ID
    $serviceModel = Service::where('slug', $service)
                          ->orWhere('id', $service)
                          ->firstOrFail();
    $this->authorize('sign', $serviceModel);

    // 2. Chercher le document avec l’ID numérique du service
    $doc = Document::where('service_id', $serviceModel->id)
                   ->where('id', $id)
                   ->firstOrFail();

    // 3. Stub SES
    $doc->update(['status' => 'signed']); // persistance réelle

    return response()->json([
        'success' => true,
        'data' => [
            'document_id' => $doc->id,
            'status'      => 'signed',
            'signed_at'   => now()->toIso8601String(),
            'signature_id'=> uniqid('sig_'),
        ],
        'message' => 'Document signé électroniquement'
    ], 200);
}
}
