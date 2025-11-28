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
        $path = "service-{$service}/{$uuid}";
        if (Storage::disk('local-signed')->exists($path)) {
            Storage::disk('local-signed')->delete($path);
            return response()->noContent();
        }
        return response()->json(['message' => 'Fichier introuvable'], 404);
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