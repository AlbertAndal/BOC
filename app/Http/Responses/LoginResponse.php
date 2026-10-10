<?php

namespace App\Http\Responses;

use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements LoginResponseContract
{
    /**
     * @param  Request  $request
     */
    public function toResponse($request): RedirectResponse|Redirector
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            return redirect('/');
        }

        if ($user->isAdmin()) {
            return redirect('/admin');
        }

        if ($user->isAccountsManager()) {
            return redirect('/manager');
        }

        if ($user->isCustomsBroker()) {
            return redirect('/broker');
        }

        return redirect('/');
    }
}
