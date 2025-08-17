<?php

namespace App\Filament\Widgets;

use App\Models\PolicyTicket;
use App\Models\Payment;
use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinancialReportStats extends BaseWidget
{
    protected function getStats(): array
    {
        // ข้อมูลตามที่ขอ
        
        // 1. จำนวนเงินที่เรียกเก็บ (ยอดรวมที่ควรได้รับ)
        $totalAmountToCollect = Payment::sum('amount') ?? 0;
        
        // 2. จำนวนเงินที่เก็บเงินได้ (confirmed payments)
        $collectedAmount = Payment::where('status', 'confirmed')->sum('amount') ?? 0;
        
        // 3. จำนวนรายการที่สร้าง (PolicyTickets)
        $totalCreatedItems = PolicyTicket::count() ?? 0;
        
        // 4. จำนวนลูกค้าในรายการที่สร้าง (unique customers in PolicyTickets)
        $uniqueCustomers = PolicyTicket::distinct('customer_id')->count('customer_id') ?? 0;
        
        // คำนวณอัตราการเก็บเงิน
        $collectionRate = $totalAmountToCollect > 0 ? round(($collectedAmount / $totalAmountToCollect) * 100, 1) : 0;
        
        // กราฟข้อมูล 7 วันย้อนหลัง
        $collectionChart = [];
        $creationChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            
            // การเก็บเงินรายวัน
            $dailyCollection = Payment::where('status', 'confirmed')->whereDate('created_at', $date)->sum('amount') ?? 0;
            $collectionChart[] = max(1, $dailyCollection / 1000);
            
            // การสร้างรายการรายวัน
            $dailyCreation = PolicyTicket::whereDate('created_at', $date)->count() ?? 0;
            $creationChart[] = max(1, $dailyCreation);
        }
        
        return [
            Stat::make('จำนวนเงินที่เรียกเก็บ', '฿' . number_format($totalAmountToCollect, 0))
                ->description('ยอดรวมที่ต้องเรียกเก็บทั้งหมด')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info')
                ->chart($collectionChart),
            
            Stat::make('จำนวนเงินที่เก็บได้', '฿' . number_format($collectedAmount, 0))
                ->description($collectionRate . '% ของยอดที่ต้องเก็บ')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart($collectionChart),
            
            Stat::make('จำนวนรายการที่สร้าง', number_format($totalCreatedItems) . ' รายการ')
                ->description('PolicyTickets ที่สร้างทั้งหมด')
                ->descriptionIcon('heroicon-m-document-plus')
                ->color('primary')
                ->chart($creationChart),
            
            Stat::make('จำนวนลูกค้าในรายการ', number_format($uniqueCustomers) . ' คน')
                ->description('ลูกค้าที่มีรายการอย่างน้อย 1 ฉบับ')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning')
                ->chart([1, 2, 3, 2, 4, 3, max(1, $uniqueCustomers)]),
        ];
    }
}
