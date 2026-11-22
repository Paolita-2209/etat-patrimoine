<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class VerifierToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Jeton manquant'], 401);
        }

        try {
            $reponse = Http::withToken($token)
                ->acceptJson()
                ->timeout(5)
                ->get(env('AUTH_SERVICE_URL', 'http://127.0.0.1:8000') . '/api/user');
        } catch (\Exception $e) {
            return response()->json(['message' => "Service d'authentification indisponible"], 503);
        }

        if ($reponse->failed()) {
            return response()->json(['message' => 'Jeton invalide'], 401);
        }

        $request->attributes->set('utilisateur', $reponse->json());

        return $next($request);
    }
}