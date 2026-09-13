<?php

namespace App\Filament\Resources\Issues\RelationManagers;

use App\Models\BocOffice;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActionsRelationManager extends RelationManager
{
    protected static string $relationship = 'actions';

    protected static ?string $title = 'Action History & Audit Trail';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('action_taken')
                    ->label('Action Performed / Description')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Select::make('coordinated_office')
                    ->label('Coordinated BOC Office')
                    ->options(BocOffice::pluck('office_name', 'office_name'))
                    ->searchable()
                    ->nullable(),

                DatePicker::make('action_date')
                    ->label('Action Date')
                    ->default(now())
                    ->required(),

                Textarea::make('remarks')
                    ->label('Remarks / Additional Notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('action_taken')
            ->columns([
                TextColumn::make('action_date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),

                TextColumn::make('action_taken')
                    ->label('Action Taken / Timeline Event')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('coordinated_office')
                    ->label('BOC Office')
                    ->badge()
                    ->color('amber')
                    ->placeholder('None')
                    ->sortable(),

                TextColumn::make('performer.full_name')
                    ->label('Recorded By')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('remarks')
                    ->label('Remarks')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->remarks)
                    ->placeholder('—'),
            ])
            ->defaultSort('action_id', 'desc')
            ->headerActions([
                CreateAction::make('log_action')
                    ->label('Record New Action')
                    ->icon('heroicon-o-plus-circle')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['performed_by'] = auth()->id();
                        $timeStr = now()->format('F j, Y - g:i A');
                        $roleName = auth()->user()?->role?->role_name ?? 'User';
                        $userName = auth()->user()?->full_name ?? 'User';
                        $data['action_taken'] = "{$timeStr}: {$data['action_taken']} by {$roleName} ({$userName})";

                        return $data;
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
