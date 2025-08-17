<?php

namespace App\Filament\Widgets;

use App\Models\PolicyTicket;
use Filament\Widgets\ChartWidget;

class PolicyTicketStatusChart extends ChartWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 1;
    
    public function getHeading(): ?string
    {
        return 'สถานะตั๋วประกัน';
    }

    protected function getData(): array
    {
        $statusCounts = PolicyTicket::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statusLabels = [
            'draft' => 'ร่าง',
            'submitted' => 'ส่งให้ทิพย',
            'processing' => 'กำลังดำเนินการ',
            'completed' => 'เสร็จสมบูรณ์',
            'rejected' => 'ถูกปฏิเสธ',
        ];

        $data = [];
        $labels = [];

        foreach ($statusLabels as $status => $label) {
            $count = $statusCounts[$status] ?? 0;
            $data[] = $count;
            $labels[] = $label;
        }

        return [
            'datasets' => [
                [
                    'label' => 'จำนวนตั๋ว',
                    'data' => $data,
                    'backgroundColor' => [
                        '#6B7280', // draft - gray
                        '#3B82F6', // submitted - blue
                        '#F59E0B', // processing - amber
                        '#10B981', // completed - green
                        '#EF4444', // rejected - red
                    ],
                    'borderColor' => [
                        '#4B5563',
                        '#2563EB', 
                        '#D97706',
                        '#059669',
                        '#DC2626',
                    ],
                    'borderWidth' => 1,
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
