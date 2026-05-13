<?php

namespace Database\Seeders;

use App\Models\Domain;
use App\Models\User;
use Illuminate\Database\Seeder;

class DomainSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $domains = [
            ['name' => 'Laravel', 'color' => '#ef4444'],
            ['name' => 'PHP OOP', 'color' => '#3b82f6'],
            ['name' => 'MySQL', 'color' => '#22c55e'],
        ];

        foreach ($domains as $domain) {
            Domain::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'name' => $domain['name'],
                ],
                ['color' => $domain['color']]
            );
        }
    }
}