<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BienController;
use App\Http\Controllers\CompteController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/login'));

// Connexion
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

// Tableau de bord
Route::get('/dashboard', [DashboardController::class, 'index']);

// Biens
Route::get('/biens', [BienController::class, 'index']);
Route::get('/biens/ajouter', [BienController::class, 'create']);
Route::post('/biens', [BienController::class, 'store']);
Route::get('/biens/{id}/modifier', [BienController::class, 'edit']);
Route::put('/biens/{id}', [BienController::class, 'update']);
Route::delete('/biens/{id}', [BienController::class, 'destroy']);

// Comptes (administrateur uniquement)
Route::get('/comptes', [CompteController::class, 'index']);
Route::get('/comptes/ajouter', [CompteController::class, 'create']);
Route::post('/comptes', [CompteController::class, 'store']);
Route::get('/comptes/{id}/modifier', [CompteController::class, 'edit']);
Route::put('/comptes/{id}', [CompteController::class, 'update']);
Route::patch('/comptes/{id}/statut', [CompteController::class, 'changerStatut']);
