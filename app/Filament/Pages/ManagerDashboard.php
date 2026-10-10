<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ManagerAssignedIssuesTableWidget;
use App\Filament\Widgets\ManagerStatsOverview;
use App\Models\User;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class ManagerDashboard extends BaseDashboard
{
    protected static ?string $title = 'Manager Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return User::current()?->isAccountsManager() ?? false;
    }

    public function getHeading(): string|Htmlable
    {
        return 'Accounts Manager Dashboard';
    }

    public function getSubheading(): ?string
    {
        return 'Monitor issues assigned to you and move cases through resolution.';
    }

    public function getWidgets(): array
    {
        return [
            ManagerStatsOverview::class,
            ManagerAssignedIssuesTableWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 2;
    }
}
