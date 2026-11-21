<?php

namespace App\Filament\Widgets;

use App\Enums\IssueStatus;
use App\Models\Issue;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return User::current()?->isAdmin() ?? false;
    }

    protected function getStats(): array
    {
        $count = fn (IssueStatus $status): string => (string) Issue::where('status_id', $status)->count();

        return [
            Stat::make('New', $count(IssueStatus::New))
                ->description('Awaiting initial review')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color(IssueStatus::New->getColor()),

            Stat::make('Under Review', $count(IssueStatus::UnderReview))
                ->description('Intake verification')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color(IssueStatus::UnderReview->getColor()),

            Stat::make('Assigned', $count(IssueStatus::Assigned))
                ->description('With an accounts manager')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color(IssueStatus::Assigned->getColor()),

            Stat::make('In Progress', $count(IssueStatus::InProgress))
                ->description('Active assessments')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color(IssueStatus::InProgress->getColor()),

            Stat::make('Pending Coordination', $count(IssueStatus::PendingCoordination))
                ->description('Waiting for client or BOC office')
                ->descriptionIcon('heroicon-m-clock')
                ->color(IssueStatus::PendingCoordination->getColor()),

            Stat::make('Resolved', $count(IssueStatus::Resolved))
                ->description('Cases resolved')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color(IssueStatus::Resolved->getColor()),

            Stat::make('Closed', $count(IssueStatus::Closed))
                ->description('Cases closed')
                ->descriptionIcon('heroicon-m-lock-closed')
                ->color(IssueStatus::Closed->getColor()),

            Stat::make('Cancelled', $count(IssueStatus::Cancelled))
                ->description('Cases cancelled')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color(IssueStatus::Cancelled->getColor()),
        ];
    }
}
