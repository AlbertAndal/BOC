<?php

namespace App\Filament\Widgets;

use App\Enums\IssueStatus;
use App\Models\Issue;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ManagerStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return User::current()?->isAccountsManager() ?? false;
    }

    protected function getStats(): array
    {
        $userId = User::current()?->user_id;

        $newAssignments = Issue::where('assigned_manager', $userId)
            ->where('status_id', IssueStatus::Assigned)
            ->count();

        $inProgress = Issue::where('assigned_manager', $userId)
            ->where('status_id', IssueStatus::InProgress)
            ->count();

        $pending = Issue::where('assigned_manager', $userId)
            ->whereIn('status_id', [
                IssueStatus::PendingClient,
                IssueStatus::PendingBocCoordination,
            ])
            ->count();

        $resolved = Issue::where('assigned_manager', $userId)
            ->where('status_id', IssueStatus::Resolved)
            ->count();

        $overdue = Issue::where('assigned_manager', $userId)
            ->where('date_received', '<', now()->subDays(7)->toDateString())
            ->whereNotIn('status_id', [
                IssueStatus::Resolved,
                IssueStatus::Closed,
                IssueStatus::Cancelled,
            ])
            ->count();

        return [
            Stat::make('New Assignments', (string) $newAssignments)
                ->description('Awaiting initial action')
                ->descriptionIcon('heroicon-m-inbox-arrow-down')
                ->color('primary'),

            Stat::make('In Progress', (string) $inProgress)
                ->description('Under active assessment')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('sky'),

            Stat::make('Pending Response/Office', (string) $pending)
                ->description('Awaiting client or BOC office')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Resolved Cases', (string) $resolved)
                ->description('Successfully resolved')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Overdue Cases', (string) $overdue)
                ->description('Open for more than 7 days')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
