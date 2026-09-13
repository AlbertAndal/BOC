<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AdminStatsOverview;
use App\Filament\Widgets\BrokerIssuesTableWidget;
use App\Filament\Widgets\BrokerStatsOverview;
use App\Filament\Widgets\IssuesByBocOfficeChart;
use App\Filament\Widgets\IssuesByImporterChart;
use App\Filament\Widgets\IssuesByMonthChart;
use App\Filament\Widgets\IssuesByPortChart;
use App\Filament\Widgets\IssuesByStatusChart;
use App\Filament\Widgets\ManagerAssignedIssuesTableWidget;
use App\Filament\Widgets\ManagerStatsOverview;
use App\Filament\Widgets\RecentIssuesTableWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    public function getTitle(): string | Htmlable
    {
        $user = auth()->user();

        if ($user?->isAdmin()) {
            return 'Administrator Command Center';
        }

        if ($user?->isAccountsManager()) {
            return 'Accounts Manager Case Workspace';
        }

        if ($user?->isCustomsBroker()) {
            return 'Customs Broker Concerns Portal';
        }

        return 'Dashboard';
    }

    public function getHeading(): string | Htmlable
    {
        $user = auth()->user();
        $name = $user?->full_name ?? 'User';

        if ($user?->isAdmin()) {
            return "Welcome back, {$name} (Administrator)";
        }

        if ($user?->isAccountsManager()) {
            return "Welcome back, {$name} (Accounts Manager)";
        }

        if ($user?->isCustomsBroker()) {
            return "Welcome back, {$name} (Customs Broker)";
        }

        return "Welcome, {$name}";
    }

    public function getSubheading(): ?string
    {
        $user = auth()->user();

        if ($user?->isAdmin()) {
            return 'Comprehensive operational intelligence, port statistics, and issue resolution metrics across all BOC ports.';
        }

        if ($user?->isAccountsManager()) {
            return 'Review, investigate, coordinate, and resolve client concerns currently assigned to your desk.';
        }

        if ($user?->isCustomsBroker()) {
            return 'Track the live clearance and resolution progress of your submitted client concerns.';
        }

        return null;
    }

    public function getWidgets(): array
    {
        $user = auth()->user();

        if ($user?->isAdmin()) {
            return [
                AdminStatsOverview::class,
                IssuesByMonthChart::class,
                IssuesByStatusChart::class,
                IssuesByPortChart::class,
                IssuesByImporterChart::class,
                IssuesByBocOfficeChart::class,
                RecentIssuesTableWidget::class,
            ];
        }

        if ($user?->isAccountsManager()) {
            return [
                ManagerStatsOverview::class,
                ManagerAssignedIssuesTableWidget::class,
            ];
        }

        if ($user?->isCustomsBroker()) {
            return [
                BrokerStatsOverview::class,
                BrokerIssuesTableWidget::class,
            ];
        }

        return [];
    }

    public function getColumns(): int | array
    {
        return 2;
    }
}
