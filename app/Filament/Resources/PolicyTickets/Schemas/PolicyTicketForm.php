<?php

namespace App\Filament\Resources\PolicyTickets\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\Customer;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;

class PolicyTicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('📄 ข้อมูลคำขอประกันภัย')
                    ->description('ข้อมูลพื้นฐานของคำขอประกันภัย')
                    ->icon('heroicon-m-document-text')
                    ->schema([
                        Select::make('customer_id')
                            ->label('ลูกค้า')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                if ($state) {
                                    $customer = Customer::find($state);
                                    if ($customer) {
                                        // ตั้งค่าประเภทประกันเป็นตัวแรกที่ลูกค้าสนใจ
                                        $interestedTypes = $customer->getInterestedInsuranceTypes();
                                        if (!empty($interestedTypes)) {
                                            $set('insurance_type', $interestedTypes[0]);
                                            
                                            // คำนวนส่วนลดโดยอัตโนมัติ
                                            $duration = $get('duration') ?: '6_months'; // default
                                            $discount = $customer->getDiscountFor($interestedTypes[0], $duration);
                                            $set('discount_amount', $discount);
                                        }
                                    }
                                } else {
                                    $set('insurance_type', null);
                                    $set('discount_amount', 0);
                                }
                                
                                self::calculateTotalAmount($set, $get);
                    })
                    ->helperText(function (callable $get) {
                        $customerId = $get('customer_id');
                        if ($customerId) {
                            $customer = Customer::find($customerId);
                            return $customer ? "💰 เครดิตปัจจุบัน: ฿" . number_format($customer->current_credit, 2) : '';
                        }
                        return '';
                    }),

                Select::make('insurance_type')
                    ->label('ประเภทประกัน')
                    ->options(function (callable $get) {
                        $customerId = $get('customer_id');
                        if (!$customerId) {
                            return [
                                'MOU' => 'MOU',
                                'มติ24' => 'มติ24',
                            ];
                        }
                        
                        $customer = Customer::find($customerId);
                        if (!$customer) {
                            return [];
                        }
                        
                        $interestedTypes = $customer->getInterestedInsuranceTypes();
                        $options = [];
                        
                        foreach ($interestedTypes as $type) {
                            $options[$type] = $type;
                        }
                        
                        return $options;
                    })
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, callable $get, $state) {
                        $customerId = $get('customer_id');
                        $duration = $get('duration') ?: '6_months';
                        
                        if ($customerId && $state) {
                            $customer = Customer::find($customerId);
                            if ($customer) {
                                $discount = $customer->getDiscountFor($state, $duration);
                                $set('discount_amount', $discount);
                            }
                        }
                        
                        self::calculateTotalAmount($set, $get);
                    })
                    ->helperText('แสดงเฉพาะประเภทที่ลูกค้าสนใจ'),

                Select::make('duration')
                    ->label('ระยะเวลาความคุ้มครอง')
                    ->options(function () {
                        $products = \App\Models\Product::select('duration', 'duration_display', 'base_price')
                            ->where('is_active', true)
                            ->where('type', 'MOU') // Default to MOU, will update based on insurance_type
                            ->orderBy('duration')
                            ->get();
                        
                        $options = [];
                        foreach ($products as $product) {
                            $options[$product->duration] = $product->duration_display . ' (' . number_format($product->base_price) . ' บาท)';
                        }
                        return $options;
                    })
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, callable $get, $state) {
                        $customerId = $get('customer_id');
                        $insuranceType = $get('insurance_type');
                        
                        if ($customerId && $insuranceType && $state) {
                            $customer = Customer::find($customerId);
                            if ($customer) {
                                $discount = $customer->getDiscountFor($insuranceType, $state);
                                $set('discount_amount', $discount);
                            }
                        }
                        
                        self::calculateTotalAmount($set, $get);
                    }),

                TextInput::make('person_count')
                    ->label('จำนวนคน')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set, callable $get) => self::calculateTotalAmount($set, $get)),

                TextInput::make('discount_amount')
                    ->label('ส่วนลด (บาท/คน)')
                    ->numeric()
                    ->prefix('฿')
                    ->default(0)
                    ->helperText('ส่วนลดที่ใช้กับตั๋วนี้')
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set, callable $get) => self::calculateTotalAmount($set, $get)),

                TextInput::make('total_amount')
                    ->label('ราคารวม (บาท)')
                    ->numeric()
                    ->prefix('฿')
                    ->disabled()
                    ->dehydrated()
                    ->helperText(function (callable $get) {
                        $customerId = $get('customer_id');
                        $totalAmount = $get('total_amount');
                        
                        if ($customerId && $totalAmount) {
                            $customer = Customer::find($customerId);
                            if ($customer) {
                                $remaining = $customer->current_credit - $totalAmount;
                                if ($remaining < 0) {
                                    return "🚨 เครดิตไม่เพียงพอ! ขาด ฿" . number_format(abs($remaining), 2);
                                } else {
                                    return "✅ หลังซื้อจะคงเหลือ ฿" . number_format($remaining, 2);
                                }
                            }
                        }
                        return 'ราคาหลังหักส่วนลดแล้ว';
                    }),

                        TextInput::make('base_price_per_person')
                            ->hidden()
                            ->dehydrated(),
                        
                        TextInput::make('discount_per_person')
                            ->hidden()
                            ->dehydrated(),

                        TextInput::make('public_url_display')
                            ->hidden()
                            ->dehydrated(false),

                        FileUpload::make('request_file_path')
                            ->label('ไฟล์รายชื่อ (ส่งให้ทิพย)')
                            ->acceptedFileTypes([
                                'application/pdf', 
                                'application/vnd.ms-excel', 
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/zip'
                            ])
                            ->maxSize(307200) // 300MB
                            ->disk('public')
                            ->directory('policy-requests')
                            ->downloadable()
                            ->openable()
                            ->helperText('รองรับ PDF, Excel (.xls/.xlsx), ZIP ขนาดสูงสุด 300MB'),

                        Textarea::make('our_notes')
                            ->label('หมายเหตุเจ้าหน้าที่TKR')
                            ->placeholder('บันทึกเพิ่มเติมสำหรับทีมงาน')
                            ->rows(3),
                    ]),

                // === Row 1: ข้อมูลหลัก 2 คอลัมน์ ===
                Section::make('🏢 ข้อมูลจากทิพยประกันภัย')
                    ->description('ข้อมูลที่ได้รับจากทิพยประกันภัย และข้อมูลการทำงานของเจ้าหน้าที่ทิพย')
                    ->icon('heroicon-m-building-office-2')
                    ->schema([
                        Textarea::make('tipaya_notes')
                            ->label('บันทึกถึงเจ้าหน้าที่ทิพยประกันภัย')
                            ->placeholder('บันทึกหมายเหตุที่ต้องการส่งให้เจ้าหน้าที่ทิพยประกันภัย')
                            ->rows(3)
                            ->helperText('บันทึกนี้เจ้าหน้าที่ทิพยจะเห็นในหน้า Staff Form'),

                        TextInput::make('staff_name')
                            ->label('ชื่อเจ้าหน้าที่ทิพย (ที่กรอกข้อมูล)')
                            ->helperText('ชื่อเจ้าหน้าที่ทิพยที่จะกรอกข้อมูลในระบบ'),

                        Textarea::make('staff_notes')
                            ->label('หมายเหตุจากเจ้าหน้าที่ทิพย')
                            ->placeholder('เจ้าหน้าที่ทิพยจะกรอกข้อมูลนี้ผ่าน Staff Form')
                            ->rows(3)
                            ->helperText('ข้อมูลนี้จะถูกกรอกโดยเจ้าหน้าที่ทิพยผ่าน Staff URL'),

                        DateTimePicker::make('staff_updated_at')
                            ->label('วันที่เจ้าหน้าที่ทิพยอัพเดตข้อมูลล่าสุด')
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('จะอัพเดทอัตโนมัติเมื่อเจ้าหน้าที่ทิพยกรอกข้อมูล'),

                        FileUpload::make('staff_file_path')
                            ->label('ไฟล์จากเจ้าหน้าที่ทิพย')
                            ->acceptedFileTypes(['application/pdf', 'application/zip', 'image/*'])
                            ->maxSize(102400) // 100MB
                            ->disk('public')
                            ->directory('staff-files')
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->visibility('public')
                            ->helperText('เจ้าหน้าที่ทิพยจะอัพโหลดผ่าน Staff Form (PDF, ZIP, รูปภาพ สูงสุด 100MB)')
                            ->hintIcon('heroicon-m-document-arrow-down')
                            ->hintColor('info'),

                        TextInput::make('public_url_preview')
                            ->label('🔗 Public URL สำหรับลูกค้า')
                            ->disabled()
                            ->reactive()
                            ->default(function (callable $get, $record) {
                                if ($record && $record->ticket_number) {
                                    return route('public.ticket.check', ['ticket_number' => $record->ticket_number]);
                                } elseif ($get('ticket_number')) {
                                    return route('public.ticket.check', ['ticket_number' => $get('ticket_number')]);
                                }
                                return 'จะแสดงหลังจากกรอกหมายเลขตั๋ว';
                            })
                            ->afterStateHydrated(function (TextInput $component, $state, callable $get, $record) {
                                if ($record && $record->ticket_number) {
                                    $component->state(route('public.ticket.check', ['ticket_number' => $record->ticket_number]));
                                } elseif ($get('ticket_number')) {
                                    $component->state(route('public.ticket.check', ['ticket_number' => $get('ticket_number')]));
                                } else {
                                    $component->state('จะแสดงหลังจากกรอกหมายเลขตั๋ว');
                                }
                            })
                            ->helperText('ลิงค์นี้ให้ลูกค้าใช้ตรวจสอบสถานะคำขอ - คลิกขวาเพื่อคัดลอก')
                            ->extraAttributes(['class' => 'cursor-pointer'])
                            ->dehydrated(false),

                        TextInput::make('staff_url_preview')
                            ->label('👨‍💼 Staff URL สำหรับพนักงานทิพย')
                            ->disabled()
                            ->reactive()
                            ->default(function (callable $get, $record) {
                                if ($record && $record->ticket_number && $record->access_code) {
                                    return route('ticket.staff-verify', [
                                        'ticket_number' => $record->ticket_number,
                                        'access_code' => $record->access_code
                                    ]);
                                } elseif ($get('ticket_number') && $get('access_code')) {
                                    return route('ticket.staff-verify', [
                                        'ticket_number' => $get('ticket_number'),
                                        'access_code' => $get('access_code')
                                    ]);
                                }
                                return 'จะแสดงหลังจากกรอกข้อมูลครบ';
                            })
                            ->afterStateHydrated(function (TextInput $component, $state, callable $get, $record) {
                                if ($record && $record->ticket_number && $record->access_code) {
                                    $component->state(route('ticket.staff-verify', [
                                        'ticket_number' => $record->ticket_number,
                                        'access_code' => $record->access_code
                                    ]));
                                } elseif ($get('ticket_number') && $get('access_code')) {
                                    $component->state(route('ticket.staff-verify', [
                                        'ticket_number' => $get('ticket_number'),
                                        'access_code' => $get('access_code')
                                    ]));
                                } else {
                                    $component->state('จะแสดงหลังจากกรอกข้อมูลครบ');
                                }
                            })
                            ->helperText('ลิงค์นี้ให้เจ้าหน้าที่ทิพยเข้าระบบเพื่ออัพเดทข้อมูล - คลิกขวาเพื่อคัดลอก')
                            ->extraAttributes(['class' => 'cursor-pointer'])
                            ->dehydrated(false),
                    ]),

                // === Row 2: ข้อมูลระบบและการชำระเงิน ===
                Section::make('⚙️ ข้อมูลระบบ')
                    ->description('หมายเลขตั๋ว, รหัสเข้าถึง และสถานะ')
                    ->icon('heroicon-m-cog-6-tooth')
                    ->schema([
                        TextInput::make('ticket_number')
                            ->label('หมายเลขตั๋ว')
                            ->default(fn () => 'TKR-' . date('Ymd') . '-' . Str::random(4))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                if ($state) {
                                    $publicUrl = route('public.ticket.check', ['ticket_number' => $state]);
                                    $set('public_url_display', $publicUrl);
                                    $set('public_url_preview', $publicUrl);
                                    
                                    // อัพเดท Staff URL ถ้ามี access_code
                                    $accessCode = $get('access_code');
                                    if ($accessCode) {
                                        $staffUrl = route('ticket.staff-verify', [
                                            'ticket_number' => $state,
                                            'access_code' => $accessCode
                                        ]);
                                        $set('staff_url_preview', $staffUrl);
                                    }
                                } else {
                                    $set('public_url_display', 'จะแสดงหลังจากกรอกหมายเลขตั๋ว');
                                    $set('public_url_preview', 'จะแสดงหลังจากกรอกหมายเลขตั๋ว');
                                    $set('staff_url_preview', 'จะแสดงหลังจากกรอกข้อมูลครบ');
                                }
                            }),

                        TextInput::make('access_code')
                            ->label('รหัสเข้าถึง')
                            ->default(fn () => Str::random(10))
                            ->required()
                            ->maxLength(10)
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                $ticketNumber = $get('ticket_number');
                                if ($state && $ticketNumber) {
                                    $staffUrl = route('ticket.staff-verify', [
                                        'ticket_number' => $ticketNumber,
                                        'access_code' => $state
                                    ]);
                                    $set('staff_url_preview', $staffUrl);
                                } else {
                                    $set('staff_url_preview', 'จะแสดงหลังจากกรอกข้อมูลครบ');
                                }
                            })
                            ->helperText('รหัสสุ่ม 10 ตัวอักษรสำหรับให้ลูกค้าตรวจสอบสถานะ'),

                        Select::make('status')
                            ->label('สถานะ')
                            ->options([
                                'draft' => '📝 ร่าง',
                                'submitted' => '📤 ส่งให้ทิพย',
                                'processing' => '⏳ ทิพยกำลังดำเนินการ',
                                'completed' => '✅ เสร็จสมบูรณ์',
                                'rejected' => '❌ ถูกปฏิเสธ',
                            ])
                            ->default('draft'),
                    ]),

                Section::make('💰 ข้อมูลการชำระเงิน')
                    ->description('สถานะและรายละเอียดการชำระเงิน')
                    ->icon('heroicon-m-banknotes')
                    ->schema([
                        Select::make('payment_status')
                            ->label('สถานะการชำระเงิน')
                            ->options([
                                'pending' => '⏳ รอชำระ',
                                'partial' => '💰 ชำระบางส่วน', 
                                'paid' => '✅ ชำระครบแล้ว',
                                'refunded' => '🔄 คืนเงิน',
                            ])
                            ->default('pending'),

                        TextInput::make('paid_amount')
                            ->label('จำนวนเงินที่ชำระ')
                            ->numeric()
                            ->prefix('฿')
                            ->default(0),

                        DateTimePicker::make('paid_at')
                            ->label('วันที่ชำระเงิน')
                            ->displayFormat('d/m/Y H:i'),
                    ]),

                // === Row 3: การจัดการงาน (เต็มแถว) ===
                Section::make('📋 การจัดการงาน')
                    ->description('การมอบหมายงานและติดตามความคืบหน้า')
                    ->icon('heroicon-m-clipboard-document-check')
                    ->schema([
                        Select::make('assigned_to')
                            ->label('มอบหมายให้')
                            ->relationship('assignedTo', 'name')
                            ->searchable()
                            ->preload(),

                        Select::make('priority')
                            ->label('ความสำคัญ')
                            ->options([
                                'low' => '🟢 ต่ำ',
                                'normal' => '🟡 ปกติ',
                                'high' => '🔴 สูง',
                                'urgent' => '🚨 ด่วนมาก',
                            ])
                            ->default('normal'),

                        DateTimePicker::make('due_date')
                            ->label('กำหนดเสร็จ')
                            ->displayFormat('d/m/Y H:i'),
                    ]),
            ]);
    }

    /**
     * คำนวนราคารวมอัตโนมัติ
     */
    private static function calculateTotalAmount(callable $set, callable $get): void
    {
        $insuranceType = $get('insurance_type');
        $duration = $get('duration');
        $personCount = (int) $get('person_count');
        $discountAmount = (float) $get('discount_amount');

        if (!$insuranceType || !$duration || !$personCount) {
            return;
        }

        // ดึงราคาจากฐานข้อมูล Product
        $product = \App\Models\Product::where('type', $insuranceType)
            ->where('duration', $duration)
            ->where('is_active', true)
            ->first();

        $pricePerPerson = $product ? $product->base_price : 0;

        // คำนวนราคารวม = (ราคาต่อคน - ส่วนลดต่อคน) × จำนวนคน
        $totalAmount = ($pricePerPerson - $discountAmount) * $personCount;
        
        // ป้องกันราคาติดลบ
        $totalAmount = max(0, $totalAmount);

        // ตั้งค่าฟิลด์ที่จำเป็นสำหรับฐานข้อมูล
        $set('base_price_per_person', $pricePerPerson);
        $set('discount_per_person', $discountAmount);
        $set('total_amount', $totalAmount);
    }
}
