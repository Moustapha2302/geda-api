<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;


class FileController extends Controller
{
    /* ---------- 1. UPLOAD ---------- */
    public function upload(Request $request, int $service)
    {
        $request->validate([
            'file' => 'required|file|max:51200', // 50 Mo
        ]);

                    $file     = $request->file('file');
                    $uuid     = (string) Str::uuid();
                    $ext      = $file->extension();
$filename = $uuid . '.' . $ext;


// On stocke dans le disk local-signed
$path     = $file->storeAs("service-{$service}", $filename, 'local-signed');

return response()->json([
    'uuid' => $uuid,
    'link' => route('files.download', ['service' => $service, 'uuid' => $filename]), // ← avec extension
], 201);
    }

    /* ---------- 2. SUPPRESSION ---------- */
    public function destroy(int $service, string $uuid)
    {
        // On supprime physiquement le fichier
        $path = "service-{$service}/{$uuid}";
        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
            return response()->noContent();
        }
        return response()->json(['message' => 'Fichier introuvable'], 404);
    }

    /* ---------- 3. DOWNLOAD (signed URL 5 min) ---------- */
    public function download(int $service, string $uuid)
{
    // $uuid contient déjà l’extension (.pdf, .jpg, etc.)
    $path = "service-{$service}/{$uuid}";

    if (!Storage::disk('local-signed')->exists($path)) {
        abort(404);
    }

    return Storage::disk('local-signed')->download($path);
}
}
