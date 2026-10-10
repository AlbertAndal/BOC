<?php

namespace App\Providers\Filament;

use App\Filament\Pages\BrokerDashboard;
use App\Filament\Pages\BrokerMyIssues;
use App\Filament\Pages\SubmitConcern;
use App\Filament\Resources\Issues\IssueResource;
use App\Filament\Widgets\BrokerIssuesTableWidget;
use App\Filament\Widgets\BrokerStatsOverview;
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

class BrokerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('broker')
            ->path('broker')
            ->brandName('Customs Broker Portal')
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
                BrokerDashboard::class,
                SubmitConcern::class,
                BrokerMyIssues::class,
            ])
            ->widgets([
                AccountWidget::class,
                BrokerStatsOverview::class,
                BrokerIssuesTableWidget::class,
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
