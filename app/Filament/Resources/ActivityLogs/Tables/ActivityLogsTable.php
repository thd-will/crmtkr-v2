<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('วันเวลา')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->since(),

                TextColumn::make('causer.name')
                    ->label('ผู้ใช้')
                    ->default('ระบบ')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('description')
                    ->label('การกระทำ')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        str_contains(strtolower($state), 'created') || str_contains(strtolower($state), 'สร้าง') => 'success',
                        str_contains(strtolower($state), 'updated') || str_contains(strtolower($state), 'อัปเดต') => 'info',
                        str_contains(strtolower($state), 'deleted') || str_contains(strtolower($state), 'ลบ') => 'danger',
                        str_contains(strtolower($state), 'login') || str_contains(strtolower($state), 'เข้าสู่') => 'warning',
                        default => 'gray',
                    }),
                    
                TextColumn::make('subject_type')
                    ->label('ประเภท')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'App\\Models\\Customer' => 'ลูกค้า',
                        'App\\Models\\CreditTransaction' => 'เครดิต',
                        'App\\Models\\Payment' => 'การชำระ',
                        'App\\Models\\User' => 'ผู้ใช้',
                        'App\\Models\\PolicyTicket' => 'กรมธรรม์',
                        default => $state ? class_basename($state) : '-',
                    })
                    ->badge(),
                    
                TextColumn::make('subject_id')
                    ->label('รหัส')
                    ->limit(10),
                    
                TextColumn::make('properties')
                    ->label('รายละเอียด')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (is_array($state)) {
                            return json_encode($state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                        }
                        return $state;
                    })
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            if (isset($state['attributes'])) {
                                $attrs = array_slice($state['attributes'], 0, 2);
                                return implode(', ', array_map(fn($k, $v) => "$k: $v", array_keys($attrs), $attrs));
                            }
                            return json_encode($state, JSON_UNESCAPED_UNICODE);
                        }
                        return $state ?: '-';
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->filters([
                //
            ]);
    }
}
