<?php

namespace App\Filament\Pages;

use App\Models\Customer;
use App\Models\CreditTransaction;
use App\Filament\Widgets\CreditStatsWidget;
use App\Filament\Widgets\AlertsWidget;
use App\Filament\Widgets\RecentTransactionsWidget;
use Filament\Pages\Page;

class CreditDashboard extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'แดชบอร์ดเครดิต';
    protected static ?string $title = 'แดชบอร์ดเครดิต';
    protected static ?int $navigationSort = 19;
    protected static ?string $slug = 'credit-dashboard';

    // ใช้ Widget แทน blade template
    
    public function getHeaderWidgets(): array
    {
        return [
            CreditStatsWidget::class,
            AlertsWidget::class,
        ];
    }

    public function getFooterWidgets(): array
    {
        return [
            RecentTransactionsWidget::class,
        ];
    }

    public function mount(): void
    {
        // Initialize any data
    }

    // ใช้ getViewData สำหรับส่งข้อมูลไปยัง default template
    protected function getViewData(): array
    {
        $totalCredit = Customer::sum('current_credit') ?? 0;
        $totalCustomers = Customer::whereHas('creditTransactions')->count();
        $totalTransactions = CreditTransaction::count();
        $monthlyTransactions = CreditTransaction::where('created_at', '>=', now()->startOfMonth())->count();
        $recentTransactions = CreditTransaction::with('customer')->latest()->limit(10)->get();

        return [
            'totalCredit' => $totalCredit,
            'totalCustomers' => $totalCustomers,
            'totalTransactions' => $totalTransactions,
            'monthlyTransactions' => $monthlyTransactions,
            'recentTransactions' => $recentTransactions,
        ];
    }
}
