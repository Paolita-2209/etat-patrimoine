<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BienController;
use App\Http\Controllers\StatistiqueController;
use App\Http\Middleware\VerifierRole;
use App\Http\Middleware\VerifierToken;
use App\Models\Categorie;

// Afficher les catégories (accessible sans jeton)
Route::get('/categories', function () {
    return Categorie::all();
});

// Routes protégées : jeton obligatoire
Route::middleware(VerifierToken::class)->group(function () {

    // Lecture : tous les rôles
    Route::get('/biens', [BienController::class, 'index']);
    Route::get('/biens/{bien}', [BienController::class, 'show']);
    Route::get('/statistiques', [StatistiqueController::class, 'index']);

    // Ajout : administrateur et agent
    Route::post('/biens', [BienController::class, 'store'])
        ->middleware(VerifierRole::class . ':administrateur,agent');

    // Modification : administrateur, agent et responsable
    Route::match(['put', 'patch'], '/biens/{bien}', [BienController::class, 'update'])
        ->middleware(VerifierRole::class . ':administrateur,agent,responsable');

    // Suppression : administrateur uniquement
    Route::delete('/biens/{bien}', [BienController::class, 'destroy'])
        ->middleware(VerifierRole::class . ':administrateur');
});