<?php

namespace App\Filament\Widgets;

use App\Enums\IssueStatus;
use App\Models\Issue;
use Filament\Widgets\ChartWidget;

class IssuesByStatusChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Issues by Current Status';

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected function getData(): array
    {
        $labels = [];
        $data = [];
        $colors = [
            '#3B82F6', // New (Blue)
            '#F59E0B', // Under Review (Amber)
            '#6366F1', // Assigned (Indigo)
            '#0EA5E9', // In Progress (Sky)
            '#A855F7', // Pending Client (Purple)
            '#F97316', // Pending BOC (Orange)
            '#10B981', // Resolved (Green)
            '#6B7280', // Closed (Gray)
            '#EF4444', // Cancelled (Red)
        ];

        foreach (IssueStatus::cases() as $index => $status) {
            $count = Issue::where('status_id', $status)->count();
            $labels[] = $status->getLabel();
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Issues',
                    'data' => $data,
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
