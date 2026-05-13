@extends('layouts.app')

@section('content')
<h1>Modifier le Concept</h1>

<form method="POST" action="{{ route('domains.concepts.update', [$domain, $concept]) }}">
    @csrf
    @method('PATCH')

    <div>
        <label for="title">Titre</label>
        <input type="text" name="title" id="title" value="{{ old('title', $concept->title) }}" required>
        @error('title')
            <p style="color: red;">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="explanation">Explication</label>
        <textarea name="explanation" id="explanation" rows="5" required>{{ old('explanation', $concept->explanation) }}</textarea>
        @error('explanation')
            <p style="color: red;">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="difficulty">Difficulté</label>
        <select name="difficulty" id="difficulty" required>
            <option value="junior" @selected($concept->difficulty === 'junior')>Junior</option>
            <option value="mid" @selected($concept->difficulty === 'mid')>Mid</option>
            <option value="senior" @selected($concept->difficulty === 'senior')>Senior</option>
        </select>
        @error('difficulty')
            <p style="color: red;">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="status">Statut</label>
        <select name="status" id="status" required>
            <option value="to_review" @selected($concept->status === 'to_review')>À revoir</option>
            <option value="in_progress" @selected($concept->status === 'in_progress')>En cours</option>
            <option value="mastered" @selected($concept->status === 'mastered')>Maîtrisé</option>
        </select>
        @error('status')
            <p style="color: red;">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit">Mettre à jour</button>
    <a href="{{ route('domains.concepts.show', [$domain, $concept]) }}">Annuler</a>
</form>
@endsection