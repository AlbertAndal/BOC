<?php

namespace App\Filament\Pages\Auth;

use App\Models\Role;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Auth\Events\Registered;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Register extends BaseRegister
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('full_name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(150),

                TextInput::make('username')
                    ->label('Username')
                    ->required()
                    ->unique(User::class, 'username')
                    ->maxLength(50),

                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->unique(User::class, 'email')
                    ->maxLength(190),

                Select::make('role_id')
                    ->label('Role to Request')
                    ->options(
                        Role::where('role_name', '!=', 'Administrator')
                            ->pluck('role_name', 'role_id')
                    )
                    ->default(function () {
                        return Role::where('role_name', 'Customs Broker')->value('role_id');
                    })
                    ->required(),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable(filament()->arePasswordsRevealable())
                    ->required()
                    ->rule(Password::default())
                    ->same('passwordConfirmation')
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state)),

                TextInput::make('passwordConfirmation')
                    ->label('Confirm Password')
                    ->password()
                    ->revealable(filament()->arePasswordsRevealable())
                    ->required()
                    ->dehydrated(false),
            ]);
    }

    protected function handleRegistration(array $data): Model
    {
        // Require administrator approval: Newly registered users start as 'Inactive'
        $data['status'] = 'Inactive';

        $user = User::create($data);

        // Sync Spatie role
        if ($user->role) {
            $user->syncRoles([$user->role->role_name]);
        }

        return $user;
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        if ($this->isRegisterRateLimited($this->data['email'] ?? '')) {
            return null;
        }

        $user = $this->wrapInDatabaseTransaction(function (): Model {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration($data);

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');

            return $user;
        });

        event(new Registered($user));

        Notification::make()
            ->title('Registration Submitted')
            ->body('Your registration was received. An Administrator must approve your account before you can log in.')
            ->warning()
            ->persistent()
            ->send();

        $this->redirect(filament()->getLoginUrl());

        return null;
    }
}

