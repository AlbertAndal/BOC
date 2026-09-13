<?php

namespace App\Policies;

use App\Models\Issue;
use App\Models\User;

class IssuePolicy
{
    /**
     * Administrators have full access.
     * Accounts Managers can view their assigned issues.
     * Customs Brokers can view their own submitted issues.
     */
    public function viewAny(User $user): bool
    {
        return $user->status === 'Active';
    }

    public function view(User $user, Issue $issue): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isAccountsManager()) {
            return $issue->assigned_manager === $user->user_id;
        }

        if ($user->isCustomsBroker()) {
            return $issue->created_by === $user->user_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        // Administrators and Customs Brokers can submit issues
        return $user->isAdmin() || $user->isCustomsBroker();
    }

    public function update(User $user, Issue $issue): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Accounts Managers can update assigned issues (e.g. status, remarks)
        if ($user->isAccountsManager()) {
            return $issue->assigned_manager === $user->user_id;
        }

        return false;
    }

    public function delete(User $user, Issue $issue): bool
    {
        return $user->isAdmin();
    }
}

