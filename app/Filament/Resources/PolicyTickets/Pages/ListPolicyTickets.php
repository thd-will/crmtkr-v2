<?php

namespace App\Filament\Resources\PolicyTickets\Pages;

use App\Filament\Resources\PolicyTickets\PolicyTicketResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPolicyTickets extends ListRecords
{
    protected static string $resource = PolicyTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
