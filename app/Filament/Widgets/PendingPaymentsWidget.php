<?php

namespace App\Filament\Widgets;

use App\Models\PolicyTicket;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;

class PendingPaymentsWidget extends BaseWidget
{
    protected static ?string $heading = '🚨 รายการค้างชำระ';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PolicyTicket::with(['customer'])
                    ->whereIn('payment_status', ['pending', 'partial'])
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('ticket_number')
                    ->label('หมายเลขตั๋ว')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),
                
                TextColumn::make('customer.name')
                    ->label('ลูกค้า')
                    ->searchable()
                    ->limit(25),
                
                TextColumn::make('insurance_type')
                    ->label('ประเภท')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'MOU' => 'info',
                        'มติ24' => 'warning',
                        default => 'gray',
                    }),
                
                TextColumn::make('total_amount')
                    ->label('ยอดรวม')
                    ->money('THB')
                    ->weight('bold')
                    ->color('danger'),
                
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
                    ->color('warning'),
                
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
                    ->since()
                    ->color('gray'),
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
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('🎉 ไม่มีรายการค้างชำระ')
            ->emptyStateDescription('ลูกค้าทุกคนชำระเงินครบแล้ว!')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
