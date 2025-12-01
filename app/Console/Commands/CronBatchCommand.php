<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CronBatchCommand extends Command
{
    protected $signature = 'cron:batch {service=s02}';
    protected $description = 'Lance les 3批次 CRON pour un service';

    public function handle()
    {
        $service = $this->argument('service');
        $base = config('app.url') . "/api/{$service}/cron";

        // 1. Auto-transfer J+7
        $this->info("1. Auto-transfer J+7...");
        $r1 = Http::post("{$base}/auto-transfer-j7");
        $this->line($r1->json('message') . ' - ' . $r1->json('transferred'));

        // 2. Alertes 6 mois
        $this->info("2. Alert-before-end...");
        $r2 = Http::post("{$base}/alert-before-end");
        $this->line($r2->json('message') . ' - ' . $r2->json('alerted'));

        // 3. OCR pending
        $this->info("3. OCR pending...");
        $r3 = Http::post("{$base}/ocr-pending", ['limit' => 50, 'async' => true]);
        $this->line($r3->json('message') . ' - ' . $r3->json('processed'));

        $this->info('Batch terminé.');
    }
}
