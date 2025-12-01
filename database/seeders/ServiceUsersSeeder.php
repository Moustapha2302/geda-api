<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\User;

class ServiceUsersSeeder extends Seeder
{
    public function run()
    {
        $services = [
            ['code' => 'S01', 'name' => 'Etat Civil', 'slug' => 's01'],
            ['code' => 'S02', 'name' => 'Finances', 'slug' => 's02'],
            ['code' => 'S03', 'name' => 'Urbanisme', 'slug' => 's03'],
            ['code' => 'S04', 'name' => 'Ressources Humaines', 'slug' => 's04'],
            ['code' => 'S05', 'name' => 'Communication', 'slug' => 's05'],
            ['code' => 'S06', 'name' => 'Services Techniques Communaux', 'slug' => 's06'],
            ['code' => 'S07', 'name' => 'Direction Planification & DPCT', 'slug' => 's07'],
            ['code' => 'S08', 'name' => 'Cellule Partenariat & Coopération', 'slug' => 's08'],
            ['code' => 'S09', 'name' => 'Cellule Juridique & Contentieux', 'slug' => 's09'],
            ['code' => 'S10', 'name' => 'Cellule Informatique', 'slug' => 's10'],
            ['code' => 'S11', 'name' => 'Secrétariat Général', 'slug' => 's11'],
            ['code' => 'S12', 'name' => 'Cabinet du Maire', 'slug' => 's12'],
            ['code' => 'S13', 'name' => 'Archives Municipales', 'slug' => 's13'],
        ];

        foreach ($services as $svc) {
            $s = Service::firstOrCreate(['code' => $svc['code']], $svc);

            // Agent
            User::firstOrCreate(['email' => "agent.{$svc['slug']}@mairie.sn"], [
                'name' => "Agent {$svc['name']}",
                'password' => bcrypt('password'),
                'service_id' => $s->id,
                'role' => 'agent',
            ]);

            // Chef
            User::firstOrCreate(['email' => "chef.{$svc['slug']}@mairie.sn"], [
                'name' => "Chef {$svc['name']}",
                'password' => bcrypt('password'),
                'service_id' => $s->id,
                'role' => 'chef',
            ]);
        }
    }
}
