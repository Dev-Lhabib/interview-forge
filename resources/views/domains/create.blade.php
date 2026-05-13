@extends('layouts.app')

@section('content')
<h1>Nouveau Domaine</h1>

<form method="POST" action="{{ route('domains.store') }}">
    @csrf

    <div>
        <label for="name">Nom du domaine</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}" required>
        @error('name')
            <p style="color: red;">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="color">Couleur</label>
        <input type="color" name="color" id="color" value="{{ old('color', '#3B82F6') }}" required>
        @error('color')
            <p style="color: red;">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit">Créer</button>
    <a href="{{ route('domains.index') }}">Annuler</a>
</form>
@endsection