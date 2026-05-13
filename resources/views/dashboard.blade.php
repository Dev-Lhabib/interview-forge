@extends('layouts.app')

@section('content')
<h1>Mon Tableau de Bord</h1>

@auth
    <a href="{{ route('domains.index') }}">Mes Domaines</a> |
    <a href="{{ route('concepts.archived') }}">Concepts Archivés</a>

    @if($totalConcepts > 0)
        <div style="margin: 2rem 0;">
            <h2>Statistiques Globales</h2>
            <p><strong>Total des concepts:</strong> {{ $totalConcepts }}</p>
            <p><strong>À revoir:</strong> {{ $totalToReview }}</p>
            <p><strong>En cours:</strong> {{ $totalInProgress }}</p>
            <p><strong>Maîtrisés:</strong> {{ $totalMastered }}</p>
            <p><strong>Pourcentage de maîtrise:</strong> {{ $masteryPercentage }}%</p>
            <p><strong>Questions générées:</strong> {{ $totalQuestions }}</p>
        </div>

        <div style="margin: 2rem 0;">
            <h2>Domaines Clés</h2>
            @if($bestDomain && $bestDomain->mastered_count > 0)
                <p><strong>Meilleur domaine maîtrisé:</strong> {{ $bestDomain->name }} ({{ $bestDomain->mastered_count }} maîtrisés)</p>
            @else
                <p><strong>Meilleur domaine maîtrisé:</strong> Aucun domaine</p>
            @endif

            @if($mostToReviewDomain && $mostToReviewDomain->to_review_count > 0)
                <p><strong>Plus à revoir:</strong> {{ $mostToReviewDomain->name }} ({{ $mostToReviewDomain->to_review_count }} à revoir)</p>
            @else
                <p><strong>Plus à revoir:</strong> Aucun domaine</p>
            @endif
        </div>

        <div style="margin: 2rem 0;">
            <h2>Progression par Domaine</h2>
            @forelse ($domains as $domain)
                @php
                    $pct = $domain->concepts_count > 0
                        ? round(($domain->mastered_count / $domain->concepts_count) * 100)
                        : 0;
                @endphp
                <div style="margin-bottom: 1rem;">
                    <p>
                        <strong>{{ $domain->name }}</strong>
                        ({{ $domain->concepts_count }} concepts, {{ $domain->mastered_count }} maîtrisés)
                    </p>
                    <div style="background-color: #e5e7eb; width: 100%; height: 20px; border-radius: 4px;">
                        <div style="background-color: #10b981; height: 20px; border-radius: 4px; width: {{ $pct }}%;"></div>
                    </div>
                    <p style="font-size: 0.9rem;">{{ $pct }}% maîtrisé</p>
                </div>
            @empty
                <p>Aucun domaine. <a href="{{ route('domains.create') }}">Créer votre premier domaine</a>.</p>
            @endforelse
        </div>
    @else
        <p>Aucun concept pour le moment. <a href="{{ route('domains.index') }}">Commencez par créer un domaine</a>.</p>
    @endif
@endauth

@guest
    <p>Bienvenue sur InterviewPrep!</p>
    <p><a href="{{ route('register') }}">Créer un compte</a> ou <a href="{{ route('login') }}">se connecter</a> pour commencer.</p>
@endguest
@endsection