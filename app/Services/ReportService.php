<?php

namespace App\Services;

use App\Enums\IssueStatus;
use App\Models\BocOffice;
use App\Models\Importer;
use App\Models\Issue;
use App\Models\Port;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportService
{
    /**
     * Apply filter criteria to the Issue query
     */
    public static function applyFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['year'])) {
            $query->whereYear('date_received', $filters['year']);
        }

        if (! empty($filters['month'])) {
            $query->whereMonth('date_received', $filters['month']);
        }

        if (! empty($filters['port_id'])) {
            $query->where('port_id', $filters['port_id']);
        }

        if (! empty($filters['importer_id'])) {
            $query->where('importer_id', $filters['importer_id']);
        }

        if (! empty($filters['assigned_manager'])) {
            $query->where('assigned_manager', $filters['assigned_manager']);
        }

        if (! empty($filters['status_id'])) {
            $query->where('status_id', $filters['status_id']);
        }

        if (! empty($filters['boc_office'])) {
            $query->whereHas('actions', function ($q) use ($filters) {
                $q->where('coordinated_office', $filters['boc_office']);
            });
        }

        return $query;
    }

    /**
     * Get Report Data: returns ['title' => ..., 'headers' => [...], 'rows' => [...], 'appliedFilters' => ...]
     */
    public static function getReportData(string $reportType, array $filters): array
    {
        $filterDescriptions = [];
        if (! empty($filters['year'])) $filterDescriptions[] = "Year: {$filters['year']}";
        if (! empty($filters['month'])) $filterDescriptions[] = "Month: " . date('F', mktime(0, 0, 0, (int)$filters['month'], 10));
        if (! empty($filters['port_id'])) $filterDescriptions[] = "Port: " . (Port::find($filters['port_id'])?->port_name ?? 'N/A');
        if (! empty($filters['importer_id'])) $filterDescriptions[] = "Importer: " . (Importer::find($filters['importer_id'])?->importer_name ?? 'N/A');
        if (! empty($filters['assigned_manager'])) $filterDescriptions[] = "Manager: " . (User::find($filters['assigned_manager'])?->full_name ?? 'N/A');
        if (! empty($filters['status_id'])) {
            $statusEnum = $filters['status_id'] instanceof IssueStatus ? $filters['status_id'] : IssueStatus::tryFrom((int)$filters['status_id']);
            $filterDescriptions[] = "Status: " . ($statusEnum?->getLabel() ?? 'N/A');
        }
        if (! empty($filters['boc_office'])) $filterDescriptions[] = "BOC Office: {$filters['boc_office']}";

        $appliedFiltersString = empty($filterDescriptions) ? 'All records (No filters applied)' : implode(' | ', $filterDescriptions);

        return match ($reportType) {
            'port_report' => static::buildPortReport($filters, $appliedFiltersString),
            'importer_report' => static::buildImporterReport($filters, $appliedFiltersString),
            'manager_performance' => static::buildManagerPerformanceReport($filters, $appliedFiltersString),
            default => static::buildIssueSummaryReport($filters, $appliedFiltersString),
        };
    }

    /**
     * 1. Issue Summary Report
     */
    protected static function buildIssueSummaryReport(array $filters, string $appliedFiltersString): array
    {
        $query = Issue::with(['importer', 'port', 'assignedManager', 'creator']);
        $query = static::applyFilters($query, $filters)->latest('issue_id');
        $issues = $query->get();

        $headers = [
            'Issue Number',
            'Date Received',
            'Importer',
            'Port',
            'Concern',
            'Assigned Manager',
            'Status',
            'Date Resolved',
        ];

        $rows = [];
        foreach ($issues as $issue) {
            $rows[] = [
                $issue->issue_number,
                $issue->date_received?->format('M d, Y') ?? '—',
                $issue->importer?->importer_name ?? '—',
                $issue->port?->port_name ?? '—',
                \Illuminate\Support\Str::limit($issue->concern, 50),
                $issue->assignedManager?->full_name ?? 'Unassigned',
                $issue->status_id?->getLabel() ?? 'Unknown',
                $issue->date_resolved?->format('M d, Y') ?? 'Pending',
            ];
        }

        return [
            'title' => 'Issue Summary Report',
            'headers' => $headers,
            'rows' => $rows,
            'appliedFilters' => $appliedFiltersString,
        ];
    }

    /**
     * 2. Port Report
     */
    protected static function buildPortReport(array $filters, string $appliedFiltersString): array
    {
        $portsQuery = Port::query();
        if (! empty($filters['port_id'])) {
            $portsQuery->where('port_id', $filters['port_id']);
        }
        $ports = $portsQuery->get();

        $headers = [
            'Port Name',
            'Location',
            'Total Issues',
            'Resolved',
            'In Progress',
            'Pending Actions',
            'Resolution Rate',
        ];

        $rows = [];
        foreach ($ports as $port) {
            $baseQuery = Issue::where('port_id', $port->port_id);
            $baseQuery = static::applyFilters($baseQuery, $filters);

            $total = (clone $baseQuery)->count();
            $resolved = (clone $baseQuery)->where('status_id', IssueStatus::Resolved)->count();
            $inProgress = (clone $baseQuery)->where('status_id', IssueStatus::InProgress)->count();
            $pending = (clone $baseQuery)->whereIn('status_id', [
                IssueStatus::New,
                IssueStatus::UnderReview,
                IssueStatus::Assigned,
                IssueStatus::PendingClient,
                IssueStatus::PendingBocCoordination,
            ])->count();

            $rate = $total > 0 ? round(($resolved / $total) * 100, 1) . '%' : '0.0%';

            $rows[] = [
                $port->port_name,
                $port->location ?? '—',
                $total,
                $resolved,
                $inProgress,
                $pending,
                $rate,
            ];
        }

        return [
            'title' => 'Port Operational Issue Report',
            'headers' => $headers,
            'rows' => $rows,
            'appliedFilters' => $appliedFiltersString,
        ];
    }

    /**
     * 3. Importer Report
     */
    protected static function buildImporterReport(array $filters, string $appliedFiltersString): array
    {
        $importersQuery = Importer::query();
        if (! empty($filters['importer_id'])) {
            $importersQuery->where('importer_id', $filters['importer_id']);
        }
        $importers = $importersQuery->get();

        $headers = [
            'Importer Name',
            'Company',
            'Total Concerns Filed',
            'Resolved',
            'In Progress',
            'Pending Actions',
        ];

        $rows = [];
        foreach ($importers as $importer) {
            $baseQuery = Issue::where('importer_id', $importer->importer_id);
            $baseQuery = static::applyFilters($baseQuery, $filters);

            $total = (clone $baseQuery)->count();
            $resolved = (clone $baseQuery)->where('status_id', IssueStatus::Resolved)->count();
            $inProgress = (clone $baseQuery)->where('status_id', IssueStatus::InProgress)->count();
            $pending = (clone $baseQuery)->whereIn('status_id', [
                IssueStatus::New,
                IssueStatus::UnderReview,
                IssueStatus::Assigned,
                IssueStatus::PendingClient,
                IssueStatus::PendingBocCoordination,
            ])->count();

            $rows[] = [
                $importer->importer_name,
                $importer->company ?? '—',
                $total,
                $resolved,
                $inProgress,
                $pending,
            ];
        }

        return [
            'title' => 'Importer Concern & Resolution Report',
            'headers' => $headers,
            'rows' => $rows,
            'appliedFilters' => $appliedFiltersString,
        ];
    }

    /**
     * 4. Manager Performance Report
     */
    protected static function buildManagerPerformanceReport(array $filters, string $appliedFiltersString): array
    {
        $managersQuery = User::whereHas('role', fn ($q) => $q->where('role_name', 'Accounts Manager'));
        if (! empty($filters['assigned_manager'])) {
            $managersQuery->where('user_id', $filters['assigned_manager']);
        }
        $managers = $managersQuery->get();

        $headers = [
            'Accounts Manager',
            'Total Assigned',
            'Resolved',
            'In Progress',
            'Overdue (> 7 Days)',
            'Avg Resolution Time',
        ];

        $rows = [];
        foreach ($managers as $mgr) {
            $baseQuery = Issue::where('assigned_manager', $mgr->user_id);
            $baseQuery = static::applyFilters($baseQuery, $filters);

            $total = (clone $baseQuery)->count();
            $resolved = (clone $baseQuery)->where('status_id', IssueStatus::Resolved)->count();
            $inProgress = (clone $baseQuery)->where('status_id', IssueStatus::InProgress)->count();
            $overdue = (clone $baseQuery)
                ->where('date_received', '<', now()->subDays(7)->toDateString())
                ->whereNotIn('status_id', [IssueStatus::Resolved, IssueStatus::Closed, IssueStatus::Cancelled])
                ->count();

            // Calculate average resolution time
            $resolvedIssues = (clone $baseQuery)
                ->whereNotNull('date_resolved')
                ->where('status_id', IssueStatus::Resolved)
                ->get();

            if ($resolvedIssues->count() > 0) {
                $totalDays = 0;
                foreach ($resolvedIssues as $resIssue) {
                    $totalDays += $resIssue->date_received->diffInDays($resIssue->date_resolved);
                }
                $avgDays = round($totalDays / $resolvedIssues->count(), 1) . ' days';
            } else {
                $avgDays = 'N/A';
            }

            $rows[] = [
                $mgr->full_name,
                $total,
                $resolved,
                $inProgress,
                $overdue,
                $avgDays,
            ];
        }

        return [
            'title' => 'Accounts Manager Performance & Turnaround Report',
            'headers' => $headers,
            'rows' => $rows,
            'appliedFilters' => $appliedFiltersString,
        ];
    }

    /**
     * Generate PDF stream / download response
     */
    public static function exportPdf(string $reportType, array $filters)
    {
        $data = static::getReportData($reportType, $filters);

        $pdf = Pdf::loadView('reports.pdf', $data)
            ->setPaper('a4', 'landscape');

        $filename = str_replace(' ', '_', strtolower($data['title'])) . '_' . date('Ymd_His') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Generate CSV / Excel stream download response
     */
    public static function exportCsv(string $reportType, array $filters): StreamedResponse
    {
        $data = static::getReportData($reportType, $filters);
        $filename = str_replace(' ', '_', strtolower($data['title'])) . '_' . date('Ymd_His') . '.csv';

        $callback = function () use ($data) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Write report title and filters
            fputcsv($handle, [$data['title']]);
            fputcsv($handle, ['Generated At: ' . now()->format('Y-m-d H:i:s')]);
            fputcsv($handle, ['Filters: ' . $data['appliedFilters']]);
            fputcsv($handle, []); // empty row

            // Write Headers
            fputcsv($handle, $data['headers']);

            // Write Data Rows
            foreach ($data['rows'] as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
