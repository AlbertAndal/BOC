<?php

namespace App\Policies;

use App\Models\Port;
use App\Models\User;

class PortPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->status === 'Active';
    }

    public function view(User $user, Port $port): bool
    {
        return $user->status === 'Active';
    }

    public function create(User $user): bool
    {
        return $user->status === 'Active';
    }

    public function update(User $user, Port $port): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Port $port): bool
    {
        return $user->isAdmin();
    }
}
