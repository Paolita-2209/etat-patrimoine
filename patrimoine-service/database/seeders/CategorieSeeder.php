<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorieSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Bâtiment', 'Terrain', 'Véhicule', 'Équipement'] as $nom) {
            DB::table('categories')->updateOrInsert(['nom' => $nom], ['nom' => $nom]);
        }
    }
}