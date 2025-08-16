<?php

namespace App\Filament\Resources\CreditTransactions\Pages;

use App\Filament\Resources\CreditTransactions\CreditTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCreditTransaction extends EditRecord
{
    protected static string $resource = CreditTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('ดู')
                ->icon('heroicon-o-eye'),
            Actions\DeleteAction::make()
                ->label('ลบ')
                ->icon('heroicon-o-trash'),
        ];
    }
}
