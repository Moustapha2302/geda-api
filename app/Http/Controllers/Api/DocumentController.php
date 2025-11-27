<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Service;
use App\Jobs\ProcessOcr; // ✅ Ajout
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Liste des documents du service connecté
     */
    public function index(Request $request, string $service)
    {
        $serviceModel = Service::findOrFail($service);

        // ✅ Ajout: filtres OCR optionnels
        $query = Document::where('service_id', $service);

        if ($request->has('ocr_status')) {
            $query->where('ocr_status', $request->ocr_status);
        }

        if ($request->has('search_ocr') && $request->search_ocr) {
            $query->where('ocr_text', 'like', '%' . $request->search_ocr . '%');
        }

        $docs = $query->paginate(20);

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

        $file = $request->file('file');

        // Calculer le MD5 avant de stocker pour vérifier les doublons
        $md5 = md5_file($file->getRealPath());

        // Vérifier si le fichier existe déjà
        $existingDoc = Document::where('md5', $md5)
                               ->where('service_id', $service)
                               ->first();

        if ($existingDoc) {
            return response()->json([
                'success' => false,
                'message' => 'Ce fichier existe déjà dans ce service',
                'data'    => $existingDoc->only(['id', 'uuid', 'title', 'file_path', 'status'])
            ], 409);
        }

        $uuid     = Str::uuid();
        $ext      = $file->extension();
        $fileName = $uuid . '.' . $ext;
        $dir      = "services/{$service}";
        $path     = $file->storeAs($dir, $fileName, 'local');

        // Enregistrement DB
        $doc = Document::create([
            'uuid'       => $uuid,
            'title'      => $file->getClientOriginalName(),
            'file_path'  => $path,
            'service_id' => $service,
            'user_id'    => $request->user()->id,
            'md5'        => $md5,
            'status'     => 'pending',
            'ocr_status' => 'pending', // ✅ Ajout
        ]);

        // ✅ Lancer l'OCR automatiquement si c'est une image ou PDF
        if ($this->shouldProcessOcr($doc)) {
            try {
                ProcessOcr::dispatch($doc);
                Log::info("OCR queued for document #{$doc->id}");
            } catch (\Exception $e) {
                Log::error("Failed to queue OCR for document #{$doc->id}: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'data'    => $doc->only(['id', 'uuid', 'title', 'file_path', 'status', 'ocr_status']),
            'message' => 'Document enregistré' . ($this->shouldProcessOcr($doc) ? ' - OCR en cours' : '')
        ], 201);
    }

    public function batchStore(Request $request, string $service)
    {
        $request->validate([
            'files'   => 'required|array|max:10',
            'files.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $created = collect();
        $skipped = collect();

        foreach ($request->file('files') as $file) {
            $md5 = md5_file($file->getRealPath());

            // Vérifier les doublons
            $existingDoc = Document::where('md5', $md5)
                                   ->where('service_id', $service)
                                   ->first();

            if ($existingDoc) {
                $skipped->push([
                    'filename' => $file->getClientOriginalName(),
                    'reason'   => 'Fichier déjà existant',
                    'existing' => $existingDoc->only(['id', 'uuid', 'title'])
                ]);
                continue;
            }

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
                'md5'        => $md5,
                'status'     => 'pending',
                'ocr_status' => 'pending', // ✅ Ajout
            ]);

            // ✅ Lancer l'OCR pour chaque fichier
            if ($this->shouldProcessOcr($doc)) {
                try {
                    ProcessOcr::dispatch($doc);
                    Log::info("OCR queued for document #{$doc->id}");
                } catch (\Exception $e) {
                    Log::error("Failed to queue OCR for document #{$doc->id}: " . $e->getMessage());
                }
            }

            $created->push($doc->only(['id', 'uuid', 'title', 'file_path', 'status', 'ocr_status']));
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'created' => $created,
                'skipped' => $skipped,
            ],
            'message' => count($created) . ' document(s) créé(s), ' . count($skipped) . ' ignoré(s)'
        ], 201);
    }

    public function show(Request $request, string $service, string $id)
    {
        $doc = Document::where('service_id', $service)
                       ->where('id', $id)
                       ->firstOrFail();

        // URL de téléchargement signée valable 5 minutes
        $downloadUrl = URL::temporarySignedRoute(
            'documents.download',
            now()->addMinutes(5),
            ['service' => $service, 'id' => $id]
        );

        return response()->json([
            'success' => true,
            'data'    => [
                'document'     => $doc->only([
                    'id', 'uuid', 'title', 'status',
                    'ocr_text', 'ocr_status', 'ocr_processed_at', 'ocr_error', // ✅ Ajout
                    'created_at', 'updated_at'
                ]),
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

    public function update(Request $request, string $service, string $id)
    {
        $serviceModel = Service::findOrFail($service);

        if (!$request->user()->can('update', $serviceModel)) {
            abort(403, 'Vous devez être chef de service pour modifier un document');
        }

        $doc = Document::where('service_id', $service)
                       ->where('id', $id)
                       ->firstOrFail();

        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:1000',
            'status'      => 'sometimes|in:draft,pending,signed,archived',
        ]);

        $doc->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $doc->only(['id', 'title', 'description', 'status', 'updated_at']),
            'message' => 'Métadonnées mises à jour'
        ]);
    }

    public function destroy(Request $request, string $service, string $id)
    {
        $serviceModel = Service::findOrFail($service);
        $this->authorize('delete', $serviceModel);

        $doc = Document::where('service_id', $service)
                       ->where('id', $id)
                       ->firstOrFail();

        $doc->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document supprimé (soft-delete)'
        ], 200);
    }

    public function preview(Request $request, string $service, string $id)
    {
        Log::info('Preview request', ['service' => $service, 'id' => $id]);

        $doc = Document::where('service_id', $service)
                       ->where('id', $id)
                       ->firstOrFail();

        $path = storage_path('app/' . $doc->file_path);

        Log::info('File path', ['path' => $path, 'exists' => file_exists($path)]);

        if (!file_exists($path)) {
            Log::error('File not found', ['path' => $path]);
            abort(404, 'Fichier introuvable: ' . $doc->file_path);
        }

        $mimeType = mime_content_type($path);

        $supportedTypes = [
            'application/pdf',
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp'
        ];

        if (!in_array($mimeType, $supportedTypes)) {
            return response()->json([
                'success' => false,
                'message' => 'Type de fichier non supporté pour la preview. Téléchargez le fichier.',
                'mime_type' => $mimeType
            ], 415);
        }

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($doc->file_path) . '"'
        ]);
    }

    // ✅ NOUVELLE MÉTHODE: Déterminer si un document doit passer par l'OCR
    private function shouldProcessOcr(Document $document): bool
    {
        $ocrMimeTypes = [
            'image/png',
            'image/jpeg',
            'image/jpg',
            'application/pdf'
        ];

        $extension = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
        $ocrExtensions = ['png', 'jpg', 'jpeg', 'pdf'];

        return in_array($extension, $ocrExtensions);
    }
}
