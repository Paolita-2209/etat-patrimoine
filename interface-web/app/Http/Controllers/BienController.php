<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BienController extends Controller
{
    private const MESSAGE_INTERDIT = 'Action non autorisée pour votre rôle';

    /**
     * Vrai si le rôle de l'utilisateur connecté fait partie des rôles donnés.
     */
    private function roleAutorise(array $roles): bool
    {
        return in_array(session('user.role'), $roles, true);
    }

    public function index()
    {
        if (!session('token')) {
            return redirect('/login');
        }

        try {
            $reponse = Http::acceptJson()
                ->withToken(session('token'))
                ->timeout(5)
                ->get(env('PATRIMOINE_SERVICE_URL') . '/api/biens');
        } catch (\Exception $e) {
            return view('biens', ['user' => session('user'), 'biens' => [], 'erreur' => 'Service patrimoine indisponible']);
        }

        if ($reponse->status() === 401) {
            session()->flush();
            return redirect('/login');
        }

        if ($reponse->failed()) {
            return view('biens', ['user' => session('user'), 'biens' => [], 'erreur' => 'Impossible de charger les biens']);
        }

        return view('biens', ['user' => session('user'), 'biens' => $reponse->json(), 'erreur' => null]);
    }

    public function create()
    {
        if (!session('token')) {
            return redirect('/login');
        }

        if (!$this->roleAutorise(['administrateur', 'agent'])) {
            return redirect('/biens')->with('erreur', self::MESSAGE_INTERDIT);
        }

        try {
            $reponse = Http::acceptJson()
                ->timeout(5)
                ->get(env('PATRIMOINE_SERVICE_URL') . '/api/categories');

            $categories = $reponse->successful() ? $reponse->json() : [];
        } catch (\Exception $e) {
            $categories = [];
        }

        return view('biens-ajouter', ['user' => session('user'), 'categories' => $categories, 'erreur' => session('erreur')]);
    }

    public function store(Request $request)
    {
        if (!session('token')) {
            return redirect('/login');
        }

        if (!$this->roleAutorise(['administrateur', 'agent'])) {
            return redirect('/biens')->with('erreur', self::MESSAGE_INTERDIT);
        }

        $donnees = $request->validate([
            'nom' => 'required|string|max:255',
            'categorie_id' => 'required|integer',
            'localisation' => 'required|string|max:255',
            'valeur' => 'required|numeric|min:0',
            'etat' => 'required|in:Bon,Moyen,Mauvais',
            'date_acquisition' => 'required|date',
        ]);

        try {
            $reponse = Http::acceptJson()
                ->withToken(session('token'))
                ->timeout(5)
                ->post(env('PATRIMOINE_SERVICE_URL') . '/api/biens', $donnees);
        } catch (\Exception $e) {
            return back()->withInput()->with('erreur', 'Service patrimoine indisponible');
        }

        if ($reponse->status() === 401) {
            session()->flush();
            return redirect('/login');
        }

        if ($reponse->status() === 403) {
            return redirect('/biens')->with('erreur', self::MESSAGE_INTERDIT);
        }

        if ($reponse->failed()) {
            return back()->withInput()->with('erreur', "Le bien n'a pas pu être enregistré");
        }

        return redirect('/biens')->with('succes', 'Bien enregistré avec succès');
    }

    public function edit($id)
    {
        if (!session('token')) {
            return redirect('/login');
        }

        if (!$this->roleAutorise(['administrateur', 'agent', 'responsable'])) {
            return redirect('/biens')->with('erreur', self::MESSAGE_INTERDIT);
        }

        try {
            $reponseBien = Http::acceptJson()
                ->withToken(session('token'))
                ->timeout(5)
                ->get(env('PATRIMOINE_SERVICE_URL') . '/api/biens/' . $id);

            $reponseCategories = Http::acceptJson()
                ->timeout(5)
                ->get(env('PATRIMOINE_SERVICE_URL') . '/api/categories');
        } catch (\Exception $e) {
            return redirect('/biens')->with('erreur', 'Service patrimoine indisponible');
        }

        if ($reponseBien->status() === 401) {
            session()->flush();
            return redirect('/login');
        }

        if ($reponseBien->failed()) {
            return redirect('/biens')->with('erreur', 'Bien introuvable');
        }

        $bien = $reponseBien->json();

        // Si le service renvoie le bien dans une enveloppe ("bien" ou "data")
        if (isset($bien['bien'])) {
            $bien = $bien['bien'];
        } elseif (isset($bien['data'])) {
            $bien = $bien['data'];
        }

        $categories = $reponseCategories->successful() ? $reponseCategories->json() : [];

        return view('biens-modifier', [
            'user' => session('user'),
            'bien' => $bien,
            'categories' => $categories,
            'erreur' => session('erreur'),
        ]);
    }

    public function update(Request $request, $id)
    {
        if (!session('token')) {
            return redirect('/login');
        }

        if (!$this->roleAutorise(['administrateur', 'agent', 'responsable'])) {
            return redirect('/biens')->with('erreur', self::MESSAGE_INTERDIT);
        }

        $donnees = $request->validate([
            'nom' => 'required|string|max:255',
            'categorie_id' => 'required|integer',
            'localisation' => 'required|string|max:255',
            'valeur' => 'required|numeric|min:0',
            'etat' => 'required|in:Bon,Moyen,Mauvais',
            'date_acquisition' => 'required|date',
        ]);

        try {
            $reponse = Http::acceptJson()
                ->withToken(session('token'))
                ->timeout(5)
                ->put(env('PATRIMOINE_SERVICE_URL') . '/api/biens/' . $id, $donnees);
        } catch (\Exception $e) {
            return back()->withInput()->with('erreur', 'Service patrimoine indisponible');
        }

        if ($reponse->status() === 401) {
            session()->flush();
            return redirect('/login');
        }

        if ($reponse->status() === 403) {
            return redirect('/biens')->with('erreur', self::MESSAGE_INTERDIT);
        }

        if ($reponse->failed()) {
            return back()->withInput()->with('erreur', "Le bien n'a pas pu être modifié");
        }

        return redirect('/biens')->with('succes', 'Bien modifié avec succès');
    }

    public function destroy($id)
    {
        if (!session('token')) {
            return redirect('/login');
        }

        if (!$this->roleAutorise(['administrateur'])) {
            return redirect('/biens')->with('erreur', self::MESSAGE_INTERDIT);
        }

        try {
            $reponse = Http::acceptJson()
                ->withToken(session('token'))
                ->timeout(5)
                ->delete(env('PATRIMOINE_SERVICE_URL') . '/api/biens/' . $id);
        } catch (\Exception $e) {
            return redirect('/biens')->with('erreur', 'Service patrimoine indisponible');
        }

        if ($reponse->status() === 401) {
            session()->flush();
            return redirect('/login');
        }

        if ($reponse->status() === 403) {
            return redirect('/biens')->with('erreur', self::MESSAGE_INTERDIT);
        }

        if ($reponse->failed()) {
            return redirect('/biens')->with('erreur', "Le bien n'a pas pu être supprimé");
        }

        return redirect('/biens')->with('succes', 'Bien supprimé');
    }
}