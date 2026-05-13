# AGENTS.md — InterviewPrep Laravel

> Read this file **entirely** before writing a single line of code.
> Rules here are non-negotiable, never expire, and override any prompt instruction that contradicts them.
> If a rule conflicts with what you think is "cleaner" or "better practice" — **follow the rule**.
> Last updated: 2026-05-11

---

## 0. How to Start Every Session — Mandatory Pre-Flight

Before doing **anything** else, run through this checklist mentally and confirm each step:

1. Have you read this file (`AGENTS.md`) in full? → If no, read it now.
2. Have you read the relevant `specs/` file for this feature? → If no, read it now.
3. Are you in **PLAN mode** or **BUILD mode**? → If the user hasn't said, ask.
4. Do you know what already exists in the codebase? → If no, ask what files are in place before generating.
5. Do you know which branch is active and which sprint it maps to? → See Section 11.1.

**You must never generate code before completing steps 1–5.**

---

## 1. Project Overview

**InterviewPrep** is a personal Laravel web application that helps a developer organize technical knowledge before a job interview.

**Context:** A Moroccan web developer just finished his training. He has a technical interview at a SaaS startup in Casablanca — backend Laravel position — in ten days. He knows a lot of things but in a scattered way. InterviewPrep gives him structure: organize by technical domain, write concept notes, track mastery level, and generate realistic interview questions via AI.

Core features:
- Organize knowledge by **technical domain** (Laravel, OOP, MySQL, REST API...)
- Write **concept notes** explaining each topic in plain language
- Track **mastery status** per concept (to review / in progress / mastered)
- Generate **realistic interview questions** for any concept via the Groq AI API

**One user = one private workspace.** All data is scoped to the authenticated user.

---

## 2. Tech Stack — Allowed and Forbidden

### ✅ Allowed — use ONLY these

| Layer | Technology | Notes |
|-------|-----------|-------|
| Framework | Laravel 13 | PHP 8.4+ |
| Database | MySQL 8 | Local via Laragon (port 3306) / phpmyadmin — Eloquent only |
| Frontend | Blade templates | No Vue, no React, no Livewire |
| CSS | Plain CSS or Tailwind CSS | No Bootstrap |
| HTTP Client | Laravel `Http::` facade | Zero external packages for API calls |
| AI API | Groq API | Endpoint: `https://api.groq.com/openai/v1/chat/completions` |
| Auth | Laravel Breeze | Already installed — do not reinstall |
| Debug | Laravel Debugbar | Already installed — dev only |
| Version Control | Git + GitHub | Branch-per-feature workflow |

### ❌ Forbidden — never introduce these

- Livewire, Inertia.js, Vue, React, Alpine.js — any JS framework
- Bootstrap — use plain CSS or Tailwind only
- Any AI/LLM SDK package (`openai-php`, `groq-php`, etc.) — use raw `Http::` only
- Repository pattern, `BaseRepository`, Service Container bindings, DDD layers
- Any `composer require` package NOT already in the project without explicit user approval
- Any `npm install` package without explicit user approval
- Vite config changes or new JS build dependencies
- Raw SQL via `DB::statement()` or `DB::select()` — use Eloquent only

---

## 3. Database Schema

### 3.1 Tables

```sql
-- users (managed by Laravel Breeze — never touch this migration)
users
  id              BIGINT UNSIGNED PK AUTO_INCREMENT
  name            VARCHAR(255) NOT NULL
  email           VARCHAR(255) UNIQUE NOT NULL
  password        VARCHAR(255) NOT NULL
  remember_token  VARCHAR(100) NULL
  timestamps

-- domains
domains
  id              BIGINT UNSIGNED PK AUTO_INCREMENT
  user_id         BIGINT UNSIGNED NOT NULL FK → users.id (cascade delete)
  name            VARCHAR(255) NOT NULL
  color           VARCHAR(7) NOT NULL  -- hex color e.g. #3B82F6
  timestamps

-- concepts
concepts
  id              BIGINT UNSIGNED PK AUTO_INCREMENT
  domain_id       BIGINT UNSIGNED NOT NULL FK → domains.id (cascade delete)
  user_id         BIGINT UNSIGNED NOT NULL FK → users.id (cascade delete)
  title           VARCHAR(255) NOT NULL
  explanation     TEXT NOT NULL
  difficulty      ENUM('junior', 'mid', 'senior') NOT NULL DEFAULT 'junior'
  status          ENUM('to_review', 'in_progress', 'mastered') NOT NULL DEFAULT 'to_review'
  deleted_at      TIMESTAMP NULL  -- soft deletes
  timestamps

-- generated_questions
generated_questions
  id              BIGINT UNSIGNED PK AUTO_INCREMENT
  concept_id      BIGINT UNSIGNED NOT NULL FK → concepts.id (cascade delete)
  questions       JSON NOT NULL  -- array of 5 question strings
  timestamps
```

### 3.2 Migration rules

- One migration file per table — never combine multiple table changes in one file
- **Always use fluent FK syntax:**
  ```php
  $table->foreignId('user_id')->constrained()->cascadeOnDelete();
  $table->foreignId('domain_id')->constrained()->cascadeOnDelete();
  $table->foreignId('concept_id')->constrained()->cascadeOnDelete();
  ```
  Never use the old `$table->foreign()->references()->on()` syntax.
- `$table->softDeletes()` on the `concepts` table **only**
- Never modify an existing migration — create a new migration if a change is needed
- Never touch the `users` table migration — owned by Breeze
- The `questions` column uses `$table->json('questions')` — not `text`

---

## 4. Eloquent Models

### 4.1 Relationships — define on all four models, always

```php
// User.php
public function domains(): HasMany   // hasMany(Domain::class)
public function concepts(): HasMany  // hasMany(Concept::class)

// Domain.php
public function user(): BelongsTo    // belongsTo(User::class)
public function concepts(): HasMany  // hasMany(Concept::class)

// Concept.php
public function domain(): BelongsTo              // belongsTo(Domain::class)
public function user(): BelongsTo                // belongsTo(User::class)
public function generatedQuestions(): HasMany    // hasMany(GeneratedQuestion::class)

// GeneratedQuestion.php
public function concept(): BelongsTo  // belongsTo(Concept::class)
```

### 4.2 Accessors — use Laravel 9+ `Attribute::make()` syntax, never `getXxxAttribute()`

```php
// In Concept.php — EXACT implementation required
use Illuminate\Database\Eloquent\Casts\Attribute;

protected function statusLabel(): Attribute
{
    return Attribute::make(
        get: fn () => match($this->status) {
            'to_review'   => 'À revoir',
            'in_progress' => 'En cours',
            'mastered'    => 'Maîtrisé',
        }
    );
}

protected function difficultyLabel(): Attribute
{
    return Attribute::make(
        get: fn () => match($this->difficulty) {
            'junior' => 'Junior',
            'mid'    => 'Mid',
            'senior' => 'Senior',
        }
    );
}
```

### 4.3 Casts

```php
// In GeneratedQuestion.php
protected $casts = [
    'questions' => 'array',
];
```

### 4.4 Fillable — define on every model, no exceptions, never use `$guarded`

```php
// Domain.php
protected $fillable = ['user_id', 'name', 'color'];

// Concept.php
protected $fillable = ['domain_id', 'user_id', 'title', 'explanation', 'difficulty', 'status'];

// GeneratedQuestion.php
protected $fillable = ['concept_id', 'questions'];
```

### 4.5 Soft Deletes — `Concept` model only

```php
use Illuminate\Database\Eloquent\SoftDeletes;
// Add: use SoftDeletes; inside the Concept class body
// Never add SoftDeletes to Domain, User, or GeneratedQuestion
```

Soft-deleted concepts must NOT appear in any regular query.
`withTrashed()` only on the `/concepts/archived` page and `restore()` method.

---

## 5. Controllers & Routes

### 5.1 Route structure — do not reorder

```php
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('domains', DomainController::class);

    // ⚠️ archived MUST be declared BEFORE the resource to prevent Laravel
    // matching "archived" as a {concept} wildcard parameter
    Route::get('concepts/archived', [ConceptController::class, 'archived'])
         ->name('concepts.archived');
    Route::patch('concepts/{concept}/restore', [ConceptController::class, 'restore'])
         ->name('concepts.restore');

    Route::resource('domains.concepts', ConceptController::class);

    Route::patch('concepts/{concept}/status', [ConceptController::class, 'updateStatus'])
         ->name('concepts.updateStatus');

    Route::post('concepts/{concept}/generate', [GeneratedQuestionController::class, 'store'])
         ->name('questions.generate');
    Route::delete('generated-questions/{generatedQuestion}', [GeneratedQuestionController::class, 'destroy'])
         ->name('questions.destroy');
});
```

**Full named route reference:**
```
dashboard
domains.index  domains.create  domains.store  domains.show  domains.edit  domains.update  domains.destroy
concepts.archived  concepts.restore
domains.concepts.index  domains.concepts.create  domains.concepts.store
domains.concepts.show  domains.concepts.edit  domains.concepts.update  domains.concepts.destroy
concepts.updateStatus
questions.generate  questions.destroy
```

> ⚠️ All routes MUST have `->name()`. Never use hardcoded URI strings in Blade or controllers — always `route('name')`.

### 5.2 Controller rules

- Always use resourceful controllers: `php artisan make:controller XxxController --resource`
- Max ~20 lines per method — extract to a Service if longer
- Always scope queries to the authenticated user — never expose other users' data
- Authorization: `abort_if()` pattern (Section 5.3) — no Policies, no Gates
- Flash messages: only `success` and `error` keys
- `with()` mandatory for all relationships displayed in views — zero N+1

### 5.3 Ownership check pattern — use exactly this, every time

```php
// For Domain
abort_if($domain->user_id !== auth()->id(), 403);

// For Concept (through domain)
abort_if($concept->domain->user_id !== auth()->id(), 403);

// For GeneratedQuestion (through concept → domain)
abort_if($generatedQuestion->concept->domain->user_id !== auth()->id(), 403);
```

### 5.4 Correct query patterns

```php
// ✅ Correct — always scope to auth user
auth()->user()->domains()->findOrFail($id);
auth()->user()->domains()->get();
$domain->concepts()->with('domain', 'generatedQuestions')->findOrFail($id);

// ❌ Forbidden — never do these
Domain::find($id);
Domain::all();
Concept::find($id);
Concept::all();
```

---

## 6. Form Request Validation

**Absolute rule: never use `$request->validate([...])` inline in a controller. Ever.**

### Classes required

```bash
php artisan make:request StoreDomainRequest
php artisan make:request UpdateDomainRequest
php artisan make:request StoreConceptRequest
php artisan make:request UpdateConceptRequest
php artisan make:request UpdateConceptStatusRequest
```

### Validation rules (exact)

```php
// StoreDomainRequest / UpdateDomainRequest
'name'  => ['required', 'string', 'max:255'],
'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],

// StoreConceptRequest / UpdateConceptRequest
'title'       => ['required', 'string', 'max:255'],
'explanation' => ['required', 'string', 'min:20'],
'difficulty'  => ['required', 'in:junior,mid,senior'],
'status'      => ['sometimes', 'in:to_review,in_progress,mastered'],

// UpdateConceptStatusRequest
'status' => ['required', 'in:to_review,in_progress,mastered'],
```

### `authorize()` method

All Form Requests: `return auth()->check();`
Ownership verification happens in the **controller** via `abort_if()`, never in `authorize()`.

---

## 7. AI Feature — Groq API Integration

### 7.1 GroqService — the only place `Http::` is called

Create `app/Services/GroqService.php`:

```php
<?php

namespace App\Services;

use App\Models\Concept;
use App\Models\GeneratedQuestion;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    private string $apiKey;
    private string $endpoint = 'https://api.groq.com/openai/v1/chat/completions';
    private string $model    = 'llama3-8b-8192';

    public function __construct()
    {
        $this->apiKey = config('services.groq.key');
    }

    public function generateInterviewQuestions(Concept $concept): GeneratedQuestion|null
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(15)->post($this->endpoint, [
                'model'      => $this->model,
                'max_tokens' => 800,
                'messages'   => [
                    [
                        'role'    => 'system',
                        'content' => 'You are a senior technical interviewer. Return ONLY a JSON array of exactly 5 interview questions. No intro text, no explanation, no markdown. Just the raw JSON array.',
                    ],
                    [
                        'role'    => 'user',
                        'content' => $this->buildPrompt($concept),
                    ],
                ],
            ]);

            if ($response->failed()) {
                Log::error('Groq API error', [
                    'concept_id' => $concept->id,
                    'status'     => $response->status(),
                    'body'       => $response->body(),
                ]);
                return null;
            }

            $content   = $response->json('choices.0.message.content');
            $questions = json_decode($content, true);

            // Validate FIRST — save SECOND
            if (!is_array($questions) || count($questions) !== 5) {
                Log::error('Groq returned malformed JSON', [
                    'concept_id' => $concept->id,
                    'content'    => $content,
                ]);
                return null;
            }

            return GeneratedQuestion::create([
                'concept_id' => $concept->id,
                'questions'  => $questions,
            ]);

        } catch (\Exception $e) {
            Log::error('GroqService exception', [
                'concept_id' => $concept->id,
                'message'    => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function buildPrompt(Concept $concept): string
    {
        return <<<PROMPT
Concept: {$concept->title}
Level: {$concept->difficultyLabel}
Explanation: {$concept->explanation}

Generate exactly 5 technical interview questions a recruiter would ask about this concept.
Return ONLY a JSON array of 5 strings. No numbering, no markdown, no extra text.
PROMPT;
    }
}
```

### 7.2 Config — `config/services.php`

```php
'groq' => [
    'key' => env('GROQ_API_KEY'),
],
```

### 7.3 Environment files

`.env` (never commit):
```
GROQ_API_KEY=your_key_here
```

`.env.example` (commit this — no real key):
```
GROQ_API_KEY=
```

### 7.4 Controller usage

```php
public function store(Concept $concept, GroqService $groqService)
{
    abort_if($concept->domain->user_id !== auth()->id(), 403);

    $generation = $groqService->generateInterviewQuestions($concept);

    if (!$generation) {
        return back()->with('error', 'La génération de questions a échoué. Vérifie ta connexion ou réessaie dans quelques secondes.');
    }

    return back()->with('success', '5 questions générées avec succès.');
}
```

### 7.5 Groq API rules summary

| Rule | Detail |
|------|--------|
| Never `Http::` in a controller | Always delegate to `GroqService` |
| API key source | `config('services.groq.key')` — never raw `env()` in logic code |
| Model | `llama3-8b-8192` — do not change |
| Validate before save | If not a 5-item array → `Log::error()` + return `null` — write nothing to DB |
| Every failure path | Log with context → return `null` → controller shows `error` flash |

---

## 8. N+1 Query Prevention — Zero Tolerance

Verify with Debugbar before every commit.
If you are inside a `@foreach` loop and accessing a relationship, you have an N+1 — fix it.

```php
// DomainController::index()
$domains = auth()->user()
    ->domains()
    ->withCount([
        'concepts',
        'concepts as mastered_count' => fn($q) => $q->where('status', 'mastered'),
    ])
    ->get();

// DashboardController::index()
$domains = auth()->user()->domains()
    ->withCount([
        'concepts',
        'concepts as to_review_count'   => fn($q) => $q->where('status', 'to_review'),
        'concepts as in_progress_count' => fn($q) => $q->where('status', 'in_progress'),
        'concepts as mastered_count'    => fn($q) => $q->where('status', 'mastered'),
    ])
    ->get();

// ConceptController::index()
$concepts = $domain->concepts()
    ->with('domain')
    ->when($request->status,     fn($q, $s) => $q->where('status', $s))
    ->when($request->difficulty, fn($q, $d) => $q->where('difficulty', $d))
    ->get();

// ConceptController::show()
$concept->load(['generatedQuestions' => fn($q) => $q->latest()]);
```

---

## 9. Security Rules — Non-Negotiable

1. Every route (except Breeze auth routes) must be inside `Route::middleware('auth')`
2. Every controller method accessing a domain, concept, or generated question must call `abort_if()` with the correct ownership chain (Section 5.3)
3. Never expose another user's data — always filter through `auth()->user()->domains()`
4. `$fillable` must be defined on every model — never `$guarded = []`
5. No credentials of any kind in any committed file — `.env` is gitignored, `.env.example` has empty placeholder only
6. `withTrashed()` only on the `/concepts/archived` page and `restore()` method
7. No Policies or Gates — manual `abort_if()` checks only
8. Flash messages: only the keys `success` and `error` — no other session keys for user feedback

---

## 10. Blade Templates Rules

### File structure — do not deviate

```
resources/views/
├── layouts/
│   └── app.blade.php              ← main layout — includes flash-messages partial
├── auth/                           ← Breeze generated — DO NOT MODIFY
├── dashboard.blade.php
├── domains/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── concepts/
│   ├── create.blade.php
│   ├── edit.blade.php
│   ├── show.blade.php             ← concept detail + generated questions
│   └── archived.blade.php
└── partials/
    ├── flash-messages.blade.php
    └── concept-card.blade.php
```

Do not create views outside this structure without asking the user.
Do not modify any file inside `resources/views/auth/`.

### Blade coding rules

```blade
{{-- All forms: always include --}}
@csrf

{{-- PATCH / DELETE forms --}}
@method('PATCH')
@method('DELETE')

{{-- Accessors — always, never raw enum values --}}
{{ $concept->statusLabel }}       {{-- ✅ correct --}}
{{ $concept->difficultyLabel }}   {{-- ✅ correct --}}
{{ $concept->status }}            {{-- ❌ forbidden --}}
{{ $concept->difficulty }}        {{-- ❌ forbidden --}}

{{-- Domain color — always dynamic --}}
<span style="background-color: {{ $domain->color }}"> {{-- ✅ --}}
<span style="background-color: #3B82F6">              {{-- ❌ --}}

{{-- Lists — always @forelse, never bare @foreach --}}
@forelse($concepts as $concept)
    ...
@empty
    <p>Aucun concept trouvé.</p>
@endforelse

{{-- Navigation — use directives, not auth()->check() --}}
@auth ... @endauth    {{-- ✅ --}}
@guest ... @endguest  {{-- ✅ --}}
@if(auth()->check())  {{-- ❌ --}}

{{-- Flash messages — only in layouts/app.blade.php --}}
@include('partials.flash-messages')
```

### JavaScript in Blade — the one and only exception

The **only** JavaScript allowed in Blade files:
```blade
onchange="this.form.submit()"
```
on the quick-status select element. No other JS anywhere in Blade.

No `@php` blocks except a single `$pct` local variable inside a loop (dashboard only).

Blade file names: **kebab-case** (e.g. `create-concept.blade.php`).

---

## 11. Git Workflow

### 11.1 Branch map — sprint enforcement

Every task belongs to a sprint, and every sprint has an **assigned branch**.
The agent must confirm the active branch **before writing any code**.
Never commit sprint work to the wrong branch.

| Sprint | Days | Branch | Scope |
|--------|------|--------|-------|
| Sprint 1 — Setup & Auth | Jour 1 (11/05) | `main` | repo init, AGENTS.md, MCD/MLD, migrations, models, seeders, Breeze |
| Sprint 2 — Domains CRUD | Jour 2 (12/05) | `feature/domains-crud` | DomainController, Form Requests, views |
| Sprint 3 — Concepts CRUD | Jour 3 (13/05) | `feature/concepts-crud` | ConceptController, Form Requests, views, status update |
| Sprint 4 — AI Generation | Jour 4 (14/05) | `feature/ai-generation` | GroqService, GeneratedQuestionController, views |
| Sprint 5 — Bonus | Jour 5 matin (15/05) | `feature/bonus` | dashboard, soft deletes, combined filter |
| Sprint 6 — QA & Livrables | Jour 5 (15/05 avant 13h) | `main` | audit, N+1 fix, README, presentation |

> ⚠️ **Before generating any code**, the agent must output:
> `"Active branch: feature/xxx — matches Sprint N scope. Proceeding."`
> If the task belongs to a different sprint's branch, stop and say so until the user confirms.

Allowed branch names — **only** these:
```
main
feature/domains-crud
feature/concepts-crud
feature/ai-generation
feature/bonus
```
Do not create branches with other names without asking the user.

---

### 11.2 Starting a new sprint — mandatory git pull

The agent must emit (or explicitly remind the developer to run) these three commands at the start of every session, before any code:

```bash
# 1. Switch to the correct sprint branch
git checkout <branch-for-this-sprint>

# 2. Pull latest remote changes — NEVER skip this step
git pull origin <branch-for-this-sprint>

# 3. Confirm clean state
git status
```

> ❌ Never start coding on a branch that has not been pulled.
> Skipping `git pull` causes conflicts that waste time.

---

### 11.3 Merging a feature branch into main

Only merge when the sprint's Definition of Done is fully checked. Use this exact sequence:

```bash
# Step 1 — Commit and push all work on the feature branch
git add .
git commit -m "[AI-assisted] feat: complete Sprint N — <branch-name>"
git push origin <feature-branch>

# Step 2 — Switch to main and pull
git checkout main
git pull origin main

# Step 3 — Merge with --no-ff (mandatory — preserves branch history in git log)
git merge --no-ff <feature-branch> -m "merge: Sprint N <feature-branch> → main"

# Step 4 — Push updated main
git push origin main
```

**Rules:**
- Only merge when Definition of Done is fully checked
- Always `--no-ff` — the merge commit must appear in `git log`
- Merge message format: `merge: Sprint N feature/xxx → main`
- **Do not delete** feature branches after merging — needed for evaluation
- Never `git push --force` — always resolve conflicts manually

---

### 11.4 Handling merge conflicts

```bash
git status                      # identify conflicted files
# edit each file to resolve manually, then:
git add <resolved-file>
git commit -m "[manual] fix: resolve merge conflict — <branch> → main"
git push origin main
```

> ❌ Never use `git merge --strategy-option=theirs` or `--force`.

---

### 11.5 Commit message format

```
[AI-assisted] feat: implement Domain CRUD with color badge

- Generated migration, model, controller scaffold via OpenCode
- Manually added user scoping in index() and destroy()
- Manually fixed color validation regex (agent used 'color' rule which doesn't exist in Laravel)
```

```
[manual] fix: resolve N+1 on domains index with withCount
```

```
merge: Sprint 2 feature/domains-crud → main
```

- AI-assisted work → prefix `[AI-assisted]`
- Manual work → prefix `[manual]`
- Merge commits → prefix `merge:` (no AI/manual tag needed)
- **Never commit without a prefix**

---

### 11.6 Minimum commit cadence

| Day | Branch | Min commits |
|-----|--------|-------------|
| Jour 1 — Lundi 11/05 | `main` | 3 (init, AGENTS.md, migrations+models) |
| Jour 2 — Mardi 12/05 | `feature/domains-crud` | 3 |
| Jour 3 — Mercredi 13/05 | `feature/concepts-crud` | 4 |
| Jour 4 — Jeudi 14/05 | `feature/ai-generation` | 4 |
| Jour 5 — Vendredi 15/05 | `main` (merges + QA) | 3 |

**Total minimum: 15 commits across all branches.**
A burst of 15 commits on day 5 is a red flag during evaluation — commits must be spread across days.

---

### 11.7 Quick reference — daily git flow

```bash
# --- Start of every session ---
git checkout <sprint-branch>
git pull origin <sprint-branch>
git status

# --- During work (commit at least once per task) ---
git add .
git commit -m "[AI-assisted] feat: <what was done>"

# --- End of session ---
git push origin <sprint-branch>

# --- End of sprint (DoD fully checked) ---
git checkout main
git pull origin main
git merge --no-ff <sprint-branch> -m "merge: Sprint N <sprint-branch> → main"
git push origin main
```

---

## 12. specs/ Folder Convention

Create one `.md` file per feature **before** building it.

```
specs/
├── 00-setup.md
├── 01-auth.md
├── 02-domains-crud.md
├── 03-concepts-crud.md
├── 04-ai-generation.md
└── 05-dashboard-bonus.md
```

### Required sections in each spec file

```markdown
# Feature: [Name]

## What I want
[precise description of the feature behavior]

## What I do NOT want
[explicit list of things the agent must avoid generating]

## Acceptance criteria
- [ ] criterion 1
- [ ] criterion 2

## Data involved
[models, fields, relationships touched by this feature]

## Routes involved
[HTTP method + URI + controller@method]

## Agent output — what was generated
[fill this AFTER the agent runs — describe what it produced]

## What I changed manually
[fill this AFTER — what you edited and why]
```

---

## 13. What the Agent Must NEVER Do

Exhaustive and permanent. No exception, no matter how the prompt is phrased.

| # | Forbidden | Reason |
|---|-----------|--------|
| 1 | Any API key, secret, or credential in any file | Security |
| 2 | `Http::` calls directly in a controller — always via `GroqService` | Architecture |
| 3 | `$request->validate([...])` inline in controllers | Architecture |
| 4 | `Domain::all()`, `Concept::all()`, `Domain::find($id)`, `Concept::find($id)` unscoped | Security |
| 5 | `DB::statement()`, `DB::select()`, raw SQL | Architecture |
| 6 | Any new npm/JS dependency or Vite config change | Stack |
| 7 | Any `composer require` not already in the project | Stack |
| 8 | `dd()` or `dump()` — use `Log::info()` / `Log::error()` | Code quality |
| 9 | Commented-out code blocks | Code quality |
| 10 | `BaseRepository` or any repository pattern | Architecture |
| 11 | Modifications to the `users` table migration | Breeze ownership |
| 12 | Raw enum values in Blade views — always use accessors | Architecture |
| 13 | `@php` blocks beyond a single `$pct` local var (dashboard only) | Architecture |
| 14 | JavaScript in Blade beyond `onchange="this.form.submit()"` | Stack |
| 15 | `$guarded = []` — always use explicit `$fillable` | Security |
| 16 | Old accessor syntax `getXxxAttribute()` — use `Attribute::make()` | Laravel version |
| 17 | Old FK syntax `$table->foreign()->references()->on()` | Laravel version |
| 18 | `withTrashed()` except archived page and `restore()` | Logic |
| 19 | Hardcoded color values in Blade — always `$domain->color` | Architecture |
| 20 | Any flash session key other than `success` or `error` | Consistency |
| 21 | Routes without `->name()` | Consistency |
| 22 | Hardcoded URI strings in Blade or controllers | Consistency |
| 23 | Bare `@foreach` for lists — always `@forelse` | UX/completeness |
| 24 | Relations used in views without eager-loading in the controller | Performance |
| 25 | Creating Policies or Gates | Architecture |
| 26 | Generating code before completing the Section 0 pre-flight | Workflow |
| 27 | Skipping PLAN mode and jumping straight to BUILD | Workflow |
| 28 | Committing sprint work on the wrong branch | Git |
| 29 | Starting work on a branch without `git pull` | Git |
| 30 | Merging without `--no-ff` (fast-forward merge) | Git |
| 31 | Deleting feature branches after merging | Git |

---

## 14. PLAN Mode vs BUILD Mode

### PLAN Mode

When the user says "Enter PLAN mode" or starts a new feature:
- Do **NOT** generate any code
- List every file you will create or modify
- Describe what each file will contain (method signatures, not full code)
- Flag any ambiguity or risk you see
- Wait for explicit approval before proceeding

### BUILD Mode

Only after the user says "The plan is approved. Enter BUILD mode":
- Generate the full code for every file in the plan
- Follow all rules in this file — no shortcuts
- After generating, summarize what was created and what the user should verify manually

**Never skip PLAN mode.**

### Prompt template for each feature

```
Context: I am building InterviewPrep, a Laravel 13 app. Read AGENTS.md before anything else.

Feature: [feature name]
Spec file: specs/[filename].md
Sprint: [Sprint N]
Active branch: [feature/xxx — confirm this matches the sprint in Section 11.1 before any code]

Current state: [brief description of what already exists]

Task: [specific task for this session]

Constraints:
- Confirm active branch matches sprint scope before generating code
- Use Form Request classes for all validation
- Scope all queries to auth()->user()
- Http:: via GroqService only — never directly in controllers
- Zero N+1 — eager-load all displayed relationships
- No dd() or dump()
- All routes named; use route() helper everywhere
- @forelse for all lists in Blade

What I do NOT want:
- [list specific things based on your spec file]
```

---

## 15. Pre-Commit Checklist

Run through every item before telling the user the code is ready to commit.

**Git hygiene**
- [ ] `git pull origin <current-branch>` was run at the start of this session
- [ ] Current branch matches the sprint scope (Section 11.1)
- [ ] Commit message uses the correct prefix: `[AI-assisted]`, `[manual]`, or `merge:`

**Security**
- [ ] No hardcoded API keys or credentials anywhere
- [ ] `GROQ_API_KEY` only in `.env` — not referenced raw in any `.php` file
- [ ] All routes are inside `Route::middleware('auth')`
- [ ] `abort_if()` ownership check present in every method that touches user data

**Queries**
- [ ] All new queries eager-load relationships — verified with Debugbar, zero N+1
- [ ] All data scoped to `auth()->user()` — no unscoped `::find()` or `::all()`

**Forms & validation**
- [ ] All create/update actions use a Form Request class (no inline `$request->validate()`)
- [ ] `@csrf` present in every form
- [ ] `@method('PATCH')` / `@method('DELETE')` present where needed

**Views**
- [ ] `statusLabel` and `difficultyLabel` used — never raw enum values
- [ ] `@forelse` for all lists (no bare `@foreach`)
- [ ] Flash messages use only `success` or `error` keys
- [ ] All links/redirects use `route('name')` — no hardcoded URIs

**Models**
- [ ] `$fillable` defined on every new or modified model (no `$guarded`)
- [ ] Soft-deleted concepts invisible on all regular pages

**Code quality**
- [ ] No `dd()`, `dump()`, or commented-out code blocks
- [ ] `specs/` file has "Agent output" and "What I changed manually" filled in

**End of sprint (before merging)**
- [ ] Sprint Definition of Done fully checked
- [ ] Merge uses `--no-ff` with correct message format
- [ ] Feature branch NOT deleted after merge