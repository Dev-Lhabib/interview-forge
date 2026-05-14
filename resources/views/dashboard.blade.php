@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Mon Tableau de Bord</h1>
            <p class="text-gray-500 mt-1">Suivez votre progression vers la maîtrise</p>
        </div>
        <a href="{{ route('domains.index') }}" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg hover:bg-indigo-700 transition-colors shadow-md hover:shadow-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Voir mes domaines
        </a>
    </div>

    @auth
        @if($totalConcepts > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Concepts</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalConcepts }}</p>
                        </div>
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">À Revoir</p>
                            <p class="text-3xl font-bold text-amber-600 mt-1">{{ $totalToReview }}</p>
                        </div>
                        <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">En Cours</p>
                            <p class="text-3xl font-bold text-blue-600 mt-1">{{ $totalInProgress }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Maîtrisés</p>
                            <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $totalMastered }}</p>
                        </div>
                        <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-indigo-100 text-sm font-medium">Pourcentage de Maîtrise</p>
                        <p class="text-5xl font-bold mt-2">{{ $masteryPercentage }}%</p>
                        <p class="text-indigo-200 text-sm mt-2">{{ $totalMastered }} sur {{ $totalConcepts }} concepts maîtrisés</p>
                    </div>
                    <div class="relative w-32 h-32">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="45" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="10"></circle>
                            <circle cx="50" cy="50" r="45" fill="none" stroke="white" stroke-width="10" stroke-dasharray="{{ $masteryPercentage * 2.83 }} 283" stroke-linecap="round"></circle>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-12 h-12 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                        Domaine le Plus Maîtrisé
                    </h3>
                    @if($bestDomain && $bestDomain->mastered_count > 0)
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: {{ $bestDomain->color }}20;">
                                <div class="w-6 h-6 rounded" style="background-color: {{ $bestDomain->color }};"></div>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $bestDomain->name }}</p>
                                <p class="text-sm text-gray-500">{{ $bestDomain->mastered_count }} concepts maîtrisés</p>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-400">Aucun domaine maîtrisé</p>
                    @endif
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Plus à Revoir
                    </h3>
                    @if($mostToReviewDomain && $mostToReviewDomain->to_review_count > 0)
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: {{ $mostToReviewDomain->color }}20;">
                                <div class="w-6 h-6 rounded" style="background-color: {{ $mostToReviewDomain->color }};"></div>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $mostToReviewDomain->name }}</p>
                                <p class="text-sm text-gray-500">{{ $mostToReviewDomain->to_review_count }} concepts à revoir</p>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-400">Aucun domaine à revoir</p>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Progression par Domaine
                    </h3>
                    <span class="text-sm text-gray-500">{{ $totalQuestions }} questions générées</span>
                </div>

                <div class="space-y-4">
                    @forelse ($domains as $domain)
                        @php
                            $pct = $domain->concepts_count > 0
                                ? round(($domain->mastered_count / $domain->concepts_count) * 100)
                                : 0;
                            $questionsCount = $domain->concepts->sum('generated_questions_count');
                        @endphp
                        <div class="group">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 rounded-full" style="background-color: {{ $domain->color }};"></div>
                                    <span class="font-medium text-gray-900">{{ $domain->name }}</span>
                                </div>
                                <span class="text-sm text-gray-500">
                                    {{ $domain->concepts_count }} concepts
                                    @if($questionsCount > 0)
                                        <span class="text-emerald-600 font-medium">({{ $questionsCount }} questions)</span>
                                    @endif
                                </span>
                            </div>
                            <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full transition-all duration-500 group-hover:from-indigo-600 group-hover:to-purple-600" style="width: {{ $pct }}%;"></div>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-xs text-gray-400">{{ $pct }}% maîtrisé</span>
                                <span class="text-xs text-gray-400">{{ $domain->mastered_count }}/{{ $domain->concepts_count }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-center py-4">Aucun domaine</p>
                    @endforelse
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Commencez Votre Parcours</h2>
                <p class="text-gray-500 mb-6 max-w-md mx-auto">Créez votre premier domaine pour organizer vos concepts et préparer votre entretien technique.</p>
                <a href="{{ route('domains.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Créer mon premier domaine
                </a>
            </div>
        @endif
    @endauth

    @guest
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Bienvenue sur InterviewPrep</h2>
            <p class="text-gray-500 mb-8 max-w-lg mx-auto">Préparez-vous pour votre prochain entretien technique. Organisez vos connaissances, générez des questions et maîtrisez chaque sujet.</p>
            <div class="flex items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors shadow-md">Créer un compte</a>
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">Se connecter</a>
            </div>
        </div>
    @endguest
</div>
@endsection