<?php

namespace App\Filament\Resources\Payments;

use App\Models\Payment;
use App\Models\PolicyTicket;
use App\Models\Customer;
use App\Models\CreditTransaction;
use App\Filament\Resources\Payments\Pages;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $navigationLabel = 'การชำระเงิน';
    
    protected static ?string $modelLabel = 'การชำระเงิน';
    
    protected static ?string $pluralModelLabel = 'การชำระเงิน';
    
    protected static ?int $navigationSort = 15;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('customer_id')
                    ->label('ลูกค้า')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        // รีเซ็ต policy ticket เมื่อเปลี่ยนลูกค้า
                        $set('policy_ticket_id', null);
                    }),
                
                Select::make('policy_ticket_id')
                    ->label('ตั๋วประกัน')
                    ->options(function (callable $get) {
                        $customerId = $get('customer_id');
                        if (!$customerId) {
                            return [];
                        }
                        
                        return PolicyTicket::where('customer_id', $customerId)
                            ->where('payment_status', '!=', 'paid')
                            ->pluck('ticket_number', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state) {
                            $ticket = PolicyTicket::find($state);
                            if ($ticket) {
                                $remainingAmount = $ticket->getRemainingAmount();
                                $set('amount', $remainingAmount);
                            }
                        }
                    })
                    ->helperText('แสดงเฉพาะตั๋วที่ยังชำระไม่ครบ'),
                
                TextInput::make('amount')
                    ->label('จำนวนเงินที่รับ')
                    ->numeric()
                    ->prefix('฿')
                    ->minValue(0.01)
                    ->step(0.01)
                    ->required(),
                
                Select::make('payment_method')
                    ->label('วิธีการชำระ')
                    ->options([
                        'cash' => '💵 เงินสด',
                        'bank_transfer' => '🏦 โอนเงิน',
                        'credit_card' => '💳 บัตรเครดิต',
                        'other' => '� อื่นๆ',
                    ])
                    ->required(),
                
                DateTimePicker::make('payment_date')
                    ->label('วันที่ชำระ')
                    ->default(now())
                    ->required(),
                
                TextInput::make('reference_number')
                    ->label('หมายเลขอ้างอิง')
                    ->placeholder('เลขที่โอน, เลขเช็ค ฯลฯ'),
                
                FileUpload::make('attachments')
                    ->label('แนบสลิปการโอน / เอกสาร')
                    ->image()
                    ->acceptedFileTypes(['image/*', 'application/pdf'])
                    ->multiple()
                    ->maxFiles(5)
                    ->maxSize(10240) // 10MB
                    ->directory('payment-slips')
                    ->visibility('private')
                    ->downloadable()
                    ->previewable()
                    ->reorderable()
                    ->helperText('สามารถแนบไฟล์รูปภาพ หรือ PDF ได้ (สูงสุด 5 ไฟล์, ไฟล์ละไม่เกิน 10MB)'),
                
                Textarea::make('notes')
                    ->label('หมายเหตุ')
                    ->rows(3)
                    ->placeholder('หมายเหตุเพิ่มเติม'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('payment_date')
                    ->label('วันที่ชำระ')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('customer.name')
                    ->label('ลูกค้า')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('policyTicket.ticket_number')
                    ->label('ตั๋วประกัน')
                    ->searchable(),
                
                TextColumn::make('amount')
                    ->label('จำนวนเงิน')
                    ->money('THB')
                    ->sortable(),
                
                TextColumn::make('payment_method')
                    ->label('วิธีชำระ')
                    ->formatStateUsing(fn ($state): string => match($state) {
                        'cash' => '💵 เงินสด',
                        'bank_transfer' => '🏦 โอนเงิน',
                        'credit_card' => '💳 บัตรเครดิต',
                        'other' => '� อื่นๆ',
                        default => $state,
                    })
                    ->badge()
                    ->sortable(),
                
                TextColumn::make('reference_number')
                    ->label('หมายเลขอ้างอิง')
                    ->placeholder('-'),
                
                TextColumn::make('attachments')
                    ->label('ไฟล์แนบ')
                    ->formatStateUsing(function ($state) {
                        if (!$state || !is_array($state) || empty($state)) {
                            return '-';
                        }
                        $count = count($state);
                        return "📎 {$count} ไฟล์";
                    })
                    ->color('gray'),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('payment_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'view' => Pages\ViewPayment::route('/{record}'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
