<?php

namespace App\Filament\Widgets;

use App\Models\CreditTransaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentTransactionsWidget extends BaseWidget
{
    protected static ?string $heading = 'ประวัติการใช้เครดิต';
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(CreditTransaction::query()->with('customer')->latest())
            ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('ลูกค้า')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('type')
                    ->label('ประเภท')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'credit' => 'success',
                        'debit' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'credit' => 'เพิ่มเครดิต',
                        'debit' => 'ใช้เครดิต',
                        default => $state,
                    }),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->label('จำนวนเงิน')
                    ->money('THB')
                    ->sortable()
                    ->color(fn ($record): string => $record->type === 'credit' ? 'success' : 'danger'),
                    
                Tables\Columns\TextColumn::make('description')
                    ->label('รายละเอียด')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('วันที่')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
