<?php

namespace App\Filament\Resources\CreditTransactions\Pages;

use App\Filament\Resources\CreditTransactions\CreditTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCreditTransaction extends ViewRecord
{
    protected static string $resource = CreditTransactionResource::class;

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
