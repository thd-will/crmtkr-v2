<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\PolicyTicket;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // คำนวนสถิติ
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('is_active', true)->count();
        $totalCredit = Customer::sum('current_credit');
        
        $totalTickets = PolicyTicket::count();
        $completedTickets = PolicyTicket::where('status', 'completed')->count();
        $processingTickets = PolicyTicket::where('status', 'processing')->count();
        
        $totalUsers = User::count();
        
        return [
            Stat::make('ลูกค้าทั้งหมด', $totalCustomers)
                ->description($activeCustomers . ' คนใช้งานอยู่')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            
            Stat::make('เครดิตรวม', '฿' . number_format($totalCredit))
                ->description('เครดิตของลูกค้าทั้งหมด')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning')
                ->chart([15, 4, 10, 2, 12, 4, 12]),
            
            Stat::make('ตั๋วประกัน', $totalTickets)
                ->description($completedTickets . ' เสร็จแล้ว, ' . $processingTickets . ' กำลังดำเนินการ')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info')
                ->chart([3, 2, 5, 3, 6, 4, 8]),
            
            Stat::make('ผู้ใช้งาน', $totalUsers)
                ->description('ผู้ใช้ในระบบ')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->chart([1, 1, 2, 1, 2, 1, 2]),
        ];
    }
}
