<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
 use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
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

public function store(Request $request, string $service)
{
    $request->validate([
        'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10 Mo
    ]);

    $file     = $request->file('file');
    $uuid     = Str::uuid();
    $ext      = $file->extension();
    $fileName = $uuid . '.' . $ext;

    // Chemins
    $dir  = "services/{$service}";
    $path = $file->storeAs($dir, $fileName, 'local'); // storage/app/services/{service}/uuid.ext

    return response()->json([
        'success' => true,
        'data' => [
            'uuid' => $uuid,
            'path' => $path,
            'name' => $file->getClientOriginalName(),
        ],
        'message' => 'Fichier reçu'
    ], 201);
}
}
