<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Archive;
use Carbon\Carbon;

class ArchiveSeeder extends Seeder
{
    public function run()
    {
        // Archives intermédiaires (J+7 passé)
        Archive::create([
            'title' => 'Contrat fournisseur 2024',
            'status' => 'intermediate',
            'arrived_at' => Carbon::now()->subDays(10), // Il y a 10 jours
        ]);

        Archive::create([
            'title' => 'Facture électricité Q1 2024',
            'status' => 'intermediate',
            'arrived_at' => Carbon::now()->subDays(8),
        ]);

        // Archive déjà en final
        Archive::create([
            'title' => 'Rapport annuel 2023',
            'status' => 'final',
            'arrived_at' => Carbon::now()->subMonths(6),
            'moved_at' => Carbon::now()->subMonths(5),
        ]);

        // Archive récente (pas encore J+7)
        Archive::create([
            'title' => 'Commande matériel bureau',
            'status' => 'intermediate',
            'arrived_at' => Carbon::now()->subDays(3),
        ]);

        // Archive proche de l'échéance (dans les 6 mois)
        Archive::create([
            'title' => 'Convention partenariat',
            'status' => 'intermediate',
            'arrived_at' => Carbon::now()->addMonths(4),
        ]);
    }
}
