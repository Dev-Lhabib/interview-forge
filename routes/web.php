<?php

use App\Http\Controllers\ConceptController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\GeneratedQuestionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('domains', DomainController::class)->except('show');

    Route::get('concepts/archived', [ConceptController::class, 'archived'])->name('concepts.archived');
    Route::patch('concepts/{concept}/restore', [ConceptController::class, 'restore'])->name('concepts.restore');

    Route::resource('domains.concepts', ConceptController::class);

    Route::patch('concepts/{concept}/status', [ConceptController::class, 'updateStatus'])->name('concepts.updateStatus');

    Route::post('concepts/{concept}/generate', [GeneratedQuestionController::class, 'store'])->name('questions.generate');
    Route::delete('generated-questions/{generatedQuestion}', [GeneratedQuestionController::class, 'destroy'])->name('questions.destroy');
});

require __DIR__.'/auth.php';
