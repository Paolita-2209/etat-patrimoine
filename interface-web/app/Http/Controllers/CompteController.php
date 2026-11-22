<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Gestion des comptes : page réservée à l'administrateur.
 * Les données viennent d'auth-service (/api/comptes).
 */
class CompteController extends Controller
{
    private const MESSAGE_INTERDIT = 'Accès réservé à l’administrateur';
    private const MESSAGE_INDISPONIBLE = 'Service d’authentification indisponible';

    /**
     * Renvoie une redirection si la personne n'est pas connectée
     * ou n'est pas administrateur, sinon null.
     */
    private function refuser()
    {
        if (!session('token')) {
            return redirect('/login');
        }

        if (session('user.role') !== 'administrateur') {
            return redirect('/biens')->with('erreur', self::MESSAGE_INTERDIT);
        }

        return null;
    }

    /**
     * Appel d'auth-service avec le jeton de l'administrateur connecté.
     */
    private function appeler(string $methode, string $chemin, array $donnees = [])
    {
        return Http::acceptJson()
            ->withToken(session('token'))
            ->timeout(5)
            ->{$methode}(env('AUTH_SERVICE_URL') . '/api' . $chemin, $donnees);
    }

    /**
     * Gère les réponses 401 (jeton invalide) et 403 (pas administrateur).
     */
    private function erreurAcces($reponse)
    {
        if ($reponse->status() === 401) {
            session()->flush();
            return redirect('/login');
        }

        if ($reponse->status() === 403) {
            return redirect('/biens')->with('erreur', self::MESSAGE_INTERDIT);
        }

        return null;
    }

    /**
     * Message lisible pour une réponse 422 d'auth-service.
     */
    private function messageValidation($reponse): string
    {
        $erreurs = $reponse->json('errors') ?? [];

        if (isset($erreurs['email'])) {
            return 'Cette adresse e-mail est déjà utilisée';
        }

        return $reponse->json('message') ?? 'Données invalides';
    }

    public function index()
    {
        if ($r = $this->refuser()) {
            return $r;
        }

        try {
            $reponse = $this->appeler('get', '/comptes');
        } catch (\Exception $e) {
            return view('comptes', ['comptes' => [], 'erreur' => self::MESSAGE_INDISPONIBLE]);
        }

        if ($r = $this->erreurAcces($reponse)) {
            return $r;
        }

        if ($reponse->failed()) {
            return view('comptes', ['comptes' => [], 'erreur' => 'Impossible de charger les comptes']);
        }

        return view('comptes', ['comptes' => $reponse->json(), 'erreur' => null]);
    }

    public function create()
    {
        if ($r = $this->refuser()) {
            return $r;
        }

        return view('comptes-ajouter');
    }

    public function store(Request $request)
    {
        if ($r = $this->refuser()) {
            return $r;
        }

        $donnees = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'role' => 'required|in:administrateur,agent,responsable',
            'password' => 'required|string|min:8',
        ]);

        try {
            $reponse = $this->appeler('post', '/comptes', $donnees);
        } catch (\Exception $e) {
            return back()->withInput($request->except('password'))->with('erreur', self::MESSAGE_INDISPONIBLE);
        }

        if ($r = $this->erreurAcces($reponse)) {
            return $r;
        }

        if ($reponse->status() === 422) {
            return back()->withInput($request->except('password'))->with('erreur', $this->messageValidation($reponse));
        }

        if ($reponse->failed()) {
            return back()->withInput($request->except('password'))->with('erreur', 'Le compte n’a pas pu être créé');
        }

        return redirect('/comptes')->with('succes', 'Compte créé avec succès');
    }

    public function edit($id)
    {
        if ($r = $this->refuser()) {
            return $r;
        }

        try {
            $reponse = $this->appeler('get', '/comptes/' . $id);
        } catch (\Exception $e) {
            return redirect('/comptes')->with('erreur', self::MESSAGE_INDISPONIBLE);
        }

        if ($r = $this->erreurAcces($reponse)) {
            return $r;
        }

        if ($reponse->failed()) {
            return redirect('/comptes')->with('erreur', 'Compte introuvable');
        }

        return view('comptes-modifier', ['compte' => $reponse->json()]);
    }

    public function update(Request $request, $id)
    {
        if ($r = $this->refuser()) {
            return $r;
        }

        $donnees = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'role' => 'required|in:administrateur,agent,responsable',
            'password' => 'nullable|string|min:8',
        ]);

        // Mot de passe vide = on ne le change pas
        if (empty($donnees['password'])) {
            unset($donnees['password']);
        }

        try {
            $reponse = $this->appeler('put', '/comptes/' . $id, $donnees);
        } catch (\Exception $e) {
            return back()->withInput($request->except('password'))->with('erreur', self::MESSAGE_INDISPONIBLE);
        }

        if ($r = $this->erreurAcces($reponse)) {
            return $r;
        }

        if ($reponse->status() === 422) {
            return back()->withInput($request->except('password'))->with('erreur', $this->messageValidation($reponse));
        }

        if ($reponse->failed()) {
            return back()->withInput($request->except('password'))->with('erreur', 'Le compte n’a pas pu être modifié');
        }

        // Si l'administrateur modifie son propre compte, on met à jour la session
        if ((int) session('user.id') === (int) $id) {
            $compte = $reponse->json('compte');
            if (is_array($compte)) {
                session(['user' => array_merge(session('user'), $compte)]);
            }
        }

        return redirect('/comptes')->with('succes', 'Compte modifié avec succès');
    }

    public function changerStatut($id)
    {
        if ($r = $this->refuser()) {
            return $r;
        }

        try {
            $reponse = $this->appeler('patch', '/comptes/' . $id . '/statut');
        } catch (\Exception $e) {
            return redirect('/comptes')->with('erreur', self::MESSAGE_INDISPONIBLE);
        }

        if ($r = $this->erreurAcces($reponse)) {
            return $r;
        }

        if ($reponse->status() === 422) {
            return redirect('/comptes')->with('erreur', $reponse->json('message') ?? 'Action impossible');
        }

        if ($reponse->failed()) {
            return redirect('/comptes')->with('erreur', 'Le statut n’a pas pu être modifié');
        }

        return redirect('/comptes')->with('succes', $reponse->json('message') ?? 'Statut modifié');
    }
}
