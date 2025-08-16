<?php

namespace App\Filament\Widgets;

use App\Models\PolicyTicket;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonthlyRevenueChartWidget extends ChartWidget
{
    protected ?string $heading = 'รายได้รายเดือน - เปรียบเทียบ';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $now = Carbon::now();
        $months = collect(range(5, 0))->map(function ($monthsBack) use ($now) {
            return $now->copy()->subMonths($monthsBack);
        });

        $monthLabels = $months->map(fn ($date) => $date->format('M Y'))->toArray();

        // ยอดเรียกเก็บรายเดือน
        $billableData = $months->map(function ($date) {
            return PolicyTicket::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total_amount') ?? 0;
        })->toArray();

        // ยอดเก็บได้รายเดือน
        $collectedData = $months->map(function ($date) {
            return Payment::where('status', 'confirmed')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount') ?? 0;
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'ยอดเรียกเก็บ',
                    'data' => $billableData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
                [
                    'label' => 'ยอดเก็บได้',
                    'data' => $collectedData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
            ],
            'labels' => $monthLabels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => '(value) => "฿" + value.toLocaleString()',
                    ],
                ],
            ],
            'elements' => [
                'point' => [
                    'radius' => 4,
                    'hoverRadius' => 8,
                ],
            ],
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
        ];
    }
}
