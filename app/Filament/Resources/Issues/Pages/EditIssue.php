<?php

namespace App\Filament\Resources\Issues\Pages;

use App\Filament\Resources\Issues\IssueResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditIssue extends EditRecord
{
    protected static string $resource = IssueResource::class;

    public function getBreadcrumbs(): array
    {
        $panel = filament()->getCurrentOrDefaultPanel()?->getId();

        if ($panel === 'broker') {
            return [
                route('filament.broker.pages.broker-my-issues') => 'My Issues',
                'Edit',
            ];
        }

        if ($panel === 'manager') {
            return [
                route('filament.manager.pages.manager-assigned-issues') => 'Assigned Issues',
                'Edit',
            ];
        }

        return parent::getBreadcrumbs();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
