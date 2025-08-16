<?php

namespace App\Filament\Resources\PolicyTickets\Pages;

use App\Filament\Resources\PolicyTickets\PolicyTicketResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPolicyTicket extends EditRecord
{
    protected static string $resource = PolicyTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
