<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un bien - Patrimoine</title>
    <style>
        body { font-family: sans-serif; background: #f3f4f6; margin: 0; }
        header { background: #1d4ed8; color: #fff; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        header a { color: #fff; margin-right: 1rem; text-decoration: none; }
        header button { padding: .4rem .8rem; cursor: pointer; }
        main { padding: 2rem; max-width: 600px; margin: auto; }
        form.formulaire { background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        label { display: block; font-weight: bold; margin-top: 1rem; }
        input, select { width: 100%; padding: .6rem; margin-top: .3rem; box-sizing: border-box; }
        .boutons { display: flex; gap: .8rem; margin-top: 1.5rem; }
        .btn-enregistrer { background: #1d4ed8; color: #fff; border: 0; padding: .7rem 1.2rem; border-radius: 5px; cursor: pointer; }
        .btn-annuler { background: #e5e7eb; color: #111827; text-decoration: none; padding: .7rem 1.2rem; border-radius: 5px; }
        .erreur { background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 6px; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <header>
        <div>
            <a href="/dashboard">Tableau de bord</a>
            <a href="/biens"><strong>Biens</strong></a>
        </div>
        <div>
            {{ $user['name'] }} ({{ $user['role'] ?? 'sans rôle' }})
            <form method="POST" action="/logout" style="display:inline">
                @csrf
                <button type="submit">Se déconnecter</button>
            </form>
        </div>
    </header>

    <main>
        <h1>Ajouter un bien</h1>

        @if ($erreur)
            <div class="erreur">{{ $erreur }}</div>
        @endif

        @if ($errors->any())
            <div class="erreur">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="/biens" class="formulaire">
            @csrf

            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required>

            <label for="categorie_id">Catégorie</label>
            <select id="categorie_id" name="categorie_id" required>
                <option value="">-- Choisir une catégorie --</option>
                @foreach ($categories as $categorie)
                    <option value="{{ $categorie['id'] }}"
                        {{ (string) old('categorie_id') === (string) $categorie['id'] ? 'selected' : '' }}>
                        {{ $categorie['nom'] }}
                    </option>
                @endforeach
            </select>

            <label for="localisation">Localisation</label>
            <input type="text" id="localisation" name="localisation" value="{{ old('localisation') }}" required>

            <label for="valeur">Valeur (FCFA)</label>
            <input type="number" id="valeur" name="valeur" min="0" step="0.01" value="{{ old('valeur') }}" required>

            <label for="etat">État</label>
            <select id="etat" name="etat" required>
                @foreach (['Bon', 'Moyen', 'Mauvais'] as $etat)
                    <option value="{{ $etat }}" {{ old('etat', 'Bon') === $etat ? 'selected' : '' }}>{{ $etat }}</option>
                @endforeach
            </select>

            <label for="date_acquisition">Date d'acquisition</label>
            <input type="date" id="date_acquisition" name="date_acquisition" value="{{ old('date_acquisition') }}" required>

            <div class="boutons">
                <button type="submit" class="btn-enregistrer">Enregistrer le bien</button>
                <a href="/biens" class="btn-annuler">Annuler</a>
            </div>
        </form>
    </main>
</body>
</html>