@extends('layouts.app')

@section('titre', 'Tableau de bord - Patrimoine')

@section('contenu')
    <h1>Tableau de bord</h1>
    <p class="sub">Vue d’ensemble du patrimoine.</p>

    @if ($erreur)
        <div class="alerte err">{{ $erreur }}</div>
    @else
        @php
            // Valeur totale abrégée : 3 629 500 000 => "3,6 Md FCFA"
            $v = (float) $stats['valeur_totale'];
            if ($v >= 1000000000) {
                $valeurAffichee = preg_replace('/,0$/', '', number_format($v / 1000000000, 1, ',', ' ')) . ' Md FCFA';
            } elseif ($v >= 1000000) {
                $valeurAffichee = preg_replace('/,0$/', '', number_format($v / 1000000, 1, ',', ' ')) . ' M FCFA';
            } else {
                $valeurAffichee = number_format($v, 0, ',', ' ') . ' FCFA';
            }
        @endphp

        <div class="row">
            <div class="card">
                <div class="lab">Biens enregistrés</div>
                <p class="num">{{ $stats['total_biens'] }}</p>
            </div>
            <div class="card">
                <div class="lab">Valeur totale</div>
                <p class="num">{{ $valeurAffichee }}</p>
            </div>
            <div class="card">
                <div class="lab">En bon état</div>
                <p class="num vert">{{ $stats['par_etat']['Bon'] ?? 0 }}</p>
            </div>
            <div class="card">
                <div class="lab">À surveiller</div>
                <p class="num rouge">{{ ($stats['par_etat']['Moyen'] ?? 0) + ($stats['par_etat']['Mauvais'] ?? 0) }}</p>
            </div>
        </div>

        @php
            $maxCategorie = max(1, collect($stats['par_categorie'])->max('total') ?? 1);
            $maxEtat = max(1, collect($stats['par_etat'])->max() ?? 1);
            $classesEtat = ['Bon' => 'vert', 'Moyen' => 'jaune', 'Mauvais' => 'rouge'];
        @endphp

        <div class="row">
            <div class="card">
                <strong>Biens par catégorie</strong>
                @forelse ($stats['par_categorie'] as $nom => $c)
                    <div class="bar">
                        <span>{{ $nom }}</span>
                        <i style="width: {{ round(($c['total'] / $maxCategorie) * 240) }}px"></i>
                        <b>{{ $c['total'] }}</b>
                    </div>
                @empty
                    <p class="sub">Aucun bien pour le moment.</p>
                @endforelse
            </div>

            <div class="card">
                <strong>Biens par état</strong>
                @forelse ($stats['par_etat'] as $etat => $total)
                    <div class="bar">
                        <span>{{ $etat }}</span>
                        <i class="{{ $classesEtat[$etat] ?? '' }}" style="width: {{ round(($total / $maxEtat) * 240) }}px"></i>
                        <b>{{ $total }}</b>
                    </div>
                @empty
                    <p class="sub">Aucun bien pour le moment.</p>
                @endforelse
            </div>
        </div>
    @endif
@endsection