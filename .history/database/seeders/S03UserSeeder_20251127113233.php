<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class S03UserSeeder extends Seeder
{
    public function run()
    {
        // Chef du service
        User::create([
            'name' => 'Chef Urbanisme',
            'email' => 'chef.urbanismes@mairie.sn',
            'password' => bcrypt('password'),
            'service_id' => 3, // s03
            'role' => 'chef',
        ]);

        // Agent
        User::create([
            'name' => 'Agent Urbanisme',
            'email' => 'agent.urbanismes@mairie.sn',
            'password' => bcrypt('password'),
            'service_id' => 3,
            'role' => 'agent',
        ]);
    }
}
