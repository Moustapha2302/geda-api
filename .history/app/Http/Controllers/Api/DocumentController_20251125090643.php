<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

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
        'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
    ]);

    $file     = $request->file('file');
    $uuid     = Str::uuid();
    $ext      = $file->extension();
    $fileName = $uuid . '.' . $ext;
    $dir      = "services/{$service}";
    $path     = $file->storeAs($dir, $fileName, 'local'); // storage/app/services/2/uuid.ext

    // Enregistrement DB
    $doc = Document::create([
        'uuid'       => $uuid,
        'title'      => $file->getClientOriginalName(),
        'file_path'  => $path,
        'service_id' => $service,
        'user_id'    => $request->user()->id,
        'md5'        => md5_file(Storage::path($path)),
        'status'     => 'pending',
    ]);

    return response()->json([
        'success' => true,
        'data'    => $doc->only(['id', 'uuid', 'title', 'file_path', 'status']),
        'message' => 'Document enregistré'
    ], 201);
}

public function batchStore(Request $request, string $service)
{
    $request->validate([
        'files'   => 'required|array|max:10',
        'files.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240', // 10 Mo chacun
    ]);

    $created = collect();

    foreach ($request->file('files') as $file) {
        $uuid     = Str::uuid();
        $ext      = $file->extension();
        $fileName = $uuid . '.' . $ext;
        $path     = $file->storeAs("services/{$service}", $fileName, 'local');

        $doc = Document::create([
            'uuid'       => $uuid,
            'title'      => $file->getClientOriginalName(),
            'file_path'  => $path,
            'service_id' => $service,
            'user_id'    => $request->user()->id,
            'md5'        => md5_file(Storage::path($path)),
            'status'     => 'pending',
        ]);

        $created->push($doc->only(['id', 'uuid', 'title', 'file_path', 'status']));
    }

    return response()->json([
        'success' => true,
        'data'    => $created,
        'message' => count($created) . ' documents créés'
    ], 201);
}

// DocumentController
public function show(Request $request, string $service, string $id)
{
    $doc = Document::where('service_id', $service)
                   ->where('id', $id)
                   ->firstOrFail();

    // on ajoute l’URL de téléchargement signée 5 min
    $downloadUrl = URL::temporarySignedRoute(
        'documents.download', now()->addMinutes(5), ['service' => $service, 'id' => $id]
    );

    return response()->json([
        'success' => true,
        'data'    => [
            'document' => $doc->only(['id', 'uuid', 'title', 'status', 'ocr_text', 'created_at', 'updated_at']),
            'download_url' => $downloadUrl,
        ],
        'message' => 'Détail du document'
    ]);
}

public function download(Request $request, string $service, string $id)
{
    $doc = Document::where('service_id', $service)
                   ->where('id', $id)
                   ->firstOrFail();

    $path = storage_path('app/' . $doc->file_path);

    if (!file_exists($path)) {
        abort(404, 'Fichier introuvable');
    }

    return response()->download($path, $doc->title);
}
}
