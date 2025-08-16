<?php

namespace App\Filament\Widgets;

use App\Models\PolicyTicket;
use Filament\Widgets\ChartWidget;

class PolicyTicketStatusChart extends ChartWidget
{
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
        $colors = [];

        foreach ($statusLabels as $status => $label) {
            $count = $statusCounts[$status] ?? 0;
            if ($count > 0) {
                $data[] = $count;
                $labels[] = $label;
                
                // กำหนดสี
                $colors[] = match($status) {
                    'draft' => '#6B7280',
                    'submitted' => '#3B82F6', 
                    'processing' => '#F59E0B',
                    'completed' => '#10B981',
                    'rejected' => '#EF4444',
                };
            }
        }

        return [
            'datasets' => [
                [
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
