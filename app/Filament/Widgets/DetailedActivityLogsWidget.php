<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class DetailedActivityLogsWidget extends BaseWidget
{
    protected static ?string $heading = 'กิจกรรมล่าสุด';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ActivityLog::query()
                    ->with(['causer', 'subject']) // เพิ่ม subject relation
                    ->latest()
                    ->limit(20)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('วันที่')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->size('sm'),
                    
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
                    })
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('subject_info')
                    ->label('ชื่อ/รายการ')
                    ->getStateUsing(function ($record) {
                        $type = match ($record->subject_type) {
                            'App\Models\Customer' => 'ลูกค้า',
                            'App\Models\CreditTransaction' => 'เครดิต',
                            'App\Models\User' => 'ผู้ใช้',
                            default => str_replace('App\\Models\\', '', $record->subject_type ?? ''),
                        };
                        
                        // ถ้าเป็น Customer ให้แสดงชื่อ
                        if ($record->subject_type === 'App\Models\Customer' && $record->subject) {
                            return $record->subject->name ?? "{$type} #{$record->subject_id}";
                        }
                        
                        return $record->subject_id ? "{$type} #{$record->subject_id}" : $type;
                    })
                    ->searchable()
                    ->wrap(),
                    
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('ประเภท')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'App\Models\Customer' => 'ลูกค้า',
                        'App\Models\CreditTransaction' => 'เครดิต',
                        'App\Models\User' => 'ผู้ใช้',
                        default => $state ? str_replace('App\\Models\\', '', $state) : '',
                    })
                    ->badge()
                    ->color('gray')
                    ->size('sm'),
                    
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('ผู้ทำ')
                    ->default('ระบบ')
                    ->size('sm'),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->getStateUsing(function ($record) {
                        $properties = is_array($record->properties) 
                            ? $record->properties 
                            : (is_string($record->properties) ? json_decode($record->properties, true) : []);
                        return $properties['ip_address'] ?? 'ไม่ระบุ';
                    })
                    ->size('sm')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('user_agent')
                    ->label('Browser')
                    ->getStateUsing(function ($record) {
                        $properties = is_array($record->properties) 
                            ? $record->properties 
                            : (is_string($record->properties) ? json_decode($record->properties, true) : []);
                        $userAgent = $properties['user_agent'] ?? '';
                        
                        // ตัดให้สั้นลงและแสดงเฉพาะ browser name
                        if (str_contains($userAgent, 'Chrome')) return '🌐 Chrome';
                        if (str_contains($userAgent, 'Firefox')) return '🔥 Firefox';
                        if (str_contains($userAgent, 'Safari')) return '🧭 Safari';
                        if (str_contains($userAgent, 'Edge')) return '⚡ Edge';
                        return $userAgent ? '🌐 Other' : 'ไม่ระบุ';
                    })
                    ->size('sm')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('properties')
                    ->label('รายละเอียดการเปลี่ยนแปลง')
                    ->getStateUsing(function ($record) {
                        $properties = is_array($record->properties) 
                            ? $record->properties 
                            : (is_string($record->properties) ? json_decode($record->properties, true) : []);
                        
                        if (isset($properties['old']) && !empty($properties['old'])) {
                            $changes = [];
                            $attributes = $properties['attributes'] ?? [];
                            $old = $properties['old'];
                            
                            foreach ($attributes as $key => $value) {
                                if (isset($old[$key]) && $old[$key] != $value) {
                                    $oldVal = is_array($old[$key]) ? implode(', ', $old[$key]) : $old[$key];
                                    $newVal = is_array($value) ? implode(', ', $value) : $value;
                                    $changes[] = "{$key}: {$oldVal} → {$newVal}";
                                }
                            }
                            
                            return implode("\n", array_slice($changes, 0, 3));
                        } elseif (isset($properties['attributes'])) {
                            $attributes = $properties['attributes'];
                            $data = [];
                            foreach (array_slice($attributes, 0, 3, true) as $key => $value) {
                                if (!empty($value)) {
                                    $val = is_array($value) ? implode(', ', $value) : $value;
                                    $data[] = "{$key}: {$val}";
                                }
                            }
                            return implode("\n", $data);
                        }
                        
                        return 'ไม่มีรายละเอียด';
                    })
                    ->wrap()
                    ->lineClamp(3),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50])
            ->poll('30s'); // Auto-refresh every 30 seconds
    }
}
