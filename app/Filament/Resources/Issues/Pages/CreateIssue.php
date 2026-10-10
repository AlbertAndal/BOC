<?php

namespace App\Filament\Resources\Issues\Pages;

use App\Filament\Resources\Issues\IssueResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\RecordCreated;
use Filament\Resources\Pages\CreateRecord\RecordSaved;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\HtmlString;
use Throwable;

class CreateIssue extends CreateRecord
{
    protected static string $resource = IssueResource::class;

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Submit Concern')
            ->icon('heroicon-o-paper-airplane');
    }

    public function create(bool $another = false): void
    {
        $this->authorizeAccess();

        try {
            $this->beginDatabaseTransaction();

            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeCreate($data);

            $this->callHook('beforeCreate');

            $this->record = $this->handleRecordCreation($data);

            $this->form->model($this->getRecord())->saveRelationships();

            $this->callHook('afterCreate');

            Event::dispatch(RecordCreated::class, ['record' => $this->record, 'data' => $data, 'page' => $this]);
            Event::dispatch(RecordSaved::class, ['record' => $this->record, 'data' => $data, 'page' => $this]);
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            $this->isCreating = false;

            throw $exception;
        }

        $this->commitDatabaseTransaction();

        $this->rememberData();

        $this->isCreating = false;

        $this->mountAction('submissionSuccess');
    }

    public function submissionSuccessAction(): Action
    {
        return Action::make('submissionSuccess')
            ->modalHeading('Concern Submitted Successfully!')
            ->modalDescription('Your concern has been logged into the BOC Issue Tracking System.')
            ->modalIcon('heroicon-o-check-circle')
            ->modalIconColor('success')
            ->modalWidth('lg')
            ->modalSubmitAction(
                fn (Action $action) => $action
                    ->label('Go to My Issues')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->color('primary')
            )
            ->action(function () {
                $panel = filament()->getCurrentOrDefaultPanel()?->getId();

                if ($panel === 'broker') {
                    return redirect()->to(route('filament.broker.pages.broker-my-issues'));
                }

                return redirect()->to(IssueResource::getUrl('index'));
            })
            ->modalCancelAction(false)
            ->extraModalFooterActions([
                Action::make('viewIssue')
                    ->label('View Issue Details')
                    ->color('gray')
                    ->icon('heroicon-o-eye')
                    ->url(fn (): string => $this->record ? IssueResource::getUrl('view', ['record' => $this->record]) : '#'),

                Action::make('createAnother')
                    ->label('Submit Another Concern')
                    ->color('gray')
                    ->icon('heroicon-o-plus')
                    ->action(function () {
                        $this->fillForm();
                    }),
            ])
            ->schema([
                Placeholder::make('issue_summary')
                    ->hiddenLabel()
                    ->content(function () {
                        $issue = $this->record;

                        if (! $issue) {
                            return '';
                        }

                        $issueNumber = e($issue->issue_number);
                        $dateCreated = e($issue->date_received?->format('M d, Y h:i A') ?? now()->format('M d, Y h:i A'));
                        $importer = e($issue->importer?->importer_name ?? 'N/A');
                        $port = e($issue->port?->port_name ?? 'N/A');

                        return new HtmlString("
                            <div class=\"rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50 p-4 space-y-3\">
                                <div class=\"flex items-center justify-between border-b border-gray-200 dark:border-gray-800 pb-3\">
                                    <div>
                                        <span class=\"text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400\">Barcode / Tracking No.</span>
                                        <div class=\"text-lg font-bold font-mono text-amber-600 dark:text-amber-400\">{$issueNumber}</div>
                                    </div>
                                    <span class=\"inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-800 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300\">
                                        New &bull; Step 1 Complete
                                    </span>
                                </div>

                                <div class=\"grid grid-cols-2 gap-3 text-sm\">
                                    <div>
                                        <span class=\"text-xs text-gray-500 dark:text-gray-400 block\">Date Created</span>
                                        <span class=\"font-medium text-gray-900 dark:text-gray-100\">{$dateCreated}</span>
                                    </div>
                                    <div>
                                        <span class=\"text-xs text-gray-500 dark:text-gray-400 block\">Port of Entry</span>
                                        <span class=\"font-medium text-gray-900 dark:text-gray-100\">{$port}</span>
                                    </div>
                                    <div class=\"col-span-2\">
                                        <span class=\"text-xs text-gray-500 dark:text-gray-400 block\">Importer / Client</span>
                                        <span class=\"font-medium text-gray-900 dark:text-gray-100\">{$importer}</span>
                                    </div>
                                </div>

                                <div class=\"rounded-lg bg-blue-50 dark:bg-blue-950/40 p-3 text-xs text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-900/50\">
                                    <div class=\"font-semibold mb-0.5\">Next Step: Intake Review</div>
                                    Your issue has been queued for BOC Intake Verification. An Accounts Manager will be assigned shortly to assist with your concern.
                                </div>
                            </div>
                        ");
                    }),
            ]);
    }
}
