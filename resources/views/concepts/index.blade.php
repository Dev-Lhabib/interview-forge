@extends('layouts.app')

@section('page-title', 'Concepts — ' . $domain->name)

@section('header-actions')
    <a href="{{ route('domains.concepts.create', $domain) }}" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all shadow-sm flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nouveau concept
    </a>
@endsection

@section('content')
<!-- Filters Card -->
<div class="bg-white rounded-xl shadow-sm p-5 mb-6 border border-gray-100">
    <form method="GET" action="{{ route('domains.concepts.index', $domain) }}" class="flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-700">Filtrer par statut:</label>
            <select name="status" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                <option value="">Tous</option>
                <option value="to_review" @selected(request('status') === 'to_review')>À revoir</option>
                <option value="in_progress" @selected(request('status') === 'in_progress')>En cours</option>
                <option value="mastered" @selected(request('status') === 'mastered')>Maîtrisé</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-700">Filtrer par difficulté:</label>
            <select name="difficulty" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                <option value="">Tous niveaux</option>
                <option value="junior" @selected(request('difficulty') === 'junior')>Junior</option>
                <option value="mid" @selected(request('difficulty') === 'mid')>Mid</option>
                <option value="senior" @selected(request('difficulty') === 'senior')>Senior</option>
            </select>
        </div>

        @if(request('status') || request('difficulty'))
            <a href="{{ route('domains.concepts.index', $domain) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Réinitialiser</a>
        @endif
    </form>
</div>

<!-- Concepts Grid -->
<div class="grid grid-cols-1 gap-4">
    @forelse ($concepts as $concept)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <!-- Left: Content -->
                <div class="flex-1 min-w-0">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $concept->title }}</h3>
                    <div class="flex items-center gap-3 mb-3">
                        <span class="inline-flex items-center px-3 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded-full">
                            {{ $concept->difficultyLabel }}
                        </span>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed">{{ $concept->explanation }}</p>
                </div>
                
                <!-- Right: Actions -->
                <div class="flex items-center gap-3 flex-shrink-0 lg:ml-6">
                    <form method="POST" action="{{ route('concepts.updateStatus', $concept) }}" class="flex-shrink-0">
                        @csrf
                        @method('PATCH')
                        <select name="status" onchange="this.form.submit()" class="w-full lg:w-auto px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white shadow-sm hover:bg-gray-50 transition-colors">
                            <option value="to_review" @selected($concept->status === 'to_review')>À revoir</option>
                            <option value="in_progress" @selected($concept->status === 'in_progress')>En cours</option>
                            <option value="mastered" @selected($concept->status === 'mastered')>Maîtrisé</option>
                        </select>
                    </form>
                    <a href="{{ route('domains.concepts.show', [$domain, $concept]) }}" class="inline-flex items-center px-5 py-2.5 text-sm text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg font-medium transition-colors shadow-sm">
                        Voir
                    </a>
                    <a href="{{ route('domains.concepts.edit', [$domain, $concept]) }}" class="inline-flex items-center px-4 py-2.5 text-sm text-gray-700 hover:text-indigo-600 font-medium transition-colors">
                        Modifier
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-500 font-medium">Aucun concept trouvé.</p>
        </div>
    @endforelse
</div>
@endsection