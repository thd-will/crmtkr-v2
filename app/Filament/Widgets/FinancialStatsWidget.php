<?php

namespace App\Filament\Widgets;

use App\Models\PolicyTicket;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class FinancialStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // ยอดเรียกเก็บทั้งหมด
        $totalBillable = PolicyTicket::sum('total_amount') ?? 0;
        
        // ยอดเก็บได้แล้ว
        $totalCollected = Payment::where('status', 'confirmed')->sum('amount') ?? 0;
        
        // ยอดค้างชำระ
        $outstanding = $totalBillable - $totalCollected;
        
        // อัตราการเก็บเงิน
        $collectionRate = $totalBillable > 0 ? round(($totalCollected / $totalBillable) * 100, 1) : 0;

        // จำนวน tickets รอชำระ
        $pendingTickets = PolicyTicket::whereIn('payment_status', ['pending', 'partial'])->count();

        return [
            Stat::make('ยอดเรียกเก็บทั้งหมด', '฿' . number_format($totalBillable, 2))
                ->description('ยอดรวมจากการออกตั๋วทั้งหมด')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('ยอดเก็บได้แล้ว', '฿' . number_format($totalCollected, 2))
                ->description('เงินที่ได้รับจริง')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([15, 4, 10, 2, 12, 4, 12])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('ยอดค้างชำระ', '฿' . number_format($outstanding, 2))
                ->description($pendingTickets . ' รายการรอชำระ')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger')
                ->chart([7, 3, 4, 5, 6, 3, 5])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('อัตราการเก็บเงิน', $collectionRate . '%')
                ->description($collectionRate >= 80 ? 'สถานะดี' : ($collectionRate >= 60 ? 'ปานกลาง' : 'ต้องปรับปรุง'))
                ->descriptionIcon($collectionRate >= 80 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($collectionRate >= 80 ? 'success' : ($collectionRate >= 60 ? 'warning' : 'danger'))
                ->chart($collectionRate >= 80 ? [7, 9, 10, 11, 12, 13, 15] : [15, 13, 10, 7, 5, 4, 3])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),
        ];
    }
}
