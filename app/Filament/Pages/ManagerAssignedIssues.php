<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Issues\Tables\IssuesTable;
use App\Models\Issue;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use UnitEnum;

class ManagerAssignedIssues extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?string $navigationLabel = 'My Assigned Issues';

    protected static ?string $title = 'My Assigned Issues';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.manager-assigned-issues';

    public static function canAccess(): bool
    {
        return User::current()?->isAccountsManager() ?? false;
    }

    public function table(Table $table): Table
    {
        return IssuesTable::configure($table)
            ->query(Issue::query()->where('assigned_manager', User::current()?->user_id)->latest('issue_id'))
            ->heading('Issues Assigned to Me')
            ->description('Review assigned concerns and use the available workflow actions to update them.');
    }
}
