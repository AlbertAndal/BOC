<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\AuthPanelProvider;
use App\Providers\Filament\BrokerPanelProvider;
use App\Providers\Filament\ManagerPanelProvider;

return [
    AppServiceProvider::class,
    AuthPanelProvider::class,
    AdminPanelProvider::class,
    ManagerPanelProvider::class,
    BrokerPanelProvider::class,
];
