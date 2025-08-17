<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\CreditTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class AlertsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $alerts = [];

        // ตรวจสอบลูกค้าที่มีเครดิตต่ำ
        $lowCreditCount = Customer::where('current_credit', '<', 1000)->count();
        
        if ($lowCreditCount > 0) {
            $alerts[] = Stat::make('เครดิตเหลือน้อย', $lowCreditCount . ' ราย')
                ->description('ลูกค้าที่มีเครดิตต่ำกว่า 1,000 บาท')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning');
        }

        // ตรวจสอบลูกค้าที่มีเครดิตติดลบ  
        $negativeCredit = Customer::where('current_credit', '<', 0)->count();
        
        if ($negativeCredit > 0) {
            $alerts[] = Stat::make('เครดิตติดลบ', $negativeCredit . ' ราย')
                ->description('ลูกค้าที่มีเครดิตติดลบ')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger');
        }

        // รายการเครดิตสูงวันนี้
        $highTransactionsCount = CreditTransaction::where('created_at', '>=', Carbon::today())
            ->where('amount', '>', 50000)
            ->count();
            
        if ($highTransactionsCount > 0) {
            $alerts[] = Stat::make('รายการเครดิตสูง', $highTransactionsCount . ' รายการ')
                ->description('รายการเกิน 50,000 บาทวันนี้')
                ->descriptionIcon('heroicon-m-information-circle')
                ->color('info');
        }

        // ถ้าไม่มี alert ใดๆ ไม่แสดงอะไรเลย (return empty array)
        if (empty($alerts)) {
            return [];
        }

        return $alerts;
    }
}
