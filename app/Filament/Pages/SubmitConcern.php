<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Issues\IssueResource;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class SubmitConcern extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-plus-circle';

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?string $navigationLabel = 'Submit Concern';

    protected static ?string $title = 'Submit Concern';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return User::current()?->isCustomsBroker() ?? false;
    }

    public function mount(): void
    {
        $this->redirect(IssueResource::getUrl('create', panel: 'broker'));
    }
}
