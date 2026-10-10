<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Issues\IssueResource;
use App\Models\Issue;
use App\Models\User;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class BrokerIssuesTableWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return User::current()?->isCustomsBroker() ?? false;
    }

    public function table(Table $table): Table
    {
        $userId = User::current()?->user_id;

        return $table
            ->query(
                Issue::where('created_by', $userId)->latest('issue_id')
            )
            ->heading('My Submitted Issues & Resolution Status')
            ->columns([
                TextColumn::make('issue_number')
                    ->label('Barcode / Issue #')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('date_received')
                    ->label('Date Filed')
                    ->date('M d, Y')
                    ->label('Date Created')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),

                TextColumn::make('importer.importer_name')
                    ->label('Importer / Client')
                    ->searchable(),

                TextColumn::make('port.port_name')
                    ->label('Port of Entry'),

                TextColumn::make('concern')
                    ->label('Concern Summary')
                    ->limit(45)
                    ->tooltip(fn ($record) => $record->concern),

                TextColumn::make('status_id')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('date_resolved')
                    ->label('Date Resolved')
                    ->placeholder('Pending resolution')
                    ->dateTime('M d, Y'),
            ])
            ->recordActions([
                ViewAction::make()->url(fn ($record) => IssueResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
