<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class S02UserSeeder extends Seeder
{
    public function run()
    {
        // Chef du service
        User::create([
            'name' => 'Chef Finances',
            'email' => 'chef.finances@mairie.sn',
            'password' => bcrypt('password'),
            'service_id' => 2, // s02
            'role' => 'chef',
        ]);

        // Agent
        User::create([
            'name' => 'Agent Finances',
            'email' => 'agent.finances@mairie.sn',
            'password' => bcrypt('password'),
            'service_id' => 2,
            'role' => 'agent',
        ]);
    }
}
