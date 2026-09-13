<?php

namespace App\Policies;

use App\Models\BocOffice;
use App\Models\User;

class BocOfficePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->status === 'Active';
    }

    public function view(User $user, BocOffice $office): bool
    {
        return $user->status === 'Active';
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, BocOffice $office): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, BocOffice $office): bool
    {
        return $user->isAdmin();
    }
}
