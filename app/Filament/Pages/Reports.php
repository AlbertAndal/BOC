<?php

namespace App\Filament\Pages;

use App\Enums\IssueStatus;
use App\Filament\Resources\Issues\IssueResource;
use App\Filament\Widgets\ReportsStatsOverview;
use App\Models\BocOffice;
use App\Models\User;
use App\Services\ReportService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class Reports extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string|UnitEnum|null $navigationGroup = 'Reporting & Analytics';

    protected static ?string $navigationLabel = 'Operational Reports';

    protected static ?string $title = 'System Reports & Data Exports';

    protected string $view = 'filament.pages.reports';

    public static function canAccess(): bool
    {
        $user = User::current();

        return $user?->isAdmin() || $user?->isAccountsManager() ?? false;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ReportsStatsOverview::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 4;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('resetFilters')
                ->label('Reset Filters')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function (): void {
                    $this->resetTableFiltersForm();

                    Notification::make()
                        ->title('Filters reset')
                        ->success()
                        ->send();
                }),
            Action::make('exportPdf')
                ->label('Export to PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('danger')
                ->action(fn () => ReportService::exportPdf('issue_summary', $this->getReportFilters())),
            Action::make('exportCsv')
                ->label('Export Excel / CSV')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(fn () => ReportService::exportCsv('issue_summary', $this->getReportFilters())),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(IssueResource::getEloquentQuery())
            ->heading('Issue Summary Report')
            ->description('Review and export the filtered issue register.')
            ->columns([
                TextColumn::make('issue_number')
                    ->label('Issue Number')
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
                    ->limit(50)
                    ->tooltip(fn ($record): string => $record->concern)
                    ->searchable(),
                TextColumn::make('assignedManager.full_name')
                    ->label('Assigned Manager')
                    ->placeholder('Unassigned')
                    ->sortable(),
                TextColumn::make('status_id')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('date_resolved')
                    ->label('Date Resolved')
                    ->date('M d, Y')
                    ->placeholder('Pending'),
            ])
            ->filters([
                SelectFilter::make('status_id')
                    ->label('Status')
                    ->options(IssueStatus::class),
                SelectFilter::make('port_id')
                    ->label('Port')
                    ->relationship('port', 'port_name'),
                SelectFilter::make('importer_id')
                    ->label('Importer')
                    ->relationship('importer', 'importer_name'),
                SelectFilter::make('assigned_manager')
                    ->label('Assigned Manager')
                    ->relationship('assignedManager', 'full_name'),
                SelectFilter::make('boc_office')
                    ->label('BOC Office')
                    ->options(BocOffice::pluck('office_name', 'office_name'))
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'] ?? null,
                        fn (Builder $query, string $value): Builder => $query->whereHas(
                            'actions',
                            fn (Builder $actions): Builder => $actions->where('coordinated_office', $value),
                        ),
                    )),
                SelectFilter::make('year')
                    ->label('Year')
                    ->options(array_combine(
                        range((int) date('Y'), (int) date('Y') - 4),
                        range((int) date('Y'), (int) date('Y') - 4),
                    ))
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'] ?? null,
                        fn (Builder $query, string $value): Builder => $query->whereYear('date_received', $value),
                    )),
                Filter::make('date_received')
                    ->label('Date Received')
                    ->label('Date Created')
                    ->schema([
                        DatePicker::make('from')->label('From'),
                        DatePicker::make('until')->label('Until'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('date_received', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('date_received', '<=', $date))),
            ])
            ->defaultSort('issue_id', 'desc')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getReportFilters(): array
    {
        return [
            'year' => $this->tableFilters['year']['value'] ?? null,
            'port_id' => $this->tableFilters['port_id']['value'] ?? null,
            'importer_id' => $this->tableFilters['importer_id']['value'] ?? null,
            'assigned_manager' => $this->tableFilters['assigned_manager']['value'] ?? null,
            'status_id' => $this->tableFilters['status_id']['value'] ?? null,
            'boc_office' => $this->tableFilters['boc_office']['value'] ?? null,
            'date_from' => $this->tableFilters['date_received']['from'] ?? null,
            'date_to' => $this->tableFilters['date_received']['until'] ?? null,
        ];
    }
}
