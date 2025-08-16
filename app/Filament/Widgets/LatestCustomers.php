<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestCustomers extends BaseWidget
{
    public function getTableHeading(): ?string
    {
        return 'ลูกค้าล่าสุด';
    }

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Customer::query()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('ชื่อลูกค้า')
                    ->searchable()
                    ->url(fn (Customer $record): string => route('filament.admin.resources.customers.view', $record)),
                
                Tables\Columns\TextColumn::make('phone')
                    ->label('เบอร์โทร'),
                
                Tables\Columns\TextColumn::make('current_credit')
                    ->label('เครดิต')
                    ->money('THB'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('วันที่สมัคร')
                    ->dateTime('d/m/Y H:i'),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('สถานะ')
                    ->boolean(),
            ]);
    }
}
