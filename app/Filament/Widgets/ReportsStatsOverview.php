<?php

namespace App\Filament\Widgets;

use App\Models\Importer;
use App\Models\Issue;
use App\Models\Port;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReportsStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        $user = User::current();

        return $user?->isAdmin() || $user?->isAccountsManager() ?? false;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Issue Summary', (string) Issue::count())
                ->description('Total tracked issues')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),
            Stat::make('Port Operational', (string) Port::count())
                ->description('Ports represented in reports')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('success'),
            Stat::make('Importer Concerns', (string) Importer::count())
                ->description('Importers with tracked concerns')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('warning'),
            Stat::make(
                'Manager Performance',
                (string) User::whereHas('role', fn ($query) => $query->where('role_name', 'Accounts Manager'))->count(),
            )
                ->description('Accounts managers available')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
        ];
    }
}
