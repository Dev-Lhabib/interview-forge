@extends('layouts.app')

@section('content')
<h1>Concepts Archivés</h1>

<a href="{{ route('dashboard') }}">← Retour au Dashboard</a>

@forelse ($concepts as $concept)
    <div style="border: 1px solid #ccc; padding: 1rem; margin-bottom: 1rem;">
        <h3>{{ $concept->title }}</h3>
        <p><strong>Domaine:</strong> {{ $concept->domain->name }}</p>
        <p>
            <span style="background-color: #6b7280; color: white; padding: 0.2rem 0.5rem; border-radius: 4px;">
                {{ $concept->difficultyLabel }}
            </span>
            <span style="background-color: #3b82f6; color: white; padding: 0.2rem 0.5rem; border-radius: 4px;">
                {{ $concept->statusLabel }}
            </span>
        </p>

        <form method="POST" action="{{ route('concepts.restore', $concept) }}">
            @csrf
            @method('PATCH')
            <button type="submit">Restaurer</button>
        </form>
    </div>
@empty
    <p>Aucun concept archivé.</p>
@endforelse
@endsection