<?php

namespace App\Filament\Pages;

use App\Models\PolicyTicket;
use App\Models\Payment;
use App\Models\Customer;
use App\Filament\Widgets\FinancialReportStats;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use BackedEnum;

class FinancialReport extends Page implements HasTable
{
    use InteractsWithTable;
    
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';
    
    protected static ?string $navigationLabel = 'รายงานการเงิน';
    
    protected static ?string $title = 'รายงานการเงิน';
    
    protected static ?int $navigationSort = 12;

    // ไม่ใส่ group เพื่อให้อยู่ในเมนูหลัก

    public function getView(): string
    {
        return 'filament.pages.financial-report';
    }

    public function getHeaderWidgets(): array
    {
        return [
            FinancialReportStats::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return [
            'sm' => 2,  // 2 cards per row on small screens
            'lg' => 4,  // 4 cards per row on large screens
        ];
    }

    public function getFooterWidgets(): array
    {
        return [
            // ไม่มี widgets
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(PolicyTicket::with(['customer']))
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
                    ->sortable()
                    ->limit(30),
                
                TextColumn::make('customer.phone')
                    ->label('เบอร์โทร')
                    ->toggleable(),
                
                TextColumn::make('insurance_type')
                    ->label('ประเภท')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'MOU' => 'info',
                        'มติ24' => 'warning',
                        default => 'gray',
                    }),
                
                TextColumn::make('duration')
                    ->label('ระยะเวลา')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        '3_months' => '3 เดือน',
                        '6_months' => '6 เดือน', 
                        '12_months' => '12 เดือน',
                        '15_months' => '15 เดือน',
                        default => $state,
                    })
                    ->toggleable(),
                
                TextColumn::make('person_count')
                    ->label('จำนวนคน')
                    ->alignCenter()
                    ->toggleable(),
                
                TextColumn::make('total_amount')
                    ->label('ยอดรวม')
                    ->money('THB')
                    ->weight('bold')
                    ->color('warning')
                    ->sortable(),
                
                TextColumn::make('paid_amount')
                    ->label('จ่ายแล้ว')
                    ->getStateUsing(fn (PolicyTicket $record) => $record->paid_amount ?? 0)
                    ->money('THB')
                    ->color('success'),
                
                TextColumn::make('remaining_amount')
                    ->label('คงเหลือ')
                    ->getStateUsing(fn (PolicyTicket $record) => $record->getRemainingAmount())
                    ->money('THB')
                    ->weight('bold')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
                
                BadgeColumn::make('payment_status')
                    ->label('สถานะการชำระ')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'pending' => 'รอชำระ',
                        'partial' => 'จ่ายบางส่วน',
                        'paid' => 'จ่ายครบ',
                        'overdue' => 'เกินกำหนด',
                        default => $state,
                    })
                    ->colors([
                        'danger' => 'pending',
                        'warning' => 'partial',
                        'success' => 'paid',
                        'danger' => 'overdue',
                    ]),

                BadgeColumn::make('status')
                    ->label('สถานะตั๋ว')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'draft' => 'ร่าง',
                        'submitted' => 'ส่งแล้ว',
                        'processing' => 'กำลังดำเนินการ',
                        'completed' => 'เสร็จแล้ว',
                        'rejected' => 'ถูกปฏิเสธ',
                        default => $state,
                    })
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'submitted',
                        'info' => 'processing',
                        'success' => 'completed',
                        'danger' => 'rejected',
                    ])
                    ->toggleable(),
                
                TextColumn::make('created_at')
                    ->label('สร้างเมื่อ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('payment_status')
                    ->label('สถานะการชำระ')
                    ->options([
                        'pending' => 'รอชำระ',
                        'partial' => 'จ่ายบางส่วน',
                        'paid' => 'จ่ายครบ',
                        'overdue' => 'เกินกำหนด',
                    ]),
                
                SelectFilter::make('insurance_type')
                    ->label('ประเภทประกัน')
                    ->options([
                        'MOU' => 'MOU',
                        'มติ24' => 'มติ24',
                    ]),

                SelectFilter::make('status')
                    ->label('สถานะตั๋ว')
                    ->options([
                        'draft' => 'ร่าง',
                        'submitted' => 'ส่งแล้ว',
                        'processing' => 'กำลังดำเนินการ',
                        'completed' => 'เสร็จแล้ว',
                        'rejected' => 'ถูกปฏิเสธ',
                    ]),
                
                Filter::make('high_amount')
                    ->label('ยอดสูง (>10,000 บาท)')
                    ->query(fn (Builder $query): Builder => $query->where('total_amount', '>', 10000))
                    ->toggle(),

                Filter::make('has_outstanding')
                    ->label('มียอดค้างชำระ')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereRaw('total_amount > (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.policy_ticket_id = policy_tickets.id AND payments.status = "confirmed")')
                    )
                    ->toggle(),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([25, 50, 100])
            ->emptyStateHeading('🔍 ไม่พบข้อมูลรายงานการเงิน')
            ->emptyStateDescription('ยังไม่มีตั๋วประกันในระบบ หรือโหลดข้อมูลไม่สำเร็จ')
            ->emptyStateIcon('heroicon-o-document-chart-bar')
            ->emptyStateActions([
                \Filament\Actions\Action::make('refresh')
                    ->label('รีเฟรช')
                    ->icon('heroicon-m-arrow-path')
                    ->action(fn () => $this->resetTable())
                    ->color('primary'),
            ]);
    }

    public function getRefreshInterval(): ?int
    {
        return 30; // Refresh every 30 seconds
    }
}
