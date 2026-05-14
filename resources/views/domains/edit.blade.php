@extends('layouts.app')

@section('page-title', 'Modifier le domaine')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-md p-8 border border-gray-100">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Modifier le domaine</h2>
            <p class="text-gray-600">Mettez à jour les informations du domaine</p>
        </div>

        <form method="POST" action="{{ route('domains.update', $domain) }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nom du domaine</label>
                <input type="text" name="name" id="name" value="{{ old('name', $domain->name) }}" required 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Couleur</label>
                <div class="flex items-center space-x-4">
                    <input type="color" name="color" id="color" value="{{ old('color', $domain->color) }}" required 
                        class="h-12 w-20 rounded-lg border-2 border-gray-300 cursor-pointer">
                    <span class="text-sm text-gray-500">Choisissez une couleur pour identifier ce domaine</span>
                </div>
                @error('color')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('domains.index') }}" class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all shadow-sm">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection