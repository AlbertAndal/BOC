<?php

namespace App\Filament\Resources\Issues\Tables;

use App\Enums\IssueStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class IssuesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('issue_number')
                    ->label('Barcode / Issue #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('date_received')
                    ->label('Date Received')
                    ->date('M d, Y')
                    ->sortable(),

                TextColumn::make('importer.importer_name')
                    ->label('Importer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('port.port_name')
                    ->label('Port')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('concern')
                    ->label('Concern')
                    ->limit(35)
                    ->tooltip(fn ($record) => $record->concern)
                    ->searchable(),

                TextColumn::make('status_id')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('assignedManager.full_name')
                    ->label('Assigned Manager')
                    ->placeholder('Unassigned')
                    ->sortable(),

                TextColumn::make('creator.full_name')
                    ->label('Submitted By')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('attachments')
                    ->label('Files')
                    ->badge()
                    ->formatStateUsing(fn ($state) => is_array($state) ? count($state) . ' file(s)' : 'None')
                    ->color(fn ($state) => (is_array($state) && count($state) > 0) ? 'success' : 'gray'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('issue_id', 'desc')
            ->filters([
                SelectFilter::make('status_id')
                    ->label('Filter by Status')
                    ->options(IssueStatus::class),

                SelectFilter::make('port_id')
                    ->label('Filter by Port')
                    ->relationship('port', 'port_name'),

                SelectFilter::make('importer_id')
                    ->label('Filter by Importer')
                    ->relationship('importer', 'importer_name'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
