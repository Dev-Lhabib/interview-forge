<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'InterviewPrep') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <nav>
        @auth
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('domains.index') }}">Mes Domaines</a>
            <a href="{{ route('concepts.archived') }}">Archivés</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Déconnexion</button>
            </form>
        @endauth
        @guest
            <a href="{{ route('login') }}">Connexion</a>
            <a href="{{ route('register') }}">Inscription</a>
        @endguest
    </nav>

    <main>
        @include('partials.flash-messages')

        @yield('content')
    </main>
</body>
</html>