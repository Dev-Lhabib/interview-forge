@extends('layouts.app')

@section('content')
<h1>Modifier le Domaine</h1>

<form method="POST" action="{{ route('domains.update', $domain) }}">
    @csrf
    @method('PATCH')

    <div>
        <label for="name">Nom du domaine</label>
        <input type="text" name="name" id="name" value="{{ old('name', $domain->name) }}" required>
        @error('name')
            <p style="color: red;">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="color">Couleur</label>
        <input type="color" name="color" id="color" value="{{ old('color', $domain->color) }}" required>
        @error('color')
            <p style="color: red;">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit">Mettre à jour</button>
    <a href="{{ route('domains.index') }}">Annuler</a>
</form>
@endsection