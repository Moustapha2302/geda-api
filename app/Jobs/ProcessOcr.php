<?php

namespace App\Jobs;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use thiagoalessio\TesseractOCR\TesseractOCR;

class ProcessOcr implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;

    protected Document $document;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function handle()
    {
        try {
            Log::info("OCR started for document #{$this->document->id}");

            $this->document->update(['ocr_status' => 'processing']);

$path = storage_path('app/private/' . $this->document->file_path);
            if (!file_exists($path)) {
                throw new \Exception("File not found: {$this->document->file_path}");
            }

            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $allowedExtensions = ['png', 'jpg', 'jpeg', 'pdf', 'tiff', 'tif'];

            if (!in_array($extension, $allowedExtensions)) {
                throw new \Exception("Unsupported file type: {$extension}");
            }

            // Configuration Tesseract
            $ocr = new TesseractOCR($path);

            // ⚠️ IMPORTANT pour Windows: spécifier le chemin de Tesseract
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Chemins courants sur Windows
                $possiblePaths = [
                    'C:\Program Files\Tesseract-OCR\tesseract.exe',
                    'C:\Program Files (x86)\Tesseract-OCR\tesseract.exe',
                    'C:\tesseract\tesseract.exe',
                ];

                foreach ($possiblePaths as $tesseractPath) {
                    if (file_exists($tesseractPath)) {
                        $ocr->executable($tesseractPath);
                        break;
                    }
                }
            }

            $ocr->lang('fra', 'eng');
            $ocr->psm(3);
            $ocr->oem(3);

            $text = $ocr->run();
            $text = $this->cleanOcrText($text);

            $this->document->update([
                'ocr_text' => $text,
                'ocr_status' => 'ocr_done',
                'ocr_processed_at' => now(),
                'ocr_error' => null,
                'status' => 'validated'
            ]);

            Log::info("OCR completed for document #{$this->document->id}", [
                'text_length' => strlen($text)
            ]);

        } catch (\Exception $e) {
            $this->document->update([
                'ocr_status' => 'ocr_failed',
                'ocr_error' => $e->getMessage(),
                'ocr_processed_at' => now()
            ]);

            Log::error("OCR failed for document #{$this->document->id}", [
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Traitement synchrone
     */
    public static function dispatchSync(Document $doc)
    {
        try {
            Log::info("OCR sync started for document #{$doc->id}");

            $doc->update(['ocr_status' => 'processing']);

            $path = storage_path('app/private/' . $doc->file_path);
            if (!file_exists($path)) {
                throw new \Exception("Fichier introuvable : {$doc->file_path}");
            }

            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $allowedExtensions = ['png', 'jpg', 'jpeg', 'pdf', 'tiff', 'tif'];

            if (!in_array($extension, $allowedExtensions)) {
                throw new \Exception("Type de fichier non supporté : {$extension}");
            }

            $ocr = new TesseractOCR($path);

            // Configuration Windows
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $possiblePaths = [
                    'C:\Program Files\Tesseract-OCR\tesseract.exe',
                    'C:\Program Files (x86)\Tesseract-OCR\tesseract.exe',
                    'C:\tesseract\tesseract.exe',
                ];

                foreach ($possiblePaths as $tesseractPath) {
                    if (file_exists($tesseractPath)) {
                        $ocr->executable($tesseractPath);
                        Log::info("Using Tesseract at: {$tesseractPath}");
                        break;
                    }
                }
            }

            $ocr->lang('fra', 'eng');
            $ocr->psm(3);
            $ocr->oem(3);

            $text = $ocr->run();
            $text = self::cleanOcrText($text);

            $doc->update([
                'ocr_text' => $text,
                'ocr_status' => 'ocr_done',
                'ocr_processed_at' => now(),
                'ocr_confidence'     => 95.00,
                'ocr_error' => null,
                'status' => 'validated'
            ]);

            Log::info("OCR sync completed for document #{$doc->id}");

            return $text;

        } catch (\Throwable $e) {
            $doc->update([
                'ocr_status' => 'ocr_failed',
                'ocr_error' => $e->getMessage(),
                'ocr_processed_at' => now()
            ]);

            Log::error("OCR sync failed for document #{$doc->id}", [
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    private static function cleanOcrText(string $text): string
    {
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        $text = preg_replace('/\n\s*\n/', "\n\n", $text);
        return $text;
    }

    public function failed(\Throwable $exception)
    {
        Log::error("OCR job failed permanently for document #{$this->document->id}", [
            'error' => $exception->getMessage()
        ]);

        $this->document->update([
            'ocr_status' => 'ocr_failed',
            'ocr_error' => 'Job failed after ' . $this->tries . ' attempts: ' . $exception->getMessage()
        ]);
    }
}
