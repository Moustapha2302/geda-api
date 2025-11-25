<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class S01UserSeeder extends Seeder
{
    public function run()
    {
        // Chef du service
        User::create([
            'name' => 'Chef État-Civil',
            'email' => 'chef.etatcivil@mairie.sn',
            'password' => bcrypt('password'),
            'service_id' => 1, // s01
            'role' => 'chef',
        ]);

        // Agent
        User::create([
            'name' => 'Agent État-Civil',
            'email' => 'agent.etatcivil@mairie.sn',
            'password' => bcrypt('password'),
            'service_id' => 1,
            'role' => 'agent',
        ]);
    }
}
