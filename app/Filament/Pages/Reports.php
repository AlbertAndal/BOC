<?php

namespace App\Filament\Pages;

use App\Enums\IssueStatus;
use App\Models\BocOffice;
use App\Models\Importer;
use App\Models\Port;
use App\Models\User;
use App\Services\ReportService;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;

class Reports extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string|UnitEnum|null $navigationGroup = 'Reporting & Analytics';

    protected static ?string $navigationLabel = 'Operational Reports';

    protected static ?string $title = 'System Reports & Data Exports';

    protected string $view = 'filament.pages.reports';

    public ?string $report_type = 'issue_summary';
    public ?string $year = null;
    public ?string $month = null;
    public ?int $port_id = null;
    public ?int $importer_id = null;
    public ?int $assigned_manager = null;
    public ?int $status_id = null;
    public ?string $boc_office = null;

    public ?array $reportData = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function mount(): void
    {
        $this->year = (string) date('Y');
        $this->generatePreview();
    }

    public function generatePreview(): void
    {
        $filters = [
            'year' => $this->year,
            'month' => $this->month,
            'port_id' => $this->port_id,
            'importer_id' => $this->importer_id,
            'assigned_manager' => $this->assigned_manager,
            'status_id' => $this->status_id,
            'boc_office' => $this->boc_office,
        ];

        $this->reportData = ReportService::getReportData($this->report_type, $filters);
    }

    public function exportPdf()
    {
        $filters = [
            'year' => $this->year,
            'month' => $this->month,
            'port_id' => $this->port_id,
            'importer_id' => $this->importer_id,
            'assigned_manager' => $this->assigned_manager,
            'status_id' => $this->status_id,
            'boc_office' => $this->boc_office,
        ];

        return ReportService::exportPdf($this->report_type, $filters);
    }

    public function exportCsv()
    {
        $filters = [
            'year' => $this->year,
            'month' => $this->month,
            'port_id' => $this->port_id,
            'importer_id' => $this->importer_id,
            'assigned_manager' => $this->assigned_manager,
            'status_id' => $this->status_id,
            'boc_office' => $this->boc_office,
        ];

        return ReportService::exportCsv($this->report_type, $filters);
    }
}
