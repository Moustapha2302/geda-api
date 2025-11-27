<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Jobs\ProcessOcr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CronController extends Controller
{
    /**
     * Traiter les documents en attente d'OCR pour un service
     * POST /{service}/cron/ocr-pending
     */
    public function ocrPending(Request $request, string $service)
    {
        $validated = $request->validate([
            'limit' => 'sometimes|integer|min:1|max:100',
            'async' => 'sometimes|boolean'
        ]);

        $limit = $validated['limit'] ?? 10;
        $async = $validated['async'] ?? false;

        Log::info("OCR cron triggered", [
            'service' => $service,
            'limit' => $limit,
            'async' => $async
        ]);

        try {
            // Récupérer les documents en attente (adapté à votre structure)
            $docs = Document::where('service_id', (int)$service)
                            ->where(function($query) {
                                $query->whereNull('ocr_text')
                                      ->orWhere('ocr_status', 'pending');
                            })
                            ->where('status', 'pending')
                            ->whereIn('file_path', function($q) {
                                // Uniquement les fichiers PDF et images
                                $q->selectRaw('file_path from documents where file_path like "%.pdf"
                                    or file_path like "%.png"
                                    or file_path like "%.jpg"
                                    or file_path like "%.jpeg"');
                            })
                            ->limit($limit)
                            ->get();

            if ($docs->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Aucun document en attente',
                    'processed' => 0,
                    'data' => [
                        'success' => [],
                        'failed' => []
                    ]
                ]);
            }

            $success = [];
            $failed = [];

            foreach ($docs as $doc) {
                try {
                    if ($async) {
                        ProcessOcr::dispatch($doc);
                        $success[] = [
                            'id' => $doc->id,
                            'uuid' => $doc->uuid,
                            'title' => $doc->title,
                            'ocr_status' => 'queued'
                        ];
                    } else {
                        set_time_limit(120);
                        ProcessOcr::dispatchSync($doc);
                        $doc->refresh();

                        $success[] = [
                            'id' => $doc->id,
                            'uuid' => $doc->uuid,
                            'title' => $doc->title,
                            'ocr_status' => $doc->ocr_status,
                            'text_length' => strlen($doc->ocr_text ?? ''),
                        ];
                    }
                } catch (\Throwable $e) {
                    $failed[] = [
                        'id' => $doc->id,
                        'uuid' => $doc->uuid,
                        'title' => $doc->title,
                        'error' => $e->getMessage()
                    ];

                    Log::error("OCR failed", [
                        'document_id' => $doc->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Traitement OCR terminé',
                'processed' => $docs->count(),
                'data' => [
                    'success' => $success,
                    'failed' => $failed
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("OCR cron error", [
                'service' => $service,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du traitement OCR',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Statistiques OCR d'un service
     * GET /{service}/cron/ocr-stats
     */
    public function ocrStats(string $service)
    {
        try {
            $serviceId = (int)$service;

            $stats = [
                'total' => Document::where('service_id', $serviceId)->count(),
                'pending' => Document::where('service_id', $serviceId)
                                     ->where('ocr_status', 'pending')
                                     ->count(),
                'processing' => Document::where('service_id', $serviceId)
                                        ->where('ocr_status', 'processing')
                                        ->count(),
                'done' => Document::where('service_id', $serviceId)
                                  ->where('ocr_status', 'ocr_done')
                                  ->count(),
                'failed' => Document::where('service_id', $serviceId)
                                    ->where('ocr_status', 'ocr_failed')
                                    ->count(),
            ];

            $stats['completion_rate'] = $stats['total'] > 0
                ? round(($stats['done'] / $stats['total']) * 100, 2)
                : 0;

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Forcer le retraitement d'un document
     * POST /{service}/cron/ocr-force/{documentId}
     */
    public function ocrForce(string $service, int $documentId)
    {
        try {
            $doc = Document::where('service_id', (int)$service)
                           ->where('id', $documentId)
                           ->firstOrFail();

            set_time_limit(180);

            $doc->update([
                'ocr_status' => 'pending',
                'ocr_text' => null,
                'ocr_error' => null,
                'ocr_processed_at' => null
            ]);

            ProcessOcr::dispatchSync($doc);
            $doc->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Document retraité avec succès',
                'data' => [
                    'id' => $doc->id,
                    'uuid' => $doc->uuid,
                    'title' => $doc->title,
                    'ocr_status' => $doc->ocr_status,
                    'text_preview' => mb_substr($doc->ocr_text ?? '', 0, 200),
                    'text_length' => strlen($doc->ocr_text ?? ''),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Force OCR failed", [
                'document_id' => $documentId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Échec du retraitement',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
