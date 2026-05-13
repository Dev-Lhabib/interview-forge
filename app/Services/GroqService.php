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
    private string $model = 'llama3-8b-8192';

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