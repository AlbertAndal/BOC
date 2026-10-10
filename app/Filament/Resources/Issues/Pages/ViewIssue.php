<?php

namespace App\Filament\Resources\Issues\Pages;

use App\Filament\Resources\Issues\IssueResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewIssue extends ViewRecord
{
    protected static string $resource = IssueResource::class;

    public function getHeading(): string
    {
        return "Issue: {$this->record->issue_number}";
    }

    public function getBreadcrumbs(): array
    {
        $panel = filament()->getCurrentOrDefaultPanel()?->getId();

        if ($panel === 'broker') {
            return [
                route('filament.broker.pages.broker-my-issues') => 'My Issues',
                'View',
            ];
        }

        if ($panel === 'manager') {
            return [
                route('filament.manager.pages.manager-assigned-issues') => 'Assigned Issues',
                'View',
            ];
        }

        return parent::getBreadcrumbs();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to Issues')
                ->icon('heroicon-m-arrow-left')
                ->color('gray')
                ->url(function (): string {
                    $panel = filament()->getCurrentOrDefaultPanel()?->getId();

                    if ($panel === 'broker') {
                        return route('filament.broker.pages.broker-my-issues');
                    }

                    if ($panel === 'manager') {
                        return route('filament.manager.pages.manager-assigned-issues');
                    }

                    return IssueResource::getUrl('index');
                }),

            EditAction::make(),
        ];
    }
}
