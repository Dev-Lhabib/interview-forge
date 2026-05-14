@extends('layouts.app')

@section('page-title', 'Concepts Archivés')

@section('content')
<div class="mb-6">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Retour au Dashboard
    </a>
</div>

<div class="grid grid-cols-1 gap-4">
    @forelse ($concepts as $concept)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $concept->title }}</h3>
                    <p class="text-sm text-gray-600 mb-3">
                        <span class="font-semibold">Domaine:</span> 
                        <span style="color: {{ $concept->domain->color }}">{{ $concept->domain->name }}</span>
                    </p>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded-full">
                            {{ $concept->difficultyLabel }}
                        </span>
                        <span class="px-3 py-1 text-xs font-medium text-white bg-gray-400 rounded-full">
                            {{ $concept->statusLabel }}
                        </span>
                    </div>
                </div>
                <form method="POST" action="{{ route('concepts.restore', $concept) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Restaurer
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <p class="text-gray-500 font-medium">Aucun concept archivé.</p>
        </div>
    @endforelse
</div>
@endsection