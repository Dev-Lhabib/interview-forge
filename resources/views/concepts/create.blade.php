@extends('layouts.app')

@section('content')
<h1>Nouveau Concept — {{ $domain->name }}</h1>

<form method="POST" action="{{ route('domains.concepts.store', $domain) }}">
    @csrf

    <div>
        <label for="title">Titre</label>
        <input type="text" name="title" id="title" value="{{ old('title') }}" required>
        @error('title')
            <p style="color: red;">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="explanation">Explication</label>
        <textarea name="explanation" id="explanation" rows="5" required>{{ old('explanation') }}</textarea>
        @error('explanation')
            <p style="color: red;">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="difficulty">Difficulté</label>
        <select name="difficulty" id="difficulty" required>
            <option value="">Sélectionner</option>
            <option value="junior" @selected(old('difficulty') === 'junior')>Junior</option>
            <option value="mid" @selected(old('difficulty') === 'mid')>Mid</option>
            <option value="senior" @selected(old('difficulty') === 'senior')>Senior</option>
        </select>
        @error('difficulty')
            <p style="color: red;">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit">Créer</button>
    <a href="{{ route('domains.concepts.index', $domain) }}">Annuler</a>
</form>
@endsection