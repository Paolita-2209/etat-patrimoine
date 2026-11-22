<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompteController;
use App\Http\Middleware\EstAdministrateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Connexion (la création de comptes se fait par l'administrateur, voir plus bas)
Route::post('/login', [AuthController::class, 'login']);

Route::get('/test', function () {
    return response()->json([
        'message' => 'Le service Auth fonctionne correctement !'
    ]);
});

// Routes réservées aux utilisateurs connectés
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Gestion des comptes : administrateur uniquement
    Route::middleware(EstAdministrateur::class)->group(function () {
        Route::get('/comptes', [CompteController::class, 'index']);
        Route::post('/comptes', [CompteController::class, 'store']);
        Route::get('/comptes/{compte}', [CompteController::class, 'show']);
        Route::put('/comptes/{compte}', [CompteController::class, 'update']);
        Route::patch('/comptes/{compte}/statut', [CompteController::class, 'changerStatut']);
    });
});
