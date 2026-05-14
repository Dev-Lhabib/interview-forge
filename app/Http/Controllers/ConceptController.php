<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConceptRequest;
use App\Http\Requests\UpdateConceptRequest;
use App\Http\Requests\UpdateConceptStatusRequest;
use App\Models\Concept;
use App\Models\Domain;
use Illuminate\Http\Request;

class ConceptController extends Controller
{
    public function index(Request $request, Domain $domain)
    {
        abort_if($domain->user_id !== auth()->id(), 403);

        $concepts = $domain->concepts()
            ->with('domain')
            ->withCount('generatedQuestions')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->difficulty, fn($q, $d) => $q->where('difficulty', $d))
            ->get();

        return view('concepts.index', compact('domain', 'concepts'));
    }

    public function create(Domain $domain)
    {
        abort_if($domain->user_id !== auth()->id(), 403);

        return view('concepts.create', compact('domain'));
    }

    public function store(StoreConceptRequest $request, Domain $domain)
    {
        abort_if($domain->user_id !== auth()->id(), 403);

        $domain->concepts()->create([
            ...$request->validated(),
            'user_id' => auth()->id(),
            'status' => 'to_review',
        ]);

        return redirect()->route('domains.concepts.index', $domain)->with('success', 'Concept créé.');
    }

    public function show(Domain $domain, Concept $concept)
    {
        abort_if($domain->user_id !== auth()->id(), 403);
        abort_if($concept->domain->user_id !== auth()->id(), 403);

        $concept->load(['generatedQuestions' => fn($q) => $q->latest()]);

        return view('concepts.show', compact('domain', 'concept'));
    }

    public function edit(Domain $domain, Concept $concept)
    {
        abort_if($domain->user_id !== auth()->id(), 403);
        abort_if($concept->domain->user_id !== auth()->id(), 403);

        return view('concepts.edit', compact('domain', 'concept'));
    }

    public function update(UpdateConceptRequest $request, Domain $domain, Concept $concept)
    {
        abort_if($domain->user_id !== auth()->id(), 403);
        abort_if($concept->domain->user_id !== auth()->id(), 403);

        $concept->update($request->validated());

        return redirect()->route('domains.concepts.show', [$domain, $concept])->with('success', 'Concept mis à jour.');
    }

    public function destroy(Domain $domain, Concept $concept)
    {
        abort_if($domain->user_id !== auth()->id(), 403);
        abort_if($concept->domain->user_id !== auth()->id(), 403);

        $concept->delete();

        return redirect()->route('domains.concepts.index', $domain)->with('success', 'Concept supprimé.');
    }

    public function updateStatus(UpdateConceptStatusRequest $request, Concept $concept)
    {
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