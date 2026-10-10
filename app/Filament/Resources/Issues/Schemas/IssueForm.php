<?php

namespace App\Filament\Resources\Issues\Schemas;

use App\Enums\IssueStatus;
use App\Models\Issue;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class IssueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'default' => 1,
                    'lg' => 2,
                ])
                    ->columnSpanFull()
                    ->schema([
                        Group::make([
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
                                        ->label('Date Created')
                                        ->default(now())
                                        ->required(),

                                    Select::make('importer_id')
                                        ->label('Importer')
                                        ->relationship('importer', 'importer_name')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->createOptionForm([
                                            TextInput::make('importer_name')
                                                ->label('Importer / Client Name')
                                                ->required()
                                                ->maxLength(150),
                                            TextInput::make('company')
                                                ->label('Company / Trade Name')
                                                ->maxLength(150),
                                            Textarea::make('contact_information')
                                                ->label('Contact Information')
                                                ->rows(2)
                                                ->maxLength(255),
                                        ])
                                        ->createOptionModalHeading('Add New Importer')
                                        ->createOptionAction(fn ($action) => $action->tooltip('Add new importer if not in list')),

                                    Select::make('port_id')
                                        ->label('Port of Entry')
                                        ->relationship('port', 'port_name')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->createOptionForm([
                                            TextInput::make('port_name')
                                                ->label('Port Name / Code')
                                                ->required()
                                                ->maxLength(150),
                                            TextInput::make('location')
                                                ->label('Location / District')
                                                ->maxLength(150),
                                        ])
                                        ->createOptionModalHeading('Add New Port of Entry')
                                        ->createOptionAction(fn ($action) => $action->tooltip('Add new port of entry if not in list')),
                                ])
                                ->columns(2),

                            Section::make('Assignment & Resolution')
                                ->schema([
                                    Select::make('assigned_manager')
                                        ->label('Assigned Accounts Manager')
                                        ->relationship('assignedManager', 'full_name', fn ($query) => $query->whereHas('role', fn ($q) => $q->where('role_name', 'Accounts Manager')))
                                        ->searchable()
                                        ->preload()
                                        ->visible(fn () => User::current()?->isAdmin()),

                                    Select::make('status_id')
                                        ->label('Status')
                                        ->options(IssueStatus::class)
                                        ->default(IssueStatus::New)
                                        ->required()
                                        ->disabled(fn () => User::current()?->isCustomsBroker()),

                                    DateTimePicker::make('date_resolved')
                                        ->label('Date Resolved')
                                        ->visible(fn () => User::current()?->isAdmin() || User::current()?->isAccountsManager()),

                                    Textarea::make('remarks')
                                        ->label('Additional Notes')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                ])
                                ->columns(2),
                        ])
                            ->columnSpan(1),

                        Group::make([
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
                        ])
                            ->columnSpan(1),
                    ]),
            ]);
    }
}
