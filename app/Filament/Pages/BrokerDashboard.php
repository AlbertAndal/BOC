<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BrokerIssuesTableWidget;
use App\Filament\Widgets\BrokerStatsOverview;
use App\Models\User;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class BrokerDashboard extends BaseDashboard
{
    protected static ?string $title = 'Broker Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return User::current()?->isCustomsBroker() ?? false;
    }

    public function getHeading(): string|Htmlable
    {
        return 'Customs Broker Dashboard';
    }

    public function getSubheading(): ?string
    {
        return 'Track your submitted concerns and monitor their resolution status.';
    }

    public function getWidgets(): array
    {
        return [
            BrokerStatsOverview::class,
            BrokerIssuesTableWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 2;
    }
}
