<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Liste des documents du service connecté
     */
    public function index(Request $request, string $service)
    {
        // TODO : vérifier que le user appartient bien à ce service
        $docs = Document::where('service_id', $service)->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $docs,
            'message' => 'Documents du service ' . $service
        ]);
    }
}
