<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EstAdministrateur
{
    /**
     * Laisse passer uniquement l'administrateur connecté.
     * À utiliser après auth:sanctum.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role !== 'administrateur') {
            return response()->json(['message' => 'Accès réservé à l’administrateur'], 403);
        }

        return $next($request);
    }
}
