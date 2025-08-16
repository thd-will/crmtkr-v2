<?php

namespace App\Filament\Resources\CreditTransactions\Pages;

use App\Filament\Resources\CreditTransactions\CreditTransactionResource;
use App\Models\Customer;
use Filament\Resources\Pages\CreateRecord;

class CreateCreditTransaction extends CreateRecord
{
    protected static string $resource = CreditTransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // คำนวน balance
        $customer = Customer::find($data['customer_id']);
        $currentBalance = $customer->current_credit ?? 0;
        
        if ($data['type'] === 'credit') {
            $data['balance_after'] = $currentBalance + $data['amount'];
        } else {
            $data['balance_after'] = $currentBalance - $data['amount'];
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // อัปเดต current_credit ของลูกค้า
        $customer = Customer::find($this->record->customer_id);
        $customer->update(['current_credit' => $this->record->balance_after]);
    }
}
