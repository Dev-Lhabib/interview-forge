<?php

namespace App\Http\Controllers;

use App\Models\Concept;
use App\Models\GeneratedQuestion;
use App\Services\GroqService;
use Illuminate\Http\Request;

class GeneratedQuestionController extends Controller
{
    public function store(Concept $concept, GroqService $groqService)
    {
        $concept->load('domain');

        $this->authorize('view', $concept);

        $generation = $groqService->generateInterviewQuestions($concept);

        if (!$generation) {
            return back()->with('error', 'La génération de questions a échoué. Vérifie ta connexion ou réessaie dans quelques secondes.');
        }

        return back()->with('success', '5 questions générées avec succès.');
    }

    public function destroy(GeneratedQuestion $generatedQuestion)
    {
        $this->authorize('delete', $generatedQuestion);

        $generatedQuestion->delete();

        return back()->with('success', 'Génération supprimée.');
    }
}