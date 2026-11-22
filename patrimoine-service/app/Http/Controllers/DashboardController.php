<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Categorie;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Nombre total de biens
        $totalBiens = Bien::count();

        // Valeur totale du patrimoine
        $valeurTotale = Bien::sum('valeur');

        // Nombre de biens par catégorie
        $biensParCategorie = Categorie::withCount('biens')->get();

        // Nombre de biens par état
        $biensParEtat = Bien::select(
            'etat',
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('etat')
        ->get();

        return response()->json([
            'total_biens' => $totalBiens,
            'valeur_totale' => $valeurTotale,
            'biens_par_categorie' => $biensParCategorie,
            'biens_par_etat' => $biensParEtat,
        ]);
    }
}
