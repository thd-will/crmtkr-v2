<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ActivityTimelineWidget extends BaseWidget
{
    protected static ?string $heading = 'กิจกรรมล่าสุด (สรุป)';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ActivityLog::query()
                    ->with(['causer'])
                    ->latest()
                    ->limit(8)  // ลดจำนวนเหลือ 8 รายการ
            )
            ->columns([
                Tables\Columns\IconColumn::make('icon')
                    ->label('')
                    ->icon(fn ($record): string => match ($record->description) {
                        'สร้างลูกค้าใหม่' => 'heroicon-o-user-plus',
                        'แก้ไขข้อมูลลูกค้า' => 'heroicon-o-pencil-square',
                        'ลบลูกค้า' => 'heroicon-o-user-minus',
                        'เข้าสู่ระบบ' => 'heroicon-o-arrow-right-on-rectangle',
                        'เพิ่มเครดิต' => 'heroicon-o-plus-circle',
                        'ใช้เครดิต' => 'heroicon-o-minus-circle',
                        default => 'heroicon-o-document-text',
                    })
                    ->color(fn ($record): string => match ($record->description) {
                        'สร้างลูกค้าใหม่' => 'success',
                        'แก้ไขข้อมูลลูกค้า' => 'warning',
                        'ลบลูกค้า' => 'danger',
                        'เข้าสู่ระบบ' => 'info',
                        'เพิ่มเครดิต' => 'success',
                        'ใช้เครดิต' => 'gray',
                        default => 'gray',
                    })
                    ->size('lg'),
                    
                Tables\Columns\TextColumn::make('description')
                    ->label('การกระทำ')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'สร้างลูกค้าใหม่' => 'success',
                        'แก้ไขข้อมูลลูกค้า' => 'warning',
                        'ลบลูกค้า' => 'danger',
                        'เข้าสู่ระบบ' => 'info',
                        'เพิ่มเครดิต' => 'success',
                        'ใช้เครดิต' => 'gray',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('subject_info')
                    ->label('รายการ')
                    ->getStateUsing(function ($record) {
                        $type = match ($record->subject_type) {
                            'App\Models\Customer' => 'ลูกค้า',
                            'App\Models\CreditTransaction' => 'เครดิต',
                            'App\Models\User' => 'ผู้ใช้',
                            default => $record->subject_type ? str_replace('App\\Models\\', '', $record->subject_type) : '-',
                        };
                        
                        return $record->subject_id ? "{$type} #{$record->subject_id}" : $type;
                    })
                    ->badge()
                    ->color('gray'),
                    
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('ผู้ทำ')
                    ->default('ระบบ'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('เวลา')
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at->format('d/m/Y H:i:s')),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated(false);
    }
}
