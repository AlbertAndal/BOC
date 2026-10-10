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

class BrokerMyIssues extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?string $navigationLabel = 'My Issues';

    protected static ?string $title = 'My Issues';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.broker-my-issues';

    public static function canAccess(): bool
    {
        return User::current()?->isCustomsBroker() ?? false;
    }

    public function table(Table $table): Table
    {
        return IssuesTable::configure($table)
            ->query(Issue::query()->where('created_by', User::current()?->user_id)->latest('issue_id'))
            ->heading('My Submitted Issues')
            ->description('Track the current status and resolution progress of your concerns.');
    }
}
