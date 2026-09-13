<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Issues\IssueResource;
use App\Models\Issue;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentIssuesTableWidget extends BaseWidget
{
    protected static ?int $sort = 7;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(IssueResource::getEloquentQuery()->latest('issue_id')->limit(10))
            ->heading('Recent Issues Overview')
            ->columns([
                TextColumn::make('issue_number')
                    ->label('Barcode / Issue #')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('date_received')
                    ->label('Date')
                    ->date('M d, Y'),

                TextColumn::make('importer.importer_name')
                    ->label('Importer'),

                TextColumn::make('port.port_name')
                    ->label('Port'),

                TextColumn::make('status_id')
                    ->label('Status')
                    ->badge(),

                TextColumn::make('assignedManager.full_name')
                    ->label('Assigned Manager')
                    ->placeholder('Unassigned'),

                TextColumn::make('creator.full_name')
                    ->label('Submitted By'),
            ])
            ->recordActions([
                ViewAction::make()->url(fn ($record) => IssueResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
