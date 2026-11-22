<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $reponse = Http::acceptJson()
                ->timeout(5)
                ->post(env('AUTH_SERVICE_URL') . '/api/login', $data);
        } catch (\Exception $e) {
            return back()->withErrors(['email' => "Service d'authentification indisponible"]);
        }

        // Compte désactivé : on affiche le message envoyé par auth-service
        if ($reponse->status() === 403) {
            return back()->withErrors(['email' => $reponse->json('message') ?? 'Compte désactivé'])->withInput();
        }

        if ($reponse->failed()) {
            return back()->withErrors(['email' => 'Identifiants invalides'])->withInput();
        }

        session([
            'token' => $reponse->json('token'),
            'user' => $reponse->json('user'),
        ]);

        return redirect('/dashboard');
    }

    public function logout()
    {
        session()->flush();

        return redirect('/login');
    }
}
