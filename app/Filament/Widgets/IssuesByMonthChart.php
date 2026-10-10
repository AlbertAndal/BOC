<?php

namespace App\Filament\Widgets;

use App\Models\Issue;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class IssuesByMonthChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Issues Received by Month';

    public static function canView(): bool
    {
        return User::current()?->isAdmin() ?? false;
    }

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(function ($i) {
            return Carbon::now()->subMonths($i);
        });

        $labels = [];
        $data = [];

        foreach ($months as $month) {
            $yearMonth = $month->format('Y-m');
            $labels[] = $month->format('M Y');

            $count = Issue::where(DB::raw("DATE_FORMAT(date_received, '%Y-%m')"), $yearMonth)->count();
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Issues Received',
                    'data' => $data,
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
