<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Gestion des comptes : réservée à l'administrateur
 * (voir le middleware EstAdministrateur dans routes/api.php).
 */
class CompteController extends Controller
{
    // LISTE DES COMPTES
    public function index()
    {
        return User::orderBy('id')->get(['id', 'name', 'email', 'role', 'actif']);
    }

    // UN COMPTE
    public function show(User $compte)
    {
        return $compte->only(['id', 'name', 'email', 'role', 'actif']);
    }

    // CRÉER UN COMPTE
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => ['required', Rule::in(User::ROLES)],
            'password' => 'required|string|min:8',
        ]);

        $compte = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => $data['password'], // haché automatiquement (cast "hashed")
            'actif' => true,
        ]);

        return response()->json([
            'message' => 'Compte créé avec succès',
            'compte' => $compte->only(['id', 'name', 'email', 'role', 'actif']),
        ], 201);
    }

    // MODIFIER UN COMPTE (le mot de passe est facultatif)
    public function update(Request $request, User $compte)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($compte->id)],
            'role' => ['required', Rule::in(User::ROLES)],
            'password' => 'nullable|string|min:8',
        ]);

        // Évite de se retirer soi-même les droits d'administrateur
        if ($request->user()->id === $compte->id && $data['role'] !== 'administrateur') {
            return response()->json([
                'message' => 'Vous ne pouvez pas retirer votre propre rôle d’administrateur',
            ], 422);
        }

        $compte->name = $data['name'];
        $compte->email = $data['email'];
        $compte->role = $data['role'];

        if (!empty($data['password'])) {
            $compte->password = $data['password'];
        }

        $compte->save();

        return response()->json([
            'message' => 'Compte modifié avec succès',
            'compte' => $compte->only(['id', 'name', 'email', 'role', 'actif']),
        ]);
    }

    // ACTIVER / DÉSACTIVER UN COMPTE
    public function changerStatut(Request $request, User $compte)
    {
        if ($request->user()->id === $compte->id) {
            return response()->json([
                'message' => 'Vous ne pouvez pas désactiver votre propre compte',
            ], 422);
        }

        $compte->actif = !$compte->actif;
        $compte->save();

        // Un compte désactivé est déconnecté partout : ses jetons sont supprimés
        if (!$compte->actif) {
            $compte->tokens()->delete();
        }

        return response()->json([
            'message' => $compte->actif ? 'Compte activé' : 'Compte désactivé',
            'compte' => $compte->only(['id', 'name', 'email', 'role', 'actif']),
        ]);
    }
}
