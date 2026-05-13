<?php

namespace Database\Seeders;

use App\Models\Concept;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConceptSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $domains = Domain::where('user_id', $user->id)->get();

        $concepts = [
            ['title' => 'Routes & Controllers', 'explanation' => 'Les routes définissent les URLs et les contrôleurs gèrent la logique métier.', 'difficulty' => 'junior', 'status' => 'to_review'],
            ['title' => 'Eloquent ORM', 'explanation' => 'Eloquent est l ORM de Laravel pour interagir avec la base de données.', 'difficulty' => 'mid', 'status' => 'in_progress'],
            ['title' => 'Classes abstraites', 'explanation' => 'Les classes abstraites ne peuvent pas être instanciées et peuvent contenir des méthodes abstraites.', 'difficulty' => 'mid', 'status' => 'mastered'],
            ['title' => 'Interfaces', 'explanation' => 'Les interfaces définissent un contrat que les classes doivent implémenter.', 'difficulty' => 'senior', 'status' => 'to_review'],
            ['title' => 'Index & Clés', 'explanation' => 'Les index améliorent les performances des requêtes. Les clés étrangères maintiennent l intégrité référentielle.', 'difficulty' => 'junior', 'status' => 'in_progress'],
            ['title' => 'Jointures SQL', 'explanation' => 'INNER JOIN, LEFT JOIN, RIGHT JOIN permettent de combiner des données de plusieurs tables.', 'difficulty' => 'mid', 'status' => 'mastered'],
        ];

        $conceptIndex = 0;
        foreach ($domains as $domain) {
            for ($i = 0; $i < 2; $i++) {
                $concept = $concepts[$conceptIndex];
                Concept::firstOrCreate(
                    [
                        'domain_id' => $domain->id,
                        'user_id' => $user->id,
                        'title' => $concept['title'],
                    ],
                    [
                        'explanation' => $concept['explanation'],
                        'difficulty' => $concept['difficulty'],
                        'status' => $concept['status'],
                    ]
                );
                $conceptIndex++;
            }
        }
    }
}