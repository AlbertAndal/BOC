<?php

namespace App\Providers;

use App\Http\Responses\LoginResponse;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);

        if (interface_exists('Filament\Http\Responses\Auth\Contracts\LoginResponse')) {
            $this->app->singleton('Filament\Http\Responses\Auth\Contracts\LoginResponse', LoginResponse::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
