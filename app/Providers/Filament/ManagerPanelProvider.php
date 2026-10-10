<?php

namespace App\Providers\Filament;

use App\Filament\Pages\ManagerAssignedIssues;
use App\Filament\Pages\ManagerDashboard;
use App\Filament\Resources\Issues\IssueResource;
use App\Filament\Widgets\ManagerAssignedIssuesTableWidget;
use App\Filament\Widgets\ManagerStatsOverview;
use App\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ManagerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('manager')
            ->path('manager')
            ->brandName('Accounts Manager Portal')
            ->brandLogo(asset('images/boc-logo.png'))
            ->brandLogoHeight('2.5rem')
            ->globalSearch(false)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->resources([
                IssueResource::class,
            ])
            ->pages([
                ManagerDashboard::class,
                ManagerAssignedIssues::class,
            ])
            ->widgets([
                AccountWidget::class,
                ManagerStatsOverview::class,
                ManagerAssignedIssuesTableWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
