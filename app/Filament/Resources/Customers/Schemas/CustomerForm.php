<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('ชื่อลูกค้า/บริษัท')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('เช่น บริษัท เอบีซี จำกัด หรือ นายสมชาย ใจดี'),
                
                CheckboxList::make('purchase_type')
                    ->label('ประเภทประกันที่สนใจ')
                    ->options([
                        'MOU' => 'MOU',
                        'มติ24' => 'มติ24',
                    ])
                    ->required()
                    ->helperText('เลือกได้หลายประเภท หรือเลือกเฉพาะที่ลูกค้าสนใจ')
                    ->columns(2),
                
                TextInput::make('phone')
                    ->label('เบอร์โทรศัพท์')
                    ->tel()
                    ->maxLength(20)
                    ->placeholder('0X-XXXX-XXXX'),
                
                TextInput::make('line_id')
                    ->label('Line ID')
                    ->maxLength(255)
                    ->placeholder('@username หรือ Line ID'),
                
                TextInput::make('email')
                    ->label('อีเมล')
                    ->email()
                    ->maxLength(255),
                
                Textarea::make('address')
                    ->label('ที่อยู่')
                    ->rows(3)
                    ->placeholder('ที่อยู่สำหรับการติดต่อ'),
                
                TextInput::make('current_credit')
                    ->label('เครดิตปัจจุบัน (บาท)')
                    ->numeric()
                    ->default(0)
                    ->prefix('฿'),
                
                TextInput::make('days_missing')
                    ->label('จำนวนวันที่ค้างชำระ')
                    ->numeric()
                    ->default(0)
                    ->suffix('วัน'),
                
                Toggle::make('is_active')
                    ->label('สถานะการใช้งาน')
                    ->default(true),
                
                TextInput::make('default_discount_mou')
                    ->label('ส่วนลดเริ่มต้น MOU (บาท/คน)')
                    ->numeric()
                    ->default(null)
                    ->minValue(0)
                    ->step(0.01)
                    ->prefix('฿')
                    ->placeholder('ไม่มี')
                    ->helperText('ส่วนลดที่ใช้เมื่อไม่ได้กำหนดส่วนลดพิเศษ (เว้นว่าง = ไม่มีส่วนลด)'),
                
                TextInput::make('default_discount_moti24')
                    ->label('ส่วนลดเริ่มต้น มติ24 (บาท/คน)')
                    ->numeric()
                    ->default(null)
                    ->minValue(0)
                    ->step(0.01)
                    ->prefix('฿')
                    ->placeholder('ไม่มี')
                    ->helperText('ส่วนลดที่ใช้เมื่อไม่ได้กำหนดส่วนลดพิเศษ (เว้นว่าง = ไม่มีส่วนลด)'),
                
                Textarea::make('notes')
                    ->label('หมายเหตุ')
                    ->rows(3)
                    ->placeholder('บันทึกข้อมูลเพิ่มเติมของลูกค้า'),
                
                TextInput::make('created_at')
                    ->label('วันที่สร้าง')
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn ($record) => $record !== null)
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y H:i') : '-'),
                
                TextInput::make('updated_at')
                    ->label('วันที่แก้ไขล่าสุด')
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn ($record) => $record !== null)
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y H:i') : '-'),
                
                // ส่วนจัดการส่วนลด
                self::buildDiscountSection(),
            ]);
    }
    
    /**
     * สร้างส่วนจัดการส่วนลดสำหรับลูกค้า
     */
    private static function buildDiscountSection()
    {
        return Repeater::make('discounts')
            ->label('🏷️ ส่วนลดพิเศษ (บาท/คน)')
            ->helperText('⚠️ หากกำหนดประเภทประกัน + ระยะเวลาซ้ำกัน ระบบจะใช้ค่าล่าสุดเท่านั้น')
            ->schema([
                Select::make('insurance_type')
                    ->label('ประเภทประกัน')
                    ->options([
                        'MOU' => '🔵 MOU',
                        'มติ24' => '🟢 มติ24',
                    ])
                    ->required()
                    ->placeholder('เลือกประเภทประกัน'),
                
                Select::make('duration')
                    ->label('ระยะเวลา & ราคาต่อคน')
                    ->options([
                        '3_months' => '3 เดือน → ฿590',
                        '6_months' => '6 เดือน → ฿990', 
                        '12_months' => '12 เดือน → ฿1,750',
                        '15_months' => '15 เดือน → ฿2,290',
                    ])
                    ->required()
                    ->placeholder('เลือกระยะเวลา'),
                
                TextInput::make('discount_amount')
                    ->label('💰 ส่วนลด (บาท/คน)')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->prefix('฿')
                    ->placeholder('0.00')
                    ->helperText('ใส่จำนวนเงินส่วนลดต่อคน')
                    ->required(),
            ])
            ->columns(3)
            ->defaultItems(0)
            ->addActionLabel('➕ เพิ่มส่วนลดพิเศษ')
            ->collapsible()
            ->collapsed(false)
            ->mutateDehydratedStateUsing(function (array $state): array {
                // ตรวจสอบและเตือนเรื่องการซ้ำกัน
                $unique = [];
                $keys = [];
                $duplicates = [];
                
                foreach ($state as $index => $discount) {
                    $key = ($discount['insurance_type'] ?? '') . '_' . ($discount['duration'] ?? '');
                    
                    // ถ้ามี key ซ้ำ
                    if (isset($keys[$key])) {
                        $duplicates[] = $key;
                        unset($unique[$keys[$key]]);
                    }
                    
                    $unique[] = $discount;
                    $keys[$key] = count($unique) - 1;
                }
                
                // แสดง notification ถ้ามีการซ้ำกัน
                if (!empty($duplicates)) {
                    \Filament\Notifications\Notification::make()
                        ->title('⚠️ พบการกำหนดส่วนลดซ้ำ')
                        ->body('ระบบได้ลบรายการซ้ำออกแล้ว และจะใช้ค่าล่าสุดที่กำหนด')
                        ->warning()
                        ->duration(5000)
                        ->send();
                }
                
                return array_values($unique);
            });
    }
}
