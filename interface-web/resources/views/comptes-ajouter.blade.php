@extends('layouts.app')

@section('titre', 'Créer un compte - Patrimoine')

@push('styles')
<style>
    .formulaire { max-width: 800px; }
    .grille { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px 22px; margin-bottom: 22px; }
    .boutons { display: flex; gap: 12px; }
    .aide { font-size: 12px; color: #5b6676; margin-top: 6px; }
    @media (max-width: 700px) { .grille { grid-template-columns: 1fr; } }
</style>
@endpush

@section('contenu')
    <h1>Créer un compte</h1>
    <p class="sub">Renseignez les informations du nouveau compte.</p>

    @if ($errors->any())
        <div class="alerte err">{{ $errors->first() }}</div>
    @endif

    @if (session('erreur'))
        <div class="alerte err">{{ session('erreur') }}</div>
    @endif

    <form method="POST" action="/comptes" class="card formulaire">
        @csrf

        <div class="grille">
            <div>
                <label for="name">Nom</label>
                <input class="in" type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Ex. Agent – Lomé" required>
            </div>

            <div>
                <label for="email">Adresse e-mail</label>
                <input class="in" type="email" id="email" name="email" value="{{ old('email') }}" placeholder="agent@exemple.tg" required>
            </div>

            <div>
                <label for="role">Rôle</label>
                <select class="in" id="role" name="role" required>
                    <option value="">Choisir</option>
                    @foreach (['administrateur' => 'Administrateur', 'agent' => 'Agent du patrimoine', 'responsable' => 'Responsable'] as $valeur => $libelle)
                        <option value="{{ $valeur }}" {{ old('role') === $valeur ? 'selected' : '' }}>{{ $libelle }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="password">Mot de passe</label>
                <input class="in" type="password" id="password" name="password" minlength="8" placeholder="8 caractères minimum" required>
            </div>
        </div>

        <div class="boutons">
            <button type="submit" class="btn">Enregistrer</button>
            <a href="/comptes" class="btn btn2">Annuler</a>
        </div>
    </form>
@endsection
