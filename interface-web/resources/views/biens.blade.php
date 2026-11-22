@extends('layouts.app')

@section('titre', 'Biens - Patrimoine')

@section('contenu')
    @php
        $role = session('user.role');
        $peutAjouter = in_array($role, ['administrateur', 'agent'], true);
        $peutModifier = in_array($role, ['administrateur', 'agent', 'responsable'], true);
        $peutSupprimer = $role === 'administrateur';

        $recherche = mb_strtolower(trim((string) request('q')));
        $filtreCategorie = request('categorie');
        $filtreEtat = request('etat');

        $categories = collect($biens)->map(fn ($b) => $b['categorie']['nom'] ?? null)->filter()->unique()->sort()->values();

        $affiches = collect($biens)->filter(function ($b) use ($recherche, $filtreCategorie, $filtreEtat) {
            if ($recherche !== '' && !str_contains(mb_strtolower($b['nom'] . ' ' . $b['localisation']), $recherche)) {
                return false;
            }
            if ($filtreCategorie && ($b['categorie']['nom'] ?? '') !== $filtreCategorie) {
                return false;
            }
            if ($filtreEtat && $b['etat'] !== $filtreEtat) {
                return false;
            }
            return true;
        })->values();
    @endphp

    <div class="entete">
        <div>
            <h1>Biens</h1>
            <p class="sub">Recherchez, filtrez et gérez les biens de l’État.</p>
        </div>
        @if ($peutAjouter)
            <a href="/biens/ajouter" class="btn">+ Ajouter un bien</a>
        @endif
    </div>

    @if ($erreur)
        <div class="alerte err">{{ $erreur }}</div>
    @endif

    @if (session('erreur'))
        <div class="alerte err">{{ session('erreur') }}</div>
    @endif

    <form method="GET" action="/biens" class="row" style="align-items: flex-end;">
        <div style="flex: 2;">
            <label for="q">Rechercher</label>
            <input class="in" id="q" name="q" value="{{ request('q') }}" placeholder="Nom ou localisation">
        </div>
        <div>
            <label for="categorie">Catégorie</label>
            <select class="in" id="categorie" name="categorie">
                <option value="">Toutes</option>
                @foreach ($categories as $nom)
                    <option value="{{ $nom }}" @selected($filtreCategorie === $nom)>{{ $nom }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="etat">État</label>
            <select class="in" id="etat" name="etat">
                <option value="">Tous</option>
                @foreach (['Bon', 'Moyen', 'Mauvais'] as $etat)
                    <option value="{{ $etat }}" @selected($filtreEtat === $etat)>{{ $etat }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex: none;">
            <button type="submit" class="btn">Filtrer</button>
        </div>
    </form>

    @if ($affiches->isEmpty())
        <div class="vide">Aucun bien ne correspond.</div>
    @else
        <div class="card">
            <table>
                <tr>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Localisation</th>
                    <th>Valeur</th>
                    <th>État</th>
                    <th>Acquisition</th>
                    @if ($peutModifier)
                        <th>Actions</th>
                    @endif
                </tr>
                @foreach ($affiches as $bien)
                    <tr>
                        <td>{{ $bien['nom'] }}</td>
                        <td>{{ $bien['categorie']['nom'] ?? '—' }}</td>
                        <td>{{ $bien['localisation'] }}</td>
                        <td>{{ number_format((float) $bien['valeur'], 0, ',', ' ') }} FCFA</td>
                        <td><span class="etat etat-{{ $bien['etat'] }}">{{ $bien['etat'] }}</span></td>
                        <td>{{ !empty($bien['date_acquisition']) ? \Carbon\Carbon::parse($bien['date_acquisition'])->format('d/m/Y') : '—' }}</td>
                        @if ($peutModifier)
                            <td>
                                <a class="lien" href="/biens/{{ $bien['id'] }}/modifier">Modifier</a>
                                @if ($peutSupprimer)
                                    <form method="POST" action="/biens/{{ $bien['id'] }}" style="display:inline" onsubmit="return confirm('Supprimer ce bien ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                    </form>
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </table>
        </div>
    @endif
@endsection