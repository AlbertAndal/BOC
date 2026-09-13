<?php

namespace App\Filament\Widgets;

use App\Models\Port;
use Filament\Widgets\ChartWidget;

class IssuesByPortChart extends ChartWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Issues by Port of Entry';

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected function getData(): array
    {
        $ports = Port::withCount('issues')->having('issues_count', '>', 0)->get();

        if ($ports->isEmpty()) {
            $ports = Port::withCount('issues')->limit(5)->get();
        }

        $labels = $ports->pluck('port_name')->toArray();
        $data = $ports->pluck('issues_count')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total Issues',
                    'data' => $data,
                    'backgroundColor' => [
                        '#10B981',
                        '#6366F1',
                        '#F59E0B',
                        '#EC4899',
                        '#3B82F6',
                        '#8B5CF6',
                    ],
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
