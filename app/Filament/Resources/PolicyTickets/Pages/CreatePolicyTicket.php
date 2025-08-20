<?php

namespace App\Filament\Resources\PolicyTickets\Pages;

use App\Filament\Resources\PolicyTickets\PolicyTicketResource;
use App\Models\Customer;
use App\Models\CreditTransaction;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreatePolicyTicket extends CreateRecord
{
    protected static string $resource = PolicyTicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // เพิ่มผู้สร้าง ticket
        $data['created_by'] = auth()->id();
        
        // สร้าง access_code สุ่ม 10 ตัวอักษร
        $data['access_code'] = \Illuminate\Support\Str::random(10);
        
        // คำนวณและตั้งค่า pricing fields ที่จำเป็น
        if (isset($data['duration']) && isset($data['person_count']) && isset($data['insurance_type'])) {
            $insuranceType = $data['insurance_type'];
            $duration = $data['duration'];
            $personCount = (int) $data['person_count'];
            $discountAmount = (float) ($data['discount_amount'] ?? 0);
            
            // ดึงราคาจากฐานข้อมูล Product
            $product = \App\Models\Product::where('type', $insuranceType)
                ->where('duration', $duration)
                ->where('is_active', true)
                ->first();
            
            $pricePerPerson = $product ? $product->base_price : 0;
            
            $data['base_price_per_person'] = $pricePerPerson;
            $data['discount_per_person'] = $discountAmount;
            
            // คำนวนราคารวมใหม่เพื่อให้แน่ใจ
            $totalAmount = ($pricePerPerson - $discountAmount) * $personCount;
            $data['total_amount'] = max(0, $totalAmount);
        }
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $customer = Customer::find($this->record->customer_id);
        $totalAmount = $this->record->total_amount;

        // ตรวจสอบเครดิตเพียงพอ
        if ($customer->current_credit < $totalAmount) {
            // ลบ record ที่เพิ่งสร้าง
            $this->record->delete();
            
            Notification::make()
                ->title('⚠️ เครดิตไม่เพียงพอ')
                ->body("ลูกค้ามีเครดิต ฿" . number_format($customer->current_credit, 2) . " แต่ต้องการ ฿" . number_format($totalAmount, 2))
                ->danger()
                ->persistent()
                ->send();
                
            $this->halt();
            return;
        }

        // สร้าง Credit Transaction (หักเครดิต)
        $creditTransaction = CreditTransaction::create([
            'customer_id' => $customer->id,
            'policy_ticket_id' => $this->record->id,
            'type' => 'debit',
            'amount' => $totalAmount,
            'balance_after' => $customer->current_credit - $totalAmount,
            'description' => "ซื้อตั๋วประกัน #{$this->record->ticket_number} (ประเภท: {$this->record->insurance_type}, ระยะเวลา: {$this->record->duration}, จำนวน: {$this->record->person_count} คน)",
        ]);

        // อัปเดตเครดิตลูกค้า
        $customer->update([
            'current_credit' => $creditTransaction->balance_after
        ]);

        // แจ้งเตือนสำเร็จ
        Notification::make()
            ->title('✅ สร้างตั๋วประกันสำเร็จ')
            ->body("หักเครดิต ฿" . number_format($totalAmount, 2) . " คงเหลือ ฿" . number_format($creditTransaction->balance_after, 2))
            ->success()
            ->send();
    }
}
