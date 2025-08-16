<?php

namespace App\Filament\Widgets;

use App\Models\PolicyTicket;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PaymentStatusChartWidget extends ChartWidget
{
    protected ?string $heading = 'สัดส่วนสถานะการชำระเงิน';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];

    protected function getData(): array
    {
        $statusCounts = PolicyTicket::select('payment_status', DB::raw('count(*) as count'))
            ->groupBy('payment_status')
            ->pluck('count', 'payment_status')
            ->toArray();

        $labels = [];
        $data = [];
        $colors = [];

        $statusMap = [
            'pending' => ['label' => 'รอชำระ', 'color' => '#f59e0b'],
            'paid' => ['label' => 'ชำระแล้ว', 'color' => '#10b981'],
            'partial' => ['label' => 'ชำระบางส่วน', 'color' => '#3b82f6'],
            'overdue' => ['label' => 'เกินกำหนด', 'color' => '#ef4444'],
        ];

        foreach ($statusCounts as $status => $count) {
            $config = $statusMap[$status] ?? ['label' => $status, 'color' => '#6b7280'];
            $labels[] = $config['label'];
            $data[] = $count;
            $colors[] = $config['color'];
        }

        return [
            'datasets' => [
                [
                    'label' => 'จำนวนตั๋ว',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => '(context) => context.label + ": " + context.parsed + " รายการ"',
                    ],
                ],
            ],
            'cutout' => '60%',
            'maintainAspectRatio' => false,
        ];
    }
}
