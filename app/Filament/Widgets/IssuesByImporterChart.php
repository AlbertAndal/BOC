<?php

namespace App\Filament\Widgets;

use App\Models\Importer;
use App\Models\User;
use Filament\Widgets\ChartWidget;

class IssuesByImporterChart extends ChartWidget
{
    protected static ?int $sort = 5;

    protected ?string $heading = 'Issues by Importer / Client';

    public static function canView(): bool
    {
        return User::current()?->isAdmin() ?? false;
    }

    protected function getData(): array
    {
        $importers = Importer::withCount('issues')
            ->orderBy('issues_count', 'desc')
            ->limit(6)
            ->get();

        $labels = $importers->pluck('importer_name')->toArray();
        $data = $importers->pluck('issues_count')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total Issues',
                    'data' => $data,
                    'backgroundColor' => '#F59E0B',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
