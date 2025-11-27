<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Jobs\ProcessOcr;
use Illuminate\Console\Command;

class OcrProcessCommand extends Command
{
    protected $signature = 'ocr:process
                            {--service= : ID du service à traiter}
                            {--limit=10 : Nombre de documents à traiter}
                            {--async : Utiliser la queue (asynchrone)}
                            {--retry-failed : Réessayer les documents échoués}';

    protected $description = 'Traiter les documents en attente d\'OCR';

    public function handle()
    {
        $serviceId = $this->option('service');
        $limit = (int) $this->option('limit');
        $async = $this->option('async');
        $retryFailed = $this->option('retry-failed');

        $this->info("🔍 Recherche de documents à traiter...");

        // Construction de la requête
        $query = Document::query();

        if ($serviceId) {
            $query->where('service_id', $serviceId);
            $this->info("   Service: {$serviceId}");
        }

        if ($retryFailed) {
            $query->where('ocr_status', 'ocr_failed');
            $this->info("   Mode: Réessayer les échecs");
        } else {
            $query->where(function($q) {
                $q->whereNull('ocr_text')
                  ->orWhere('ocr_status', 'pending');
            })->where('status', 'pending');
        }

        $documents = $query->limit($limit)->get();

        if ($documents->isEmpty()) {
            $this->warn("❌ Aucun document à traiter");
            return 0;
        }

        $this->info("📄 {$documents->count()} document(s) trouvé(s)");
        $this->newLine();

        $bar = $this->output->createProgressBar($documents->count());
        $bar->start();

        $success = 0;
        $failed = 0;

        foreach ($documents as $doc) {
            try {
                if ($async) {
                    // Queue asynchrone
                    ProcessOcr::dispatch($doc);
                    $this->line(" ⏳ Document #{$doc->id} mis en queue");
                } else {
                    // Traitement synchrone
                    ProcessOcr::dispatchSync($doc);
                    $doc->refresh();

                    if ($doc->ocr_status === 'ocr_done') {
                        $success++;
                        $this->line(" ✅ Document #{$doc->id} traité (" . strlen($doc->ocr_text) . " caractères)");
                    } else {
                        $failed++;
                        $this->line(" ❌ Document #{$doc->id} échoué: {$doc->ocr_error}");
                    }
                }

                $bar->advance();

            } catch (\Exception $e) {
                $failed++;
                $this->error(" ❌ Erreur sur document #{$doc->id}: " . $e->getMessage());
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);

        // Résumé
        $this->info("📊 RÉSUMÉ");
        $this->table(
            ['Métrique', 'Valeur'],
            [
                ['Total traité', $documents->count()],
                ['Succès', $success],
                ['Échecs', $failed],
                ['Mode', $async ? 'Asynchrone (queue)' : 'Synchrone'],
            ]
        );

        return 0;
    }
}
