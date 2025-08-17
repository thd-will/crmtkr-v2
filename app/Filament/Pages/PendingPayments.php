<?php

namespace App\Filament\Pages;

use App\Models\PolicyTicket;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;

class PendingPayments extends Page implements HasTable
{
    use InteractsWithTable;
    
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';
    
    protected static ?string $navigationLabel = 'รายการค้างชำระ';
    
    protected static ?string $title = 'รายการค้างชำระ';
    
    protected static ?int $navigationSort = 20;

    public function getView(): string
    {
        return 'filament.pages.pending-payments';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PolicyTicket::with(['customer'])
                    ->whereIn('payment_status', ['pending', 'partial'])
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
                    ->color('danger')
                    ->sortable(),
                
                TextColumn::make('paid_amount')
                    ->label('จ่ายแล้ว')
                    ->money('THB')
                    ->default(0)
                    ->color('success'),
                
                TextColumn::make('remaining_amount')
                    ->label('คงเหลือ')
                    ->getStateUsing(fn (PolicyTicket $record): float => $record->getRemainingAmount())
                    ->money('THB')
                    ->weight('bold')
                    ->color('warning')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->selectRaw('*, (total_amount - COALESCE(paid_amount, 0)) as remaining_amount')
                            ->orderBy('remaining_amount', $direction);
                    }),
                
                BadgeColumn::make('payment_status')
                    ->label('สถานะ')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'pending' => 'รอชำระ',
                        'partial' => 'จ่ายบางส่วน',
                        'paid' => 'จ่ายครบ',
                        default => $state,
                    })
                    ->colors([
                        'danger' => 'pending',
                        'warning' => 'partial',
                        'success' => 'paid',
                    ]),
                
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
                    ])
                    ->default('pending'),
                
                SelectFilter::make('insurance_type')
                    ->label('ประเภทประกัน')
                    ->options([
                        'MOU' => 'MOU',
                        'มติ24' => 'มติ24',
                    ]),
                
                Filter::make('high_amount')
                    ->label('ยอดสูง (>5,000 บาท)')
                    ->query(fn (Builder $query): Builder => $query->where('total_amount', '>', 5000))
                    ->toggle(),
            ])
            ->actions([
                Action::make('record_payment')
                    ->label('บันทึกชำระ')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->url(fn (PolicyTicket $record): string => "/admin/payments/create?customer_id={$record->customer_id}&policy_ticket_id={$record->id}")
                    ->openUrlInNewTab(false),
                
                Action::make('view_ticket')
                    ->label('ดูตั๋ว')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (PolicyTicket $record): string => "/admin/policy-tickets/{$record->id}/edit")
                    ->openUrlInNewTab(false),
                
                Action::make('view_customer')
                    ->label('ดูลูกค้า')
                    ->icon('heroicon-o-user')
                    ->color('warning')
                    ->url(fn (PolicyTicket $record): string => "/admin/customers/{$record->customer_id}/edit")
                    ->openUrlInNewTab(false),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('🎉 ไม่มีรายการค้างชำระ')
            ->emptyStateDescription('ลูกค้าทุกคนชำระเงินครบแล้ว!')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
