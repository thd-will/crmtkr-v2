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
        $totalCredit = Customer::sum('credit_balance') ?? 0;
        $totalCustomers = Customer::whereHas('creditTransactions')->count();
        $totalTransactions = CreditTransaction::count();
        $monthlyTransactions = CreditTransaction::where('created_at', '>=', now()->startOfMonth())->count();

        return [
            Stat::make('ยอดเครดิตรวม', number_format($totalCredit, 2) . ' ฿')
                ->description('ยอดคงเหลือทั้งหมดในระบบ')
                ->color('primary')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make('ลูกค้าทั้งหมด', number_format($totalCustomers))
                ->description('ที่มีประวัติเครดิต')
                ->color('success')
                ->icon('heroicon-o-user-group'),

            Stat::make('รายการทั้งหมด', number_format($totalTransactions))
                ->description('ตั้งแต่เริ่มใช้งานระบบ')
                ->color('info')
                ->icon('heroicon-o-document-text'),

            Stat::make('รายการเดือนนี้', number_format($monthlyTransactions))
                ->description('ณ วันที่ ' . now()->format('d/m/Y'))
                ->color('warning')
                ->icon('heroicon-o-chart-bar'),
        ];
    }
}
