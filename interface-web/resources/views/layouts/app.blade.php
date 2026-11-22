<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titre', 'Patrimoine de l’État')</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Segoe UI', Arial, sans-serif; color: #1b2430; background: #e3e6ea; }
        .app { display: flex; min-height: 100vh; }

        .side { width: 230px; flex: none; background: #0f2a4a; color: #fff; padding: 24px 16px; display: flex; flex-direction: column; }
        .logo { font-size: 18px; font-weight: 700; margin: 0 8px 28px; }
        .nav a { display: block; padding: 10px 12px; margin-bottom: 4px; border-radius: 8px; color: #d3deee; font-size: 14px; text-decoration: none; }
        .nav a.on { background: #1c5ccf; color: #fff; font-weight: 700; }
        .profil { margin-top: auto; font-size: 14px; padding: 0 8px; }
        .profil .role { color: #d3deee; margin-bottom: 10px; text-transform: capitalize; }

        .main { flex: 1; padding: 32px 40px; min-width: 0; }
        h1 { margin: 0 0 4px; font-size: 26px; color: #0f2a4a; }
        .sub { margin: 0 0 24px; color: #5b6676; font-size: 14px; }
        h2 { color: #0f2a4a; font-size: 18px; margin: 24px 0 8px; }

        .card { background: #fff; border: 1px solid #cfd4db; border-radius: 12px; padding: 20px; }
        .row { display: flex; gap: 16px; margin-bottom: 16px; flex-wrap: wrap; }
        .row > * { flex: 1; min-width: 180px; }
        .entete { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; }

        .btn { display: inline-block; background: #1c5ccf; color: #fff; border: 0; border-radius: 8px; padding: 11px 18px; font-size: 14px; font-weight: 600; text-decoration: none; cursor: pointer; }
        .btn2 { background: #fff; color: #1b2430; border: 1px solid #c5cedb; }
        .btn-danger { background: #fff; color: #991b1b; border: 1px solid #fecaca; padding: 7px 12px; }

        .in { width: 100%; padding: 11px 12px; border: 1px solid #c5cedb; border-radius: 8px; font-size: 14px; background: #fff; color: #1b2430; }
        label { display: block; margin: 0 0 6px; font-size: 13px; font-weight: 600; }

        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th { text-align: left; padding: 10px 12px; color: #5b6676; font-size: 12px; text-transform: uppercase; border-bottom: 1px solid #dde3ec; }
        td { padding: 12px; border-bottom: 1px solid #eef1f6; }
        td a.lien { color: #1d3f7a; font-weight: 600; margin-right: 10px; text-decoration: none; }

        .etat { padding: 3px 10px; border-radius: 99px; font-size: 12px; font-weight: 700; }
        .etat-Bon { background: #dcfce7; color: #166534; }
        .etat-Moyen { background: #fef3c7; color: #92400e; }
        .etat-Mauvais { background: #fee2e2; color: #991b1b; }

        .num { font-size: 28px; font-weight: 700; margin: 6px 0 0; color: #0f2a4a; }
        .num.vert { color: #166534; }
        .num.rouge { color: #b91c1c; }
        .lab { font-size: 13px; color: #5b6676; }

        .bar { display: flex; align-items: center; gap: 10px; margin: 12px 0; font-size: 13px; }
        .bar span { width: 90px; flex: none; }
        .bar i { display: block; height: 12px; border-radius: 6px; background: #0f2a4a; }
        .bar i.vert { background: #15803d; }
        .bar i.jaune { background: #d97706; }
        .bar i.rouge { background: #c0392b; }

        .alerte { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; }
        .ok { background: #dcfce7; color: #166534; }
        .err { background: #fee2e2; color: #991b1b; }
        .vide { background: #fff; padding: 24px; border-radius: 12px; border: 1px solid #cfd4db; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="app">
        <aside class="side">
            <p class="logo">Patrimoine de l’État</p>

            <nav class="nav">
                <a href="/dashboard" class="{{ request()->is('dashboard') ? 'on' : '' }}">Tableau de bord</a>
                <a href="/biens" class="{{ request()->is('biens') || request()->is('biens/*/modifier') ? 'on' : '' }}">Biens</a>
                @if (in_array(session('user.role'), ['administrateur', 'agent'], true))
                    <a href="/biens/ajouter" class="{{ request()->is('biens/ajouter') ? 'on' : '' }}">Ajouter un bien</a>
                @endif
                @if (session('user.role') === 'administrateur')
                    <a href="/comptes" class="{{ request()->is('comptes*') ? 'on' : '' }}">Comptes</a>
                @endif
            </nav>

            <div class="profil">
                <div><strong>{{ session('user.name') }}</strong></div>
                <div class="role">{{ session('user.role') ?? 'sans rôle' }}</div>
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="btn btn2">Se déconnecter</button>
                </form>
            </div>
        </aside>

        <main class="main">
            @if (session('succes'))
                <div class="alerte ok">{{ session('succes') }}</div>
            @endif

            @yield('contenu')
        </main>
    </div>
</body>
</html>
