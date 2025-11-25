<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GlobalUserSeeder extends Seeder
{
    public function run()
    {
        // Secrétaire Général
        User::create([
            'name' => 'Secrétaire Général',
            'email' => 'sg@mairie.sn',
            'password' => Hash::make('password'),
            'service_id' => null, // pas rattaché à un service
            'role' => 'sg',
        ]);

        // Maire
        User::create([
            'name' => 'Maire',
            'email' => 'maire@mairie.sn',
            'password' => Hash::make('password'),
            'service_id' => null,
            'role' => 'maire',
        ]);
    }
}
