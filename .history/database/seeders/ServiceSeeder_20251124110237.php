<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    $services = [
        ['code' => 's01', 'name' => 'État-Civil'],
        ['code' => 's02', 'name' => 'Finances'],
        ['code' => 's03', 'name' => 'Urbanisme'],
        ['code' => 's04', 'name' => 'Ressources Humaines'],
        ['code' => 's05', 'name' => 'Communication'],
        ['code' => 's06', 'name' => 'Services Techniques Communnaux'],
        ['code' => 's07', 'name' => 'Direction Planification & CT'],
        ['code' => 's08', 'name' => 'Cellule Partenariat & Coopération'],
        ['code' => 's09', 'name' => 'Cellule Juridique & Contentieux'],
        ['code' => 's10', 'name' => 'Cellule Informatique'],
        ['code' => 's11', 'name' => 'Secrétariat Général'],
        ['code' => 's12', 'name' => 'Cabinet du Maire'],
        ['code' => 's13', 'name' => 'Archives Municipales'],
    ];
    foreach ($services as $s) {
        Service::create($s);
    }
}
