<?php

namespace App\Filament\Resources\Issues\Schemas;

use App\Enums\IssueStatus;
use App\Models\Issue;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class IssueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Issue Identification & Origin')
                    ->schema([
                        TextInput::make('issue_number')
                            ->label('Issue Number / Barcode')
                            ->default(fn () => Issue::generateIssueNumber())
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->maxLength(50),

                        DatePicker::make('date_received')
                            ->label('Date Received')
                            ->default(now())
                            ->required(),

                        Select::make('importer_id')
                            ->label('Importer')
                            ->relationship('importer', 'importer_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('port_id')
                            ->label('Port of Entry')
                            ->relationship('port', 'port_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Issue Details & Attachments')
                    ->schema([
                        Textarea::make('concern')
                            ->label('Concern / Description')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('benefits_availed')
                            ->label('Benefits Availed (e.g. SGL, AEO, CPRS)')
                            ->placeholder('None or specify programs availed')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        FileUpload::make('attachments')
                            ->label('Attachments / Supporting Documents')
                            ->multiple()
                            ->directory('issue-attachments')
                            ->visibility('public')
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->maxSize(10240)
                            ->columnSpanFull(),
                    ]),

                Section::make('Assignment & Resolution')
                    ->schema([
                        Select::make('assigned_manager')
                            ->label('Assigned Accounts Manager')
                            ->relationship('assignedManager', 'full_name', fn ($query) => $query->whereHas('role', fn ($q) => $q->where('role_name', 'Accounts Manager')))
                            ->searchable()
                            ->preload()
                            ->visible(fn () => auth()->user()?->isAdmin()),

                        Select::make('status_id')
                            ->label('Status')
                            ->options(IssueStatus::class)
                            ->default(IssueStatus::New)
                            ->required()
                            ->disabled(fn () => auth()->user()?->isCustomsBroker()),

                        DateTimePicker::make('date_resolved')
                            ->label('Date Resolved')
                            ->visible(fn () => auth()->user()?->isAdmin() || auth()->user()?->isAccountsManager()),

                        Textarea::make('remarks')
                            ->label('Remarks / Resolution Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
