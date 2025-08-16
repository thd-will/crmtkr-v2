<?php

namespace App\Filament\Resources\Customers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Notifications\Notification;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->url(fn ($record) => route('filament.admin.resources.customers.view', $record)),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('line_id')
                    ->searchable(),
                TextColumn::make('purchase_type')
                    ->label('ประเภทประกันที่สนใจ')
                    ->formatStateUsing(function ($state): string {
                        if (is_array($state)) {
                            return implode(', ', $state);
                        }
                        return $state ?? '-';
                    })
                    ->badge()
                    ->separator(','),
                TextColumn::make('default_discount_mou')
                    ->label('ส่วนลด MOU')
                    ->money('THB')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('default_discount_moti24')
                    ->label('ส่วนลด มติ24')
                    ->money('THB')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('current_credit')
                    ->label('เครดิต')
                    ->money('THB')
                    ->sortable(),
                TextColumn::make('discounts')
                ->label('ส่วนลด 6 เดือน')
                ->formatStateUsing(function ($state, $record) {
                    try {
                        // ใช้ method จาก Customer model เพื่อความแม่นยำ
                        $mouDiscount = $record->getDiscountFor('MOU', '6_months');
                        $moti24Discount = $record->getDiscountFor('มติ24', '6_months');
                        
                        $results = [];
                        
                        if ($mouDiscount > 0) {
                            $results[] = "MOU: ฿" . number_format($mouDiscount, 0);
                        }
                        
                        if ($moti24Discount > 0) {
                            $results[] = "มติ24: ฿" . number_format($moti24Discount, 0);
                        }
                        
                        return !empty($results) ? implode(' | ', $results) : '';
                    } catch (\Exception $e) {
                        // ในกรณี error แสดงข้อความแทน
                        return 'Error: ' . $e->getMessage();
                    }
                })
                ->sortable(false)
                ->searchable(false)
                ->wrap(),
                TextColumn::make('days_missing')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('ดูรายละเอียด'),
                EditAction::make()
                    ->label('แก้ไข'),
                Action::make('edit_discounts')
                    ->label('✏️ แก้ไขส่วนลด')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->form([
                        Repeater::make('discounts')
                            ->label('🏷️ ส่วนลดพิเศษ')
                            ->helperText('⚠️ หากกำหนดประเภทประกัน + ระยะเวลาซ้ำกัน ระบบจะแจ้งเตือนทันที')
                            ->schema([
                                Select::make('insurance_type')
                                    ->label('ประเภทประกัน')
                                    ->options([
                                        'MOU' => '🔵 MOU',
                                        'มติ24' => '🟢 มติ24',
                                    ])
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        // ตรวจสอบการซ้ำกันแบบ real-time
                                        self::checkDuplicateDiscount($get, $set);
                                    }),
                                
                                Select::make('duration')
                                    ->label('ระยะเวลา & ราคา')
                                    ->options([
                                        '3_months' => '3 เดือน → ฿590',
                                        '6_months' => '6 เดือน → ฿990',
                                        '12_months' => '12 เดือน → ฿1,750',
                                        '15_months' => '15 เดือน → ฿2,290',
                                    ])
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        self::checkDuplicateDiscount($get, $set);
                                    }),
                                
                                TextInput::make('discount_amount')
                                    ->label('💰 ส่วนลด (บาท/คน)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->prefix('฿')
                                    ->required(),
                                    
                                Hidden::make('is_duplicate')
                                    ->default(false),
                            ])
                            ->itemLabel(function (array $state): ?string {
                                $type = $state['insurance_type'] ?? '';
                                $duration = $state['duration'] ?? '';
                                $amount = $state['discount_amount'] ?? 0;
                                $isDuplicate = $state['is_duplicate'] ?? false;
                                
                                if (!$type || !$duration) {
                                    return '➕ กำหนดส่วนลดใหม่';
                                }
                                
                                $durationText = match($duration) {
                                    '3_months' => '3ด.',
                                    '6_months' => '6ด.',
                                    '12_months' => '12ด.',
                                    '15_months' => '15ด.',
                                    default => $duration
                                };
                                
                                $label = "🏷️ {$type} ({$durationText}) → ฿" . number_format($amount, 0);
                                
                                if ($isDuplicate) {
                                    $label = "⚠️ " . $label . " (ซ้ำกัน!)";
                                }
                                
                                return $label;
                            })
                            ->addActionLabel('➕ เพิ่มส่วนลด')
                            ->collapsible()
                            ->defaultItems(0)
                            ->columns(3),
                    ])
                    ->action(function ($record, $data) {
                        // ลบรายการที่ซ้ำกันก่อนบันทึก
                        $unique = [];
                        $keys = [];
                        
                        foreach (($data['discounts'] ?? []) as $discount) {
                            $key = ($discount['insurance_type'] ?? '') . '_' . ($discount['duration'] ?? '');
                            
                            if (isset($keys[$key])) {
                                unset($unique[$keys[$key]]);
                            }
                            
                            unset($discount['is_duplicate']); // ลบ field ที่ไม่ต้องการ
                            $unique[] = $discount;
                            $keys[$key] = count($unique) - 1;
                        }
                        
                        $record->update(['discounts' => array_values($unique)]);
                        
                        Notification::make()
                            ->title('✅ อัปเดตส่วนลดสำเร็จ')
                            ->success()
                            ->send();
                    }),
                
                Action::make('view_discounts')
                    ->label('ดูส่วนลด')
                    ->icon('heroicon-o-receipt-percent')
                    ->color('success')
                    ->modalHeading(fn ($record) => "ส่วนลดของ {$record->name}")
                    ->modalContent(fn ($record) => view('filament.customer-discounts', ['customer' => $record]))
                    ->modalActions([]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * ตรวจสอบการซ้ำกันของส่วนลดแบบ real-time
     */
    private static function checkDuplicateDiscount($get, $set)
    {
        $discounts = $get('../../discounts') ?? [];
        $currentType = $get('insurance_type');
        $currentDuration = $get('duration');
        
        if (!$currentType || !$currentDuration) {
            $set('is_duplicate', false);
            return;
        }
        
        $currentKey = $currentType . '_' . $currentDuration;
        $duplicateCount = 0;
        
        // นับจำนวนการซ้ำกัน
        foreach ($discounts as $discount) {
            $key = ($discount['insurance_type'] ?? '') . '_' . ($discount['duration'] ?? '');
            if ($key === $currentKey) {
                $duplicateCount++;
            }
        }
        
        $isDuplicate = $duplicateCount > 1;
        $set('is_duplicate', $isDuplicate);
        
        if ($isDuplicate) {
            Notification::make()
                ->title('⚠️ พบการกำหนดซ้ำ')
                ->body("มีการกำหนดส่วนลดสำหรับ {$currentType} ({$currentDuration}) มากกว่า 1 รายการ")
                ->warning()
                ->duration(3000)
                ->send();
        }
    }
}
