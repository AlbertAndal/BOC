<?php

namespace App\Filament\Widgets;

use App\Enums\IssueStatus;
use App\Models\Issue;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BrokerStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()?->isCustomsBroker() ?? false;
    }

    protected function getStats(): array
    {
        $userId = auth()->id();

        $totalSubmitted = Issue::where('created_by', $userId)->count();
        $inProgress = Issue::where('created_by', $userId)
            ->whereIn('status_id', [
                IssueStatus::New,
                IssueStatus::UnderReview,
                IssueStatus::Assigned,
                IssueStatus::InProgress,
                IssueStatus::PendingClient,
                IssueStatus::PendingBocCoordination,
            ])
            ->count();
        $resolved = Issue::where('created_by', $userId)
            ->whereIn('status_id', [IssueStatus::Resolved, IssueStatus::Closed])
            ->count();

        return [
            Stat::make('Total Submitted Concerns', (string) $totalSubmitted)
                ->description('All filed cases')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Active / Under Process', (string) $inProgress)
                ->description('Being investigated by BOC')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Resolved & Cleared', (string) $resolved)
                ->description('Shipments cleared')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}

