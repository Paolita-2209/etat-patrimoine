@extends('layouts.app')

@section('titre', 'Comptes - Patrimoine')

@push('styles')
<style>
    .lien-action { background: none; border: 0; padding: 0; font: inherit; font-weight: 600; color: #1d3f7a; text-decoration: underline; cursor: pointer; }
    td a.lien-souligne { color: #1d3f7a; font-weight: 600; margin-right: 14px; text-decoration: underline; }
    .actions { display: flex; align-items: center; }
    .actions form { display: inline; }
    .defilement { overflow-x: auto; }
</style>
@endpush

@section('contenu')
    @php
        $libelles = [
            'administrateur' => 'Administrateur',
            'agent' => 'Agent du patrimoine',
            'responsable' => 'Responsable',
        ];
        $moi = (int) session('user.id');
    @endphp

    <div class="entete">
        <div>
            <h1>Comptes</h1>
            <p class="sub">Réservé à l’administrateur.</p>
        </div>
        <a href="/comptes/ajouter" class="btn">+ Créer un compte</a>
    </div>

    @if ($erreur)
        <div class="alerte err">{{ $erreur }}</div>
    @endif

    @if (session('erreur'))
        <div class="alerte err">{{ session('erreur') }}</div>
    @endif

    @if (count($comptes) === 0)
        <div class="vide">Aucun compte à afficher.</div>
    @else
        <div class="card">
            <div class="defilement">
                <table>
                    <tr>
                        <th>Nom</th>
                        <th>E-mail</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                    @foreach ($comptes as $compte)
                        <tr>
                            <td>{{ $compte['name'] }}</td>
                            <td>{{ $compte['email'] }}</td>
                            <td>{{ $libelles[$compte['role'] ?? ''] ?? 'Sans rôle' }}</td>
                            <td>
                                @if ($compte['actif'])
                                    <span class="etat etat-Bon">Actif</span>
                                @else
                                    <span class="etat etat-Mauvais">Désactivé</span>
                                @endif
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="/comptes/{{ $compte['id'] }}/modifier" class="lien-souligne">Modifier</a>

                                    @if ((int) $compte['id'] !== $moi)
                                        <form method="POST" action="/comptes/{{ $compte['id'] }}/statut"
                                              onsubmit="return confirm('{{ $compte['actif'] ? 'Désactiver ce compte ?' : 'Réactiver ce compte ?' }}')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="lien-action">{{ $compte['actif'] ? 'Désactiver' : 'Activer' }}</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    @endif
@endsection
