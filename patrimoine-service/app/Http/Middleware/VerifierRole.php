<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifierRole
{
    /**
     * Autorise la requête seulement si le rôle de l'utilisateur
     * (récupéré par VerifierToken) fait partie des rôles demandés.
     *
     * Exemple : VerifierRole::class . ':administrateur,agent'
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $utilisateur = $request->attributes->get('utilisateur');
        $role = $utilisateur['role'] ?? null;

        if (!$role || !in_array($role, $roles, true)) {
            return response()->json(['message' => 'Accès refusé : rôle insuffisant'], 403);
        }

        return $next($request);
    }
}