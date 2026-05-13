@extends('layouts.app')

@section('content')
<h1>Mes Domaines</h1>

<a href="{{ route('domains.create') }}">Nouveau domaine</a>

@forelse ($domains as $domain)
    <div style="border: 1px solid #ccc; padding: 1rem; margin-bottom: 1rem;">
        <span style="background-color: {{ $domain->color }}; padding: 0.25rem 0.5rem; color: white; border-radius: 4px;">
            {{ $domain->name }}
        </span>

        <p>{{ $domain->concepts_count }} concepts · {{ $domain->mastered_count }} maîtrisés</p>

        <a href="{{ route('domains.concepts.index', $domain) }}">Voir les concepts</a>

        <a href="{{ route('domains.edit', $domain) }}">Modifier</a>

        <form method="POST" action="{{ route('domains.destroy', $domain) }}" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('Supprimer ce domaine et tous ses concepts ?')">
                Supprimer
            </button>
        </form>
    </div>
@empty
    <p>Aucun domaine créé. <a href="{{ route('domains.create') }}">Créer votre premier domaine</a>.</p>
@endforelse
@endsection