<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ActivityStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // สถิติกิจกรรมวันนี้
        $todayActivities = ActivityLog::whereDate('created_at', today())->count();
        
        // สถิติกิจกรรมสัปดาห์นี้
        $weekActivities = ActivityLog::whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();
        
        // สถิติลูกค้าใหม่วันนี้
        $newCustomersToday = ActivityLog::where('description', 'สร้างลูกค้าใหม่')
            ->whereDate('created_at', today())
            ->count();
            
        // สถิติการแก้ไขวันนี้
        $updatesThisWeek = ActivityLog::where('description', 'แก้ไขข้อมูลลูกค้า')
            ->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->count();

        return [
            Stat::make('กิจกรรมวันนี้', $todayActivities)
                ->description('การกระทำทั้งหมดวันนี้')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),
                
            Stat::make('กิจกรรมสัปดาห์นี้', $weekActivities)
                ->description('การกระทำทั้งหมดสัปดาห์นี้')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),
                
            Stat::make('ลูกค้าใหม่วันนี้', $newCustomersToday)
                ->description('ลูกค้าใหม่ที่เพิ่มวันนี้')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('warning'),
                
            Stat::make('แก้ไขสัปดาห์นี้', $updatesThisWeek)
                ->description('การแก้ไขข้อมูลสัปดาห์นี้')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('info'),
        ];
    }
}
