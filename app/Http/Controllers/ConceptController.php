<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConceptRequest;
use App\Http\Requests\UpdateConceptRequest;
use App\Models\Concept;
use Illuminate\Http\Request;

class ConceptController extends Controller
{
    public function index(Request $request, $domain)
    {
        $domain = auth()->user()->domains()->findOrFail($domain);

        $concepts = $domain->concepts()
            ->with('domain')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->difficulty, fn($q, $d) => $q->where('difficulty', $d))
            ->get();

        return view('concepts.index', compact('domain', 'concepts'));
    }

    public function create($domain)
    {
        $domain = auth()->user()->domains()->findOrFail($domain);

        return view('concepts.create', compact('domain'));
    }

    public function store(StoreConceptRequest $request, $domain)
    {
        $domain = auth()->user()->domains()->findOrFail($domain);

        $domain->concepts()->create([
            ...$request->validated(),
            'user_id' => auth()->id(),
            'status' => 'to_review',
        ]);

        return redirect()->route('domains.concepts.index', $domain)->with('success', 'Concept créé.');
    }

    public function show($domain, Concept $concept)
    {
        $domain = auth()->user()->domains()->findOrFail($domain);
        abort_if($concept->domain->user_id !== auth()->id(), 403);

        $concept->load(['generatedQuestions' => fn($q) => $q->latest()]);

        return view('concepts.show', compact('domain', 'concept'));
    }

    public function edit($domain, Concept $concept)
    {
        $domain = auth()->user()->domains()->findOrFail($domain);
        abort_if($concept->domain->user_id !== auth()->id(), 403);

        return view('concepts.edit', compact('domain', 'concept'));
    }

    public function update(UpdateConceptRequest $request, $domain, Concept $concept)
    {
        $domain = auth()->user()->domains()->findOrFail($domain);
        abort_if($concept->domain->user_id !== auth()->id(), 403);

        $concept->update($request->validated());

        return redirect()->route('domains.concepts.show', [$domain, $concept])->with('success', 'Concept mis à jour.');
    }

    public function destroy($domain, Concept $concept)
    {
        $domain = auth()->user()->domains()->findOrFail($domain);
        abort_if($concept->domain->user_id !== auth()->id(), 403);

        $concept->delete();

        return redirect()->route('domains.concepts.index', $domain)->with('success', 'Concept supprimé.');
    }

    public function updateStatus(Request $request, Concept $concept)
    {
        $request->validate([
            'status' => 'required|in:to_review,in_progress,mastered',
        ]);

        abort_if($concept->domain->user_id !== auth()->id(), 403);

        $concept->update(['status' => $request->status]);

        return back()->with('success', 'Statut mis à jour.');
    }

    public function archived()
    {
        $concepts = Concept::onlyTrashed()
            ->whereHas('domain', fn($q) => $q->where('user_id', auth()->id()))
            ->with('domain')
            ->get();

        return view('concepts.archived', compact('concepts'));
    }

    public function restore($id)
    {
        $concept = Concept::onlyTrashed()
            ->whereHas('domain', fn($q) => $q->where('user_id', auth()->id()))
            ->findOrFail($id);

        $concept->restore();

        return back()->with('success', 'Concept restauré.');
    }
}