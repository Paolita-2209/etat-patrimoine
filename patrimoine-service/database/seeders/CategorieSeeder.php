<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorieSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            ['nom' => 'Bâtiment'],
            ['nom' => 'Terrain'],
            ['nom' => 'Véhicule'],
            ['nom' => 'Équipement'],
        ]);
    }
}