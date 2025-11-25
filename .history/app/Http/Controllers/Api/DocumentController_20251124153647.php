<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
}
