<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\CreditTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CreditStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalCredit = Customer::sum('current_credit') ?? 0;
        $totalUsedCredit = CreditTransaction::where('type', 'debit')->sum('amount') ?? 0;
        $totalCustomers = Customer::whereHas('creditTransactions')->count();
        $totalTransactions = CreditTransaction::count();
        $monthlyTransactions = CreditTransaction::where('created_at', '>=', now()->startOfMonth())->count();

        return [
            Stat::make('ยอดเครดิตคงเหลือ', number_format($totalCredit, 2) . ' ฿')
                ->description('ยอดคงเหลือปัจจุบันทั้งหมด')
                ->color('primary')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make('ยอดเครดิตที่ใช้', number_format($totalUsedCredit, 2) . ' ฿')
                ->description('จากการสร้างตั๋วประกัน')
                ->color('danger')
                ->icon('heroicon-o-arrow-down-circle'),

            Stat::make('ลูกค้าทั้งหมด', number_format($totalCustomers))
                ->description('ที่มีประวัติเครดิต')
                ->color('success')
                ->icon('heroicon-o-user-group'),

            Stat::make('รายการเดือนนี้', number_format($monthlyTransactions))
                ->description('ณ วันที่ ' . now()->format('d/m/Y'))
                ->color('warning')
                ->icon('heroicon-o-chart-bar'),
        ];
    }
}
