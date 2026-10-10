<?php

namespace App\Filament\Resources\Issues\Tables;

use App\Enums\IssueStatus;
use App\Filament\Resources\Issues\IssueResource;
use App\Models\BocOffice;
use App\Models\Issue;
use App\Models\User;
use App\Services\IssueWorkflowService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
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
                    ->label('Date Created')
                    ->dateTime('M d, Y h:i A')
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
                    ->state(function (Issue $record): string {
                        $files = $record->attachments;
                        if (empty($files) || ! is_array($files) || count($files) === 0) {
                            return 'None';
                        }

                        $count = count($files);

                        return $count.' file'.($count > 1 ? 's' : '');
                    })
                    ->tooltip(function (Issue $record): ?string {
                        $files = $record->attachments;
                        if (empty($files) || ! is_array($files) || count($files) === 0) {
                            return null;
                        }

                        return implode(', ', array_map(fn ($file) => basename($file), $files));
                    })
                    ->color(fn (string $state): string => $state === 'None' ? 'gray' : 'success')
                    ->icon(fn (string $state): ?string => $state === 'None' ? null : 'heroicon-o-paper-clip'),

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
            ->recordUrl(fn (Issue $record): string => IssueResource::getUrl('view', ['record' => $record]))
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Issue $record): string => IssueResource::getUrl('view', ['record' => $record])),
                EditAction::make()
                    ->url(fn (Issue $record): string => IssueResource::getUrl('edit', ['record' => $record]))
                    ->visible(fn (Issue $record): bool => User::current()?->can('update', $record) ?? false),

                // 10-Step Workflow Actions Group
                ActionGroup::make([
                    // Step 2: Intake Verification
                    Action::make('step2_verify')
                        ->label('Step 2: Intake Review')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->color('warning')
                        ->visible(fn ($record) => User::current()?->isAdmin() && $record->status_id === IssueStatus::New)
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            IssueWorkflowService::transition($record, 2, remarks: 'Intake documents verified by Administrator.');
                            Notification::make()->title('Issue Moved to Under Review')->success()->send();
                        }),

                    // Step 3: Assign Accounts Manager
                    Action::make('step3_assign')
                        ->label('Step 3: Assign Manager')
                        ->icon('heroicon-o-user-plus')
                        ->color('primary')
                        ->visible(fn ($record) => User::current()?->isAdmin() && in_array($record->status_id, [IssueStatus::New, IssueStatus::UnderReview]))
                        ->schema([
                            Select::make('assigned_manager')
                                ->label('Select Accounts Manager')
                                ->options(User::whereHas('role', fn ($q) => $q->where('role_name', 'Accounts Manager'))->pluck('full_name', 'user_id'))
                                ->required(),
                            Textarea::make('remarks')
                                ->label('Assignment Instructions')
                                ->rows(2),
                        ])
                        ->action(function ($record, array $data) {
                            IssueWorkflowService::transition($record, 3, remarks: $data['remarks'] ?? null, assignedManagerId: $data['assigned_manager']);
                            Notification::make()->title('Issue Assigned to Manager')->success()->send();
                        }),

                    // Step 4: Start Assessment
                    Action::make('step4_start')
                        ->label('Step 4: Start Assessment')
                        ->icon('heroicon-o-play')
                        ->color('info')
                        ->visible(fn ($record) => (User::current()?->isAdmin() || User::current()?->user_id === $record->assigned_manager) && $record->status_id === IssueStatus::Assigned)
                        ->action(function ($record) {
                            IssueWorkflowService::transition($record, 4, remarks: 'Preliminary assessment started by Accounts Manager.');
                            Notification::make()->title('Assessment Started')->success()->send();
                        }),

                    // Step 5: Request Client Information
                    Action::make('step5_request_client')
                        ->label('Step 5: Request Client Info')
                        ->icon('heroicon-o-question-mark-circle')
                        ->color('purple')
                        ->visible(fn ($record) => (User::current()?->isAdmin() || User::current()?->user_id === $record->assigned_manager) && in_array($record->status_id, [IssueStatus::Assigned, IssueStatus::InProgress]))
                        ->schema([
                            Textarea::make('remarks')
                                ->label('Information / Documents Required from Client')
                                ->required(),
                        ])
                        ->action(function ($record, array $data) {
                            IssueWorkflowService::transition($record, 5, remarks: 'Information requested from client: '.$data['remarks']);
                            Notification::make()->title('Status updated to Pending Client')->warning()->send();
                        }),

                    // Step 6: Client Response
                    Action::make('step6_client_response')
                        ->label('Step 6: Log Client Response')
                        ->icon('heroicon-o-arrow-path')
                        ->color('sky')
                        ->visible(fn ($record) => $record->status_id === IssueStatus::PendingClient)
                        ->schema([
                            Textarea::make('remarks')
                                ->label('Client Compliance Notes')
                                ->required(),
                        ])
                        ->action(function ($record, array $data) {
                            IssueWorkflowService::transition($record, 6, remarks: 'Client submitted required compliance: '.$data['remarks']);
                            Notification::make()->title('Issue returned to In Progress')->success()->send();
                        }),

                    // Step 7: Coordinate with BOC Office
                    Action::make('step7_coordinate')
                        ->label('Step 7: Coordinate BOC Office')
                        ->icon('heroicon-o-building-office')
                        ->color('amber')
                        ->visible(fn ($record) => (User::current()?->isAdmin() || User::current()?->user_id === $record->assigned_manager) && in_array($record->status_id, [IssueStatus::Assigned, IssueStatus::InProgress]))
                        ->schema([
                            Select::make('boc_office')
                                ->label('Select BOC Operating Office')
                                ->options(BocOffice::pluck('office_name', 'office_name'))
                                ->required(),
                            TextInput::make('action_taken')
                                ->label('Action / Referral Details')
                                ->required(),
                            Textarea::make('remarks')
                                ->label('Remarks / Turnaround Expectation')
                                ->rows(2),
                        ])
                        ->action(function ($record, array $data) {
                            IssueWorkflowService::transition($record, 7, coordinatedOffice: $data['boc_office'], remarks: $data['action_taken'].' - '.($data['remarks'] ?? ''));
                            Notification::make()->title('Issue endorsed to '.$data['boc_office'])->warning()->send();
                        }),

                    // Step 8: Record Office Findings
                    Action::make('step8_findings')
                        ->label('Step 8: Record Office Findings')
                        ->icon('heroicon-o-document-magnifying-glass')
                        ->color('info')
                        ->visible(fn ($record) => (User::current()?->isAdmin() || User::current()?->user_id === $record->assigned_manager) && $record->status_id === IssueStatus::PendingBocCoordination)
                        ->schema([
                            Textarea::make('findings')
                                ->label('Office Findings / Evaluation Report')
                                ->required(),
                        ])
                        ->action(function ($record, array $data) {
                            IssueWorkflowService::transition($record, 8, remarks: 'BOC Office findings recorded: '.$data['findings']);
                            Notification::make()->title('Office findings recorded. In Progress')->success()->send();
                        }),

                    // Step 9: Resolve Issue
                    Action::make('step9_resolve')
                        ->label('Step 9: Resolve Issue')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn ($record) => (User::current()?->isAdmin() || User::current()?->user_id === $record->assigned_manager) && in_array($record->status_id, [IssueStatus::InProgress, IssueStatus::PendingBocCoordination, IssueStatus::PendingClient]))
                        ->schema([
                            Textarea::make('resolution_notes')
                                ->label('Resolution Summary & Clearance Findings')
                                ->required(),
                        ])
                        ->action(function ($record, array $data) {
                            IssueWorkflowService::transition($record, 9, remarks: 'Issue resolved: '.$data['resolution_notes']);
                            Notification::make()->title('Issue marked as Resolved')->success()->send();
                        }),

                    // Step 10: Close Issue
                    Action::make('step10_close')
                        ->label('Step 10: Final Case Closure')
                        ->icon('heroicon-o-lock-closed')
                        ->color('gray')
                        ->visible(fn ($record) => User::current()?->isAdmin() && $record->status_id === IssueStatus::Resolved)
                        ->requiresConfirmation()
                        ->modalHeading('Close Issue Case')
                        ->modalDescription('Confirming closure marks the audit trail complete.')
                        ->action(function ($record) {
                            IssueWorkflowService::transition($record, 10, remarks: 'Case audited and formally closed.');
                            Notification::make()->title('Case Closed')->success()->send();
                        }),
                ])
                    ->label('Workflow')
                    ->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
