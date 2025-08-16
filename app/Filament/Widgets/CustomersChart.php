<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class CustomersChart extends ChartWidget
{
    public function getHeading(): ?string
    {
        return 'ลูกค้าใหม่ 7 วันที่ผ่านมา';
    }

    protected function getData(): array
    {
        // สร้างข้อมูล 7 วันที่ผ่านมา
        $data = [];
        $labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = Customer::whereDate('created_at', $date->format('Y-m-d'))->count();
            
            $data[] = $count;
            $labels[] = $date->format('d/m');
        }

        return [
            'datasets' => [
                [
                    'label' => 'ลูกค้าใหม่',
                    'data' => $data,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
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
