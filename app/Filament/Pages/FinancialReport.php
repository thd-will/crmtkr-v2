<?php

namespace App\Filament\Pages;

use App\Models\PolicyTicket;
use App\Models\Payment;
use App\Models\Customer;
use App\Filament\Widgets\FinancialStatsWidget;
use App\Filament\Widgets\MonthlyRevenueChartWidget;
use App\Filament\Widgets\PaymentStatusChartWidget;
use App\Filament\Widgets\TopCustomersWidget;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use BackedEnum;

class FinancialReport extends Page implements HasTable
{
    use InteractsWithTable;
    
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';
    
    protected static ?string $navigationLabel = 'รายงานการเงิน';
    
    protected static ?string $title = 'รายงานการเงิน';
    
    protected static ?int $navigationSort = 12;

    // ไม่ใส่ group เพื่อให้อยู่ในเมนูหลัก

    public function getHeaderWidgets(): array
    {
        return [
            FinancialStatsWidget::class,
        ];
    }

    public function getFooterWidgets(): array
    {
        return [
            MonthlyRevenueChartWidget::class,
            PaymentStatusChartWidget::class,
            TopCustomersWidget::class,
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PolicyTicket::with(['customer', 'payments'])
                    ->select([
                        'policy_tickets.*',
                        DB::raw('(SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.policy_ticket_id = policy_tickets.id AND payments.status = "confirmed") as paid_amount'),
                        DB::raw('(policy_tickets.total_amount - (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.policy_ticket_id = policy_tickets.id AND payments.status = "confirmed")) as remaining_amount')
                    ])
            )
            ->columns([
                TextColumn::make('ticket_number')
                    ->label('หมายเลขตั๋ว')
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),
                
                TextColumn::make('customer.name')
                    ->label('ลูกค้า')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label('ยอดเรียกเก็บ')
                    ->money('THB')
                    ->sortable()
                    ->color('warning'),

                TextColumn::make('paid_amount')
                    ->label('เก็บแล้ว')
                    ->money('THB')
                    ->sortable()
                    ->color('success'),

                TextColumn::make('remaining_amount')
                    ->label('คงเหลือ')
                    ->money('THB')
                    ->sortable()
                    ->color('danger')
                    ->weight('bold'),

                BadgeColumn::make('payment_status')
                    ->label('สถานะ')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid', 
                        'info' => 'partial',
                        'danger' => 'overdue',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'รอชำระ',
                        'paid' => 'ชำระแล้ว',
                        'partial' => 'ชำระบางส่วน',
                        'overdue' => 'เกินกำหนด',
                        default => $state,
                    }),

                TextColumn::make('created_at')
                    ->label('วันที่สร้าง')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('payment_status')
                    ->label('สถานะการชำระ')
                    ->options([
                        'pending' => 'รอชำระ',
                        'paid' => 'ชำระแล้ว',
                        'partial' => 'ชำระบางส่วน',
                        'overdue' => 'เกินกำหนด',
                    ]),

                Filter::make('high_amount')
                    ->label('ยอดสูง (> 10,000)')
                    ->query(fn (Builder $query): Builder => $query->where('total_amount', '>', 10000)),

                Filter::make('this_month')
                    ->label('เดือนนี้')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)),
            ])
            ->actions([
                // สามารถเพิ่ม actions อื่นๆ ได้
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100]);
    }
}
