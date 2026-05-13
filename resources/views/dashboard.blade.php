@extends('layouts.app')

@section('content')
<h1>Bienvenue sur InterviewPrep</h1>
<p>Préparez-vous pour votre prochain entretien technique.</p>

@auth
    <p><a href="{{ route('domains.index') }}">Voir mes domaines</a></p>
@endauth
@guest
    <p><a href="{{ route('register') }}">Créer un compte</a> ou <a href="{{ route('login') }}">se connecter</a></p>
@endguest
@endsection