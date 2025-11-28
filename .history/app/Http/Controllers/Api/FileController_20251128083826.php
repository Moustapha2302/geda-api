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
        $path     = $file->storeAs("service-{$service}", $uuid . '.' . $file->extension(), 'local');

        return response()->json([
            'uuid' => $uuid,
            'link' => route('files.download', ['service' => $service, 'uuid' => $uuid, 'signature' => '']), // on signera après
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
        $path = "service-{$service}/{$uuid}";
        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        // signed URL 5 min
        $url = Storage::disk('local')->temporaryUrl(
            $path,
            now()->addMinutes(5)
        );

        return redirect($url);
    }
}
