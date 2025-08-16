<?php

namespace App\Filament\Resources;

use App\Models\User;
use App\Filament\Resources\UserResource\Pages;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use BackedEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'บริหารจัดการผู้ใช้';
    
    protected static ?string $modelLabel = 'ผู้ใช้';
    
    protected static ?string $pluralModelLabel = 'ผู้ใช้';
    
    protected static ?int $navigationSort = 100;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('ชื่อผู้ใช้')
                    ->required()
                    ->maxLength(255),
                
                TextInput::make('email')
                    ->label('อีเมล')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                
                TextInput::make('password')
                    ->label('รหัสผ่าน')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->minLength(8)
                    ->same('passwordConfirmation')
                    ->dehydrated(fn ($state): bool => filled($state))
                    ->dehydrateStateUsing(fn ($state): string => Hash::make($state)),
                
                TextInput::make('passwordConfirmation')
                    ->label('ยืนยันรหัสผ่าน')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->minLength(8)
                    ->dehydrated(false),
                
                Select::make('role')
                    ->label('บทบาท')
                    ->options([
                        'admin' => 'ผู้ดูแลระบบ',
                        'manager' => 'ผู้จัดการ',
                        'staff' => 'พนักงาน',
                    ])
                    ->default('staff')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('ชื่อผู้ใช้')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('email')
                    ->label('อีเมล')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('role')
                    ->label('บทบาท')
                    ->formatStateUsing(fn ($state): string => match($state) {
                        'admin' => '👑 ผู้ดูแลระบบ',
                        'manager' => '📊 ผู้จัดการ',
                        'staff' => '👤 พนักงาน',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn ($state): string => match($state) {
                        'admin' => 'danger',
                        'manager' => 'warning',
                        'staff' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('วันที่สร้าง')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('แก้ไขล่าสุด')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
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
            ]);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
