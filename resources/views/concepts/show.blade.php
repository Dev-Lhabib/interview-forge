@extends('layouts.app')

@section('page-title', $concept->title)

@section('content')
<div class="max-w-4xl">
    <!-- Concept Header Card -->
    <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 mb-6">
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ $concept->title }}</h1>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 text-sm font-medium text-gray-700 bg-gray-100 rounded-full">
                        {{ $concept->difficultyLabel }}
                    </span>
                    <span class="px-3 py-1 text-sm font-medium text-white rounded-full"
                        style="background: linear-gradient(135deg, {{ $domain->color }}dd, {{ $domain->color }});">
                        {{ $concept->statusLabel }}
                    </span>
                    <span class="px-3 py-1 text-sm font-medium text-indigo-700 bg-indigo-50 rounded-full">
                        {{ $domain->name }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('domains.concepts.edit', [$domain, $concept]) }}" 
                    class="px-4 py-2 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg font-medium transition-colors">
                    Modifier
                </a>
                <form method="POST" action="{{ route('domains.concepts.destroy', [$domain, $concept]) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Supprimer ce concept ?')" 
                        class="px-4 py-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg font-medium transition-colors">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-4">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-2">Explication</h3>
            <p class="text-gray-700 leading-relaxed">{{ $concept->explanation }}</p>
        </div>
    </div>

    <!-- AI Questions Section -->
    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl shadow-md p-6 border border-indigo-100">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Questions générées par IA</h2>
            </div>
            <form method="POST" action="{{ route('questions.generate', $concept) }}">
                @csrf
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Générer des questions
                </button>
            </form>
        </div>

        @error('api')
            <div class="mb-4 bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
                <p class="font-medium text-red-800">{{ $message }}</p>
            </div>
        @enderror

        @forelse ($concept->generatedQuestions as $gen)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-4">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-semibold text-gray-600">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $gen->created_at->format('d/m/Y H:i') }}
                    </p>
                    <form method="POST" action="{{ route('questions.destroy', $gen) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Supprimer cette génération ?')" 
                            class="text-sm text-red-600 hover:text-red-800 font-medium">
                            Supprimer
                        </button>
                    </form>
                </div>
                <ol class="space-y-2 list-decimal list-inside text-gray-700">
                    @foreach ($gen->questions as $question)
                        <li class="leading-relaxed">{{ $question }}</li>
                    @endforeach
                </ol>
            </div>
        @empty
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-indigo-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-500 font-medium">Aucune question générée pour ce concept.</p>
                <p class="text-gray-400 text-sm mt-1">Cliquez sur "Générer des questions" pour commencer</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        <a href="{{ route('domains.concepts.index', $domain) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour aux concepts
        </a>
    </div>
</div>
@endsection