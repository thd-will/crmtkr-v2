<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\CustomersChart;
use App\Filament\Widgets\PolicyTicketStatusChart;
use App\Filament\Widgets\LatestCustomers;
use App\Filament\Widgets\PendingPaymentsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            CustomersChart::class,
            PolicyTicketStatusChart::class,
            PendingPaymentsWidget::class,
            LatestCustomers::class,
        ];
    }
    
    public function getColumns(): int | array
    {
        return 2;
    }
}
