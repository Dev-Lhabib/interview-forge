<?php

namespace App\Policies;

use App\Models\Domain;
use App\Models\User;

class DomainPolicy
{
    public function view(User $user, Domain $domain): bool
    {
        return $domain->user_id === $user->id;
    }

    public function update(User $user, Domain $domain): bool
    {
        return $domain->user_id === $user->id;
    }

    public function delete(User $user, Domain $domain): bool
    {
        return $domain->user_id === $user->id;
    }
}
