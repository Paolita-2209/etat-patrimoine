<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Patrimoine</title>
    <style>
        body { font-family: sans-serif; background: #f3f4f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        form { background: #fff; padding: 2rem; border-radius: 8px; width: 320px; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        h1 { margin-top: 0; font-size: 1.4rem; }
        input { width: 100%; padding: .6rem; margin: .4rem 0 1rem; box-sizing: border-box; }
        button { width: 100%; padding: .7rem; background: #1d4ed8; color: #fff; border: 0; border-radius: 4px; cursor: pointer; }
        .erreur { color: #b91c1c; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <form method="POST" action="/login">
        @csrf
        <h1>Connexion</h1>

        @if ($errors->any())
            <div class="erreur">{{ $errors->first() }}</div>
        @endif

        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required>

        <label>Mot de passe</label>
        <input type="password" name="password" required>

        <button type="submit">Se connecter</button>
    </form>
</body>
</html>