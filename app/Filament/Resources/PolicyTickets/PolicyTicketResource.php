<?php

namespace App\Filament\Resources\PolicyTickets;

use App\Filament\Resources\PolicyTickets\Pages\CreatePolicyTicket;
use App\Filament\Resources\PolicyTickets\Pages\EditPolicyTicket;
use App\Filament\Resources\PolicyTickets\Pages\ListPolicyTickets;
use App\Filament\Resources\PolicyTickets\Pages\ViewPolicyTicket;
use App\Filament\Resources\PolicyTickets\Schemas\PolicyTicketForm;
use App\Filament\Resources\PolicyTickets\Tables\PolicyTicketsTable;
use App\Models\PolicyTicket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PolicyTicketResource extends Resource
{
    protected static ?string $model = PolicyTicket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $recordTitleAttribute = 'ticket_number';
    
    protected static ?string $navigationLabel = 'ตั๋วประกัน';
    
    protected static ?string $modelLabel = 'ตั๋วประกัน';
    
    protected static ?string $pluralModelLabel = 'ตั๋วประกัน';
    
    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return PolicyTicketForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PolicyTicketsTable::configure($table);
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
            'index' => ListPolicyTickets::route('/'),
            'create' => CreatePolicyTicket::route('/create'),
            'view' => ViewPolicyTicket::route('/{record}'),
            'edit' => EditPolicyTicket::route('/{record}/edit'),
        ];
    }
}
