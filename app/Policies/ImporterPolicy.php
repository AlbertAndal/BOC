<?php

namespace App\Policies;

use App\Models\Importer;
use App\Models\User;

class ImporterPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->status === 'Active';
    }

    public function view(User $user, Importer $importer): bool
    {
        return $user->status === 'Active';
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Importer $importer): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Importer $importer): bool
    {
        return $user->isAdmin();
    }
}
