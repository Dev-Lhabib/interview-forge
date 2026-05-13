@extends('layouts.app')

@section('content')
<h1>Concepts — {{ $domain->name }}</h1>

<a href="{{ route('domains.concepts.create', $domain) }}">Nouveau concept</a>

<form method="GET" action="{{ route('domains.concepts.index', $domain) }}" style="margin: 1rem 0;">
    <label>Filtrer par statut:</label>
    <select name="status" onchange="this.form.submit()">
        <option value="">Tous</option>
        <option value="to_review" @selected(request('status') === 'to_review')>À revoir</option>
        <option value="in_progress" @selected(request('status') === 'in_progress')>En cours</option>
        <option value="mastered" @selected(request('status') === 'mastered')>Maîtrisé</option>
    </select>

    <label>Filtrer par difficulté:</label>
    <select name="difficulty" onchange="this.form.submit()">
        <option value="">Toutes</option>
        <option value="junior" @selected(request('difficulty') === 'junior')>Junior</option>
        <option value="mid" @selected(request('difficulty') === 'mid')>Mid</option>
        <option value="senior" @selected(request('difficulty') === 'senior')>Senior</option>
    </select>

    @if(request('status') || request('difficulty'))
        <a href="{{ route('domains.concepts.index', $domain) }}">Réinitialiser</a>
    @endif
</form>

@forelse ($concepts as $concept)
    <div style="border: 1px solid #ccc; padding: 1rem; margin-bottom: 1rem;">
        <h3>{{ $concept->title }}</h3>
        <p>
            <span style="background-color: #6b7280; color: white; padding: 0.2rem 0.5rem; border-radius: 4px;">
                {{ $concept->difficultyLabel }}
            </span>
            <span style="background-color: #3b82f6; color: white; padding: 0.2rem 0.5rem; border-radius: 4px;">
                {{ $concept->statusLabel }}
            </span>
        </p>

        <form method="POST" action="{{ route('concepts.updateStatus', $concept) }}" style="display: inline;">
            @csrf
            @method('PATCH')
            <select name="status" onchange="this.form.submit()">
                <option value="to_review" @selected($concept->status === 'to_review')>À revoir</option>
                <option value="in_progress" @selected($concept->status === 'in_progress')>En cours</option>
                <option value="mastered" @selected($concept->status === 'mastered')>Maîtrisé</option>
            </select>
        </form>

        <a href="{{ route('domains.concepts.show', [$domain, $concept]) }}">Voir</a>
        <a href="{{ route('domains.concepts.edit', [$domain, $concept]) }}">Modifier</a>
    </div>
@empty
    <p>Aucun concept trouvé.</p>
@endforelse
@endsection