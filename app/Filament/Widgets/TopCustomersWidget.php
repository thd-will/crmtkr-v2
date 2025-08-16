<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\PolicyTicket;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class TopCustomersWidget extends BaseWidget
{
    protected static ?string $heading = 'ลูกค้ายอดเงินสูงสุด';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Customer::select([
                    'customers.*',
                    DB::raw('COUNT(policy_tickets.id) as total_tickets'),
                    DB::raw('COALESCE(SUM(policy_tickets.total_amount), 0) as total_amount'),
                    DB::raw('COALESCE(SUM(payments.amount), 0) as paid_amount')
                ])
                ->leftJoin('policy_tickets', 'customers.id', '=', 'policy_tickets.customer_id')
                ->leftJoin('payments', function($join) {
                    $join->on('policy_tickets.id', '=', 'payments.policy_ticket_id')
                         ->where('payments.status', '=', 'confirmed');
                })
                ->groupBy('customers.id', 'customers.name', 'customers.phone', 'customers.email', 'customers.created_at', 'customers.updated_at')
                ->havingRaw('total_amount > 0')
                ->orderBy('total_amount', 'desc')
                ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('ชื่อลูกค้า')
                    ->weight('bold')
                    ->searchable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('total_tickets')
                    ->label('จำนวนตั๋ว')
                    ->alignCenter()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('ยอดรวม')
                    ->money('THB')
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('จ่ายแล้ว')
                    ->money('THB')
                    ->color('success'),
            ])
            ->paginated(false);
    }
}
