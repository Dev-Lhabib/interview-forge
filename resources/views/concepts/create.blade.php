@extends('layouts.app')

@section('page-title', 'Nouveau Concept')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl shadow-md p-8 border border-gray-100">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Nouveau Concept</h2>
            <p class="text-gray-600">Domaine: <span class="font-semibold" style="color: {{ $domain->color }}">{{ $domain->name }}</span></p>
        </div>

        <form method="POST" action="{{ route('domains.concepts.store', $domain) }}" class="space-y-6">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Titre du concept</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                @error('title')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="explanation" class="block text-sm font-medium text-gray-700 mb-2">Explication</label>
                <textarea name="explanation" id="explanation" rows="6" required 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">{{ old('explanation') }}</textarea>
                @error('explanation')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-2">Difficulté</label>
                    <select name="difficulty" id="difficulty" required 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                        <option value="">Sélectionner</option>
                        <option value="junior" @selected(old('difficulty') === 'junior')>Junior</option>
                        <option value="mid" @selected(old('difficulty') === 'mid')>Mid</option>
                        <option value="senior" @selected(old('difficulty') === 'senior')>Senior</option>
                    </select>
                    @error('difficulty')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('domains.concepts.index', $domain) }}" class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all shadow-sm">
                    Créer le concept
                </button>
            </div>
        </form>
    </div>
</div>
@endsection