<?php

namespace App\Policies;

use App\Models\GeneratedQuestion;
use App\Models\User;

class GeneratedQuestionPolicy
{
    public function delete(User $user, GeneratedQuestion $question): bool
    {
        return $question->concept->domain->user_id === $user->id;
    }
}
