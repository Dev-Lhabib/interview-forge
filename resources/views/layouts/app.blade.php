<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'InterviewPrep') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-indigo-600">InterviewPrep</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-indigo-600 transition-colors {{ request()->routeIs('dashboard') ? 'text-indigo-600 font-medium' : '' }}">Dashboard</a>
                        <a href="{{ route('domains.index') }}" class="text-gray-600 hover:text-indigo-600 transition-colors {{ request()->routeIs('domains.*') ? 'text-indigo-600 font-medium' : '' }}">Domaines</a>
                        <a href="{{ route('concepts.archived') }}" class="text-gray-600 hover:text-indigo-600 transition-colors {{ request()->routeIs('concepts.archived') ? 'text-indigo-600 font-medium' : '' }}">Archivés</a>
                    @endauth
                </div>
                <div class="flex items-center">
                    @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-red-600 transition-colors">Déconnexion</button>
                        </form>
                    @endauth
                    @guest
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-indigo-600 mr-4">Connexion</a>
                        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">Inscription</a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @include('partials.flash-messages')

        @yield('content')
    </main>
</body>
</html>