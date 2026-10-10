<?php

namespace App\Filament\Widgets;

use App\Models\BocOffice;
use App\Models\IssueAction;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class IssuesByBocOfficeChart extends ChartWidget
{
    protected static ?int $sort = 6;

    protected ?string $heading = 'Issues Coordinated by BOC Office';

    public static function canView(): bool
    {
        return User::current()?->isAdmin() ?? false;
    }

    protected function getData(): array
    {
        // Query actions with coordinated_office
        $officeCounts = IssueAction::whereNotNull('coordinated_office')
            ->where('coordinated_office', '!=', '')
            ->select('coordinated_office', DB::raw('count(distinct issue_id) as total'))
            ->groupBy('coordinated_office')
            ->orderBy('total', 'desc')
            ->get();

        if ($officeCounts->isEmpty()) {
            $offices = BocOffice::limit(5)->get();
            $labels = $offices->pluck('office_name')->toArray();
            $data = array_fill(0, count($labels), 0);
        } else {
            $labels = $officeCounts->pluck('coordinated_office')->toArray();
            $data = $officeCounts->pluck('total')->toArray();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Issues Coordinated',
                    'data' => $data,
                    'backgroundColor' => '#8B5CF6',
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
