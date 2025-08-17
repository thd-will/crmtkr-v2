<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class ActivityStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        // สถิติการกระทำวันนี้
        $todayCount = ActivityLog::whereDate('created_at', $today)->count();
        
        // สถิติการกระทำสัปดาห์นี้
        $weekCount = ActivityLog::where('created_at', '>=', $thisWeek)->count();
        
        // สถิติการกระทำเดือนนี้
        $monthCount = ActivityLog::where('created_at', '>=', $thisMonth)->count();
        
        // ผู้ใช้ที่มีการกระทำมากที่สุด
        $topUser = ActivityLog::selectRaw('causer_id, COUNT(*) as count')
            ->where('causer_type', 'App\\Models\\User')
            ->where('created_at', '>=', $thisWeek)
            ->groupBy('causer_id')
            ->orderBy('count', 'desc')
            ->with('causer')
            ->first();

        return [
            Stat::make('การกระทำวันนี้', $todayCount)
                ->description('กิจกรรมใหม่วันนี้')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success')
                ->chart([5, 10, 15, $todayCount]),

            Stat::make('การกระทำสัปดาห์นี้', $weekCount)
                ->description('กิจกรรมทั้งสัปดาห์')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info')
                ->chart([20, 35, 45, $weekCount]),

            Stat::make('การกระทำเดือนนี้', $monthCount)
                ->description('กิจกรรมทั้งเดือน')
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color('warning'),

            Stat::make('ผู้ใช้ที่มีกิจกรรมมากที่สุด', $topUser ? $topUser->causer?->name ?? 'ไม่ระบุ' : '-')
                ->description($topUser ? "{$topUser->count} กิจกรรม (สัปดาห์นี้)" : 'ไม่มีข้อมูล')
                ->descriptionIcon('heroicon-m-user')
                ->color('primary'),
        ];
    }
}
