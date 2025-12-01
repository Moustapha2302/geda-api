<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
   // database/seeders/RoleSeeder.php
public function run()
{
    \Spatie\Permission\Models\Role::create(['name' => 'ar']);
    \Spatie\Permission\Models\Role::create(['name' => 'a']);
}
}
