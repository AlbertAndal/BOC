<?php

namespace App\Filament\Widgets;

use App\Enums\IssueStatus;
use App\Models\Issue;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected function getStats(): array
    {
        $newCount = Issue::where('status_id', IssueStatus::New)->count();
        $inProgressCount = Issue::where('status_id', IssueStatus::InProgress)->count();
        $pendingCount = Issue::whereIn('status_id', [
            IssueStatus::UnderReview,
            IssueStatus::PendingClient,
            IssueStatus::PendingBocCoordination,
        ])->count();
        $resolvedCount = Issue::where('status_id', IssueStatus::Resolved)->count();
        $closedCount = Issue::where('status_id', IssueStatus::Closed)->count();

        return [
            Stat::make('New Issues', (string) $newCount)
                ->description('Awaiting initial review')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('info'),

            Stat::make('In Progress', (string) $inProgressCount)
                ->description('Active assessments')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('sky'),

            Stat::make('Pending Actions', (string) $pendingCount)
                ->description('Client or BOC Office holds')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Resolved', (string) $resolvedCount)
                ->description('Cases resolved')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Closed', (string) $closedCount)
                ->description('Audit completed')
                ->descriptionIcon('heroicon-m-lock-closed')
                ->color('gray'),
        ];
    }
}

