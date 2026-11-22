<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        if (!session('token')) {
            return redirect('/login');
        }

        try {
            $reponse = Http::acceptJson()
                ->withToken(session('token'))
                ->timeout(5)
                ->get(env('PATRIMOINE_SERVICE_URL') . '/api/statistiques');
        } catch (\Exception $e) {
            return view('dashboard', [
                'user' => session('user'),
                'stats' => null,
                'erreur' => 'Service patrimoine indisponible',
            ]);
        }

        if ($reponse->status() === 401) {
            session()->flush();
            return redirect('/login');
        }

        if ($reponse->failed()) {
            return view('dashboard', [
                'user' => session('user'),
                'stats' => null,
                'erreur' => 'Impossible de charger les statistiques',
            ]);
        }

        // La page lit : total_biens, valeur_totale, par_etat, par_categorie
        return view('dashboard', [
            'user' => session('user'),
            'stats' => $reponse->json(),
            'erreur' => null,
        ]);
    }
}