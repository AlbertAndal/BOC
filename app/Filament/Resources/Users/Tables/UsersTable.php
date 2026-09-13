<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('full_name')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable(),

                TextColumn::make('role.role_name')
                    ->label('Role')
                    ->badge()
                    ->color(fn ($record) => match ($record->role?->role_name) {
                        'Administrator' => 'danger',
                        'Accounts Manager' => 'warning',
                        'Customs Broker' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Active' => 'success',
                        'Inactive' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Registered At')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Active' => 'Active',
                        'Inactive' => 'Inactive (Pending)',
                    ]),
                SelectFilter::make('role_id')
                    ->label('Role')
                    ->relationship('role', 'role_name'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve User Account')
                    ->modalDescription('Approving this user will grant them access to log in to the system.')
                    ->visible(fn ($record) => $record->status === 'Inactive')
                    ->action(function ($record) {
                        $record->update(['status' => 'Active']);
                        Notification::make()
                            ->title('Account Approved')
                            ->body("User {$record->full_name} is now Active.")
                            ->success()
                            ->send();
                    }),

                Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Deactivate User Account')
                    ->modalDescription('Are you sure you want to deactivate this account? They will not be able to log in.')
                    ->visible(fn ($record) => $record->status === 'Active' && $record->user_id !== auth()->id())
                    ->action(function ($record) {
                        $record->update(['status' => 'Inactive']);
                        Notification::make()
                            ->title('Account Deactivated')
                            ->body("User {$record->full_name} has been set to Inactive.")
                            ->warning()
                            ->send();
                    }),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
