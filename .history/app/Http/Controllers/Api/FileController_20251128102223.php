<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    /* ---------- 1. UPLOAD ---------- */
    public function upload(Request $request, int $service)
    {
        $request->validate([
            'file' => 'required|file|max:51200', // 50 Mo
        ]);

        $file     = $request->file('file');
        $ext      = $file->extension();
        $uuid     = (string) Str::uuid();
        $filename = $uuid . '.' . $ext;

        $path = $file->storeAs("service-{$service}", $filename, 'local-signed');

        return response()->json([
            'uuid' => $uuid,
            'link' => route('files.download', ['service' => $service, 'uuid' => $filename]),
        ], 201);
    }

    /* ---------- 2. SUPPRESSION ---------- */
   public function destroy(int $service, string $uuid)
{
    // 1. Trouver le document en base de données
    $document = Document::where('uuid', $uuid)
                        ->where('service_id', $service)
                        ->firstOrFail();
    
    // 2. Supprimer le fichier physique
    if (Storage::disk('local')->exists($document->file_path)) {
        Storage::disk('local')->delete($document->file_path);
    }
    
    // 3. Supprimer l'enregistrement en base de données
    $document->delete();
    
    return response()->json([
        'message' => 'Fichier supprimé avec succès'
    ], 200);
}
    /* ---------- 3. DOWNLOAD (stream) ---------- */
    public function download(int $service, string $uuid)
    {
        $path = "service-{$service}/{$uuid}";

        if (!Storage::disk('local-signed')->exists($path)) {
            abort(404);
        }

        return response()->stream(function () use ($path) {
            $stream = Storage::disk('local-signed')->readStream($path);
            fpassthru($stream);
            if (is_resource($stream)) fclose($stream);
        }, 200, [
            'Content-Type'        => Storage::disk('local-signed')->path($path),
            'Content-Disposition' => 'attachment; filename="' . basename($path) . '"',
        ]);
    }
}