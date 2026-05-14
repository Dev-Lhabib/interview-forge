<?php

namespace App\Policies;

use App\Models\Concept;
use App\Models\User;

class ConceptPolicy
{
    public function view(User $user, Concept $concept): bool
    {
        return $concept->domain->user_id === $user->id;
    }

    public function update(User $user, Concept $concept): bool
    {
        return $concept->domain->user_id === $user->id;
    }

    public function delete(User $user, Concept $concept): bool
    {
        return $concept->domain->user_id === $user->id;
    }

    public function restore(User $user, Concept $concept): bool
    {
        return $concept->domain->user_id === $user->id;
    }
}
