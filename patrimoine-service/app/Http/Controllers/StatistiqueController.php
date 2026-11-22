<?php

namespace App\Http\Controllers;

use App\Models\Bien;

class StatistiqueController extends Controller
{
    public function index()
    {
        $biens = Bien::with('categorie')->get();

        return response()->json([
            'total_biens' => $biens->count(),
            'valeur_totale' => $biens->sum('valeur'),
            'par_etat' => $biens->groupBy('etat')->map->count(),
            'par_categorie' => $biens
                ->groupBy(fn ($bien) => $bien->categorie?->nom ?? 'Sans catégorie')
                ->map(fn ($groupe) => [
                    'total' => $groupe->count(),
                    'valeur' => $groupe->sum('valeur'),
                ]),
        ]);
    }
}