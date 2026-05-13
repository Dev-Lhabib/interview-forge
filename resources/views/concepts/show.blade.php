@extends('layouts.app')

@section('content')
<h1>{{ $concept->title }}</h1>

<p>
    <span style="background-color: #6b7280; color: white; padding: 0.25rem 0.5rem; border-radius: 4px;">
        {{ $concept->difficultyLabel }}
    </span>
    <span style="background-color: #3b82f6; color: white; padding: 0.25rem 0.5rem; border-radius: 4px;">
        {{ $concept->statusLabel }}
    </span>
</p>

<div style="margin: 1rem 0;">
    <h3>Explication</h3>
    <p>{{ $concept->explanation }}</p>
</div>

<a href="{{ route('domains.concepts.edit', [$domain, $concept]) }}">Modifier</a>

<form method="POST" action="{{ route('domains.concepts.destroy', [$domain, $concept]) }}" style="display: inline;">
    @csrf
    @method('DELETE')
    <button type="submit" onclick="return confirm('Supprimer ce concept ?')">Supprimer</button>
</form>

<hr>

<h2>Questions générées</h2>

<form method="POST" action="{{ route('questions.generate', $concept) }}">
    @csrf
    <button type="submit">Générer des questions d'entretien</button>
</form>
@error('api')
    <p style="color: red;">{{ $message }}</p>
@enderror

@forelse ($concept->generatedQuestions as $gen)
    <div style="border: 1px solid #ccc; padding: 1rem; margin-bottom: 1rem;">
        <p><strong>{{ $gen->created_at->format('d/m/Y H:i') }}</strong></p>
        <ul>
            @foreach ($gen->questions as $question)
                <li>{{ $question }}</li>
            @endforeach
        </ul>
        <form method="POST" action="{{ route('questions.destroy', $gen) }}" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('Supprimer cette génération ?')">Supprimer</button>
        </form>
    </div>
@empty
    <p>Aucune question générée pour ce concept.</p>
@endforelse

<a href="{{ route('domains.concepts.index', $domain) }}">← Retour aux concepts</a>
@endsection