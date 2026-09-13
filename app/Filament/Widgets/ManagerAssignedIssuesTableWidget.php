<?php

namespace App\Filament\Widgets;

use App\Enums\IssueStatus;
use App\Filament\Resources\Issues\IssueResource;
use App\Models\Issue;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ManagerAssignedIssuesTableWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->isAccountsManager() ?? false;
    }

    public function table(Table $table): Table
    {
        $userId = auth()->id();

        return $table
            ->query(
                Issue::where('assigned_manager', $userId)->latest('issue_id')
            )
            ->heading('My Assigned Cases & Active Investigations')
            ->columns([
                TextColumn::make('issue_number')
                    ->label('Barcode / Issue #')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('date_received')
                    ->label('Received')
                    ->date('M d, Y')
                    ->sortable(),

                TextColumn::make('importer.importer_name')
                    ->label('Importer / Client')
                    ->searchable(),

                TextColumn::make('port.port_name')
                    ->label('Port of Entry'),

                TextColumn::make('concern')
                    ->label('Concern Summary')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->concern),

                TextColumn::make('status_id')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('creator.full_name')
                    ->label('Customs Broker'),
            ])
            ->filters([
                SelectFilter::make('status_id')
                    ->label('Filter by Status')
                    ->options(IssueStatus::class),
            ])
            ->recordActions([
                ViewAction::make()->url(fn ($record) => IssueResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
