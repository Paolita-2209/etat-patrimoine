<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use Illuminate\Http\Request;

class BienController extends Controller
{
    private function regles(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'categorie_id' => 'required|integer|exists:categories,id',
            'localisation' => 'required|string|max:255',
            'valeur' => 'required|numeric|min:0',
            'etat' => 'required|in:Bon,Moyen,Mauvais',
            'date_acquisition' => 'required|date',
        ];
    }

    public function index()
    {
        return Bien::with('categorie')->orderBy('id')->get();
    }

    public function show(Bien $bien)
    {
        return $bien->load('categorie');
    }

    public function store(Request $request)
    {
        $donnees = $request->validate($this->regles());

        $bien = Bien::create($donnees);

        return response()->json([
            'message' => 'Bien enregistré avec succès',
            'bien' => $bien->load('categorie'),
        ], 201);
    }

    public function update(Request $request, Bien $bien)
    {
        $donnees = $request->validate($this->regles());

        $bien->update($donnees);

        return response()->json([
            'message' => 'Bien modifié avec succès',
            'bien' => $bien->load('categorie'),
        ]);
    }

    public function destroy(Bien $bien)
    {
        $bien->delete();

        return response()->json(['message' => 'Bien supprimé avec succès']);
    }
}