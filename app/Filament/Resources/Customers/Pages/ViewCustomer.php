<?php

namespace App\Filament\Resources\Customers\Pages;

use App\Filament\Resources\Customers\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCustomer extends ViewRecord
{
    protected static string $resource = CustomerResource::class;

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
