<?php

namespace App\Http\Middleware;

use Filament\Http\Middleware\Authenticate as BaseAuthenticate;
use Illuminate\Http\Request;

class Authenticate extends BaseAuthenticate
{
    /**
     * @param  Request  $request
     */
    protected function redirectTo($request): ?string
    {
        try {
            return filament()->getPanel('auth')->getLoginUrl() ?? url('/login');
        } catch (\Throwable) {
            return url('/login');
        }
    }
}
