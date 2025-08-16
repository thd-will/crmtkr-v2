<?php

namespace App\Filament\Resources\CreditTransactions;

use App\Models\CreditTransaction;
use App\Filament\Resources\CreditTransactions\Pages;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
use UnitEnum;
use Illuminate\Database\Eloquent\Builder;

class CreditTransactionResource extends Resource
{
    protected static ?string $model = CreditTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'ประวัติการใช้เครดิต';

    protected static UnitEnum|string|null $navigationGroup = 'การจัดการเครดิต';
    
    protected static ?string $modelLabel = 'รายการเครดิต';
    
    protected static ?string $pluralModelLabel = 'รายการเครดิต';
    
    protected static ?int $navigationSort = 21;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->label('ลูกค้า')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                
                Select::make('policy_ticket_id')
                    ->label('ตั๋วประกัน')
                    ->relationship('policyTicket', 'ticket_number')
                    ->searchable()
                    ->preload()
                    ->helperText('เลือกถ้าเกี่ยวข้องกับตั๋วประกัน'),
                
                Select::make('type')
                    ->label('ประเภท')
                    ->options([
                        'credit' => '💰 เติมเครดิต',
                        'debit' => '💸 หักเครดิต',
                    ])
                    ->required()
                    ->default('credit'),
                
                TextInput::make('amount')
                    ->label('จำนวนเงิน')
                    ->numeric()
                    ->prefix('฿')
                    ->minValue(0.01)
                    ->step(0.01)
                    ->required(),
                
                TextInput::make('description')
                    ->label('รายละเอียด')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('เช่น เติมเครดิตโดยโอน, ซื้อตั๋วประกัน'),
                
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
                TextColumn::make('created_at')
                    ->label('วันที่')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('customer.name')
                    ->label('ลูกค้า')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('type')
                    ->label('ประเภท')
                    ->formatStateUsing(fn ($state): string => $state === 'credit' ? '💰 เติมเครดิต' : '💸 หักเครดิต')
                    ->badge()
                    ->color(fn ($state): string => $state === 'credit' ? 'success' : 'danger')
                    ->sortable(),
                
                TextColumn::make('amount')
                    ->label('จำนวนเงิน')
                    ->money('THB')
                    ->sortable(),
                
                TextColumn::make('balance_after')
                    ->label('ยอดคงเหลือ')
                    ->money('THB')
                    ->sortable(),
                
                TextColumn::make('description')
                    ->label('รายละเอียด')
                    ->searchable()
                    ->wrap(),
                
                TextColumn::make('policyTicket.ticket_number')
                    ->label('ตั๋วประกัน')
                    ->searchable()
                    ->placeholder('-'),
                
                TextColumn::make('user.name')
                    ->label('ผู้ทำรายการ')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListCreditTransactions::route('/'),
            'create' => Pages\CreateCreditTransaction::route('/create'),
            'view' => Pages\ViewCreditTransaction::route('/{record}'),
            'edit' => Pages\EditCreditTransaction::route('/{record}/edit'),
        ];
    }
}
