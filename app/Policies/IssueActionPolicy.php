<?php

namespace App\Policies;

use App\Models\IssueAction;
use App\Models\User;

class IssueActionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->status === 'Active';
    }

    public function view(User $user, IssueAction $action): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isAccountsManager()) {
            return $action->issue?->assigned_manager === $user->user_id;
        }

        if ($user->isCustomsBroker()) {
            return $action->issue?->created_by === $user->user_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        // Administrators and Accounts Managers can record actions
        return $user->isAdmin() || $user->isAccountsManager();
    }

    public function update(User $user, IssueAction $action): bool
    {
        return $user->isAdmin() || ($user->isAccountsManager() && $action->performed_by === $user->user_id);
    }

    public function delete(User $user, IssueAction $action): bool
    {
        return $user->isAdmin();
    }
}
