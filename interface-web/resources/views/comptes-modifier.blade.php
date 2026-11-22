@extends('layouts.app')

@section('titre', 'Modifier un compte - Patrimoine')

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
    <h1>Modifier un compte</h1>
    <p class="sub">Modifiez les informations du compte, puis enregistrez.</p>

    @if ($errors->any())
        <div class="alerte err">{{ $errors->first() }}</div>
    @endif

    @if (session('erreur'))
        <div class="alerte err">{{ session('erreur') }}</div>
    @endif

    <form method="POST" action="/comptes/{{ $compte['id'] }}" class="card formulaire">
        @csrf
        @method('PUT')

        <div class="grille">
            <div>
                <label for="name">Nom</label>
                <input class="in" type="text" id="name" name="name" value="{{ old('name', $compte['name']) }}" required>
            </div>

            <div>
                <label for="email">Adresse e-mail</label>
                <input class="in" type="email" id="email" name="email" value="{{ old('email', $compte['email']) }}" required>
            </div>

            <div>
                <label for="role">Rôle</label>
                <select class="in" id="role" name="role" required>
                    @foreach (['administrateur' => 'Administrateur', 'agent' => 'Agent du patrimoine', 'responsable' => 'Responsable'] as $valeur => $libelle)
                        <option value="{{ $valeur }}" {{ old('role', $compte['role'] ?? '') === $valeur ? 'selected' : '' }}>{{ $libelle }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="password">Nouveau mot de passe</label>
                <input class="in" type="password" id="password" name="password" minlength="8" placeholder="Laisser vide pour ne pas le changer">
                <div class="aide">8 caractères minimum si vous le changez.</div>
            </div>
        </div>

        <div class="boutons">
            <button type="submit" class="btn">Enregistrer</button>
            <a href="/comptes" class="btn btn2">Annuler</a>
        </div>
    </form>
@endsection
