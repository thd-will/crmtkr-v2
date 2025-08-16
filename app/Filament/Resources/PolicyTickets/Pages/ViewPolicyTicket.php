<?php

namespace App\Filament\Resources\PolicyTickets\Pages;

use App\Filament\Resources\PolicyTickets\PolicyTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPolicyTicket extends ViewRecord
{
    protected static string $resource = PolicyTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('แก้ไข')
                ->icon('heroicon-o-pencil'),
            Actions\DeleteAction::make()
                ->label('ลบ')
                ->icon('heroicon-o-trash'),
        ];
    }
}
