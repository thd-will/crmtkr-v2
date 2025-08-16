<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Models\Payment;
use App\Models\PolicyTicket;
use App\Models\CreditTransaction;
use App\Filament\Resources\Payments\PaymentResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    public function mount(): void
    {
        parent::mount();
        
        // ตั้งค่าเริ่มต้นจาก URL parameters
        $customerId = request()->get('customer_id');
        $policyTicketId = request()->get('policy_ticket_id');
        
        if ($customerId || $policyTicketId) {
            $data = [];
            
            if ($customerId) {
                $data['customer_id'] = (int) $customerId;
            }
            
            if ($policyTicketId) {
                $policyTicket = PolicyTicket::find($policyTicketId);
                if ($policyTicket) {
                    $data['policy_ticket_id'] = (int) $policyTicketId;
                    $data['customer_id'] = $policyTicket->customer_id;
                    $data['amount'] = $policyTicket->getRemainingAmount();
                }
            }
            
            $this->form->fill($data);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // ตั้งสถานะเป็น confirmed เป็นค่าเริ่มต้น
        $data['status'] = 'confirmed';
        
        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        // สร้างรายการชำระเงิน
        $payment = Payment::create($data);
        
        // อัปเดตสถานะการชำระของ Policy Ticket และคืนเครดิต (ถ้าจำเป็น)
        $this->updatePolicyTicketAndCredit($payment);
        
        return $payment;
    }

    protected function updatePolicyTicketAndCredit(Payment $payment): void
    {
        $policyTicket = $payment->policyTicket;
        $customer = $policyTicket->customer;
        
        // เพิ่มจำนวนเงินที่ชำระ
        $policyTicket->paid_amount = ($policyTicket->paid_amount ?? 0) + $payment->amount;
        
        // ตรวจสอบสถานะการชำระ
        if ($policyTicket->paid_amount >= $policyTicket->total_amount) {
            // ชำระครบแล้ว
            $policyTicket->payment_status = 'paid';
            $policyTicket->paid_at = now();
            
            // คืนเครดิตให้ลูกค้า
            if ($policyTicket->canRefundCredit()) {
                // คำนวณจำนวนเครดิตที่ต้องคืน
                $creditToRefund = $policyTicket->total_amount;
                
                // อัปเดตเครดิตของลูกค้า
                $customer->increment('current_credit', $creditToRefund);
                
                // สร้างรายการ Credit Transaction สำหรับการคืนเครดิต
                CreditTransaction::create([
                    'customer_id' => $customer->id,
                    'type' => 'credit',
                    'amount' => $creditToRefund,
                    'balance_after' => $customer->current_credit,
                    'description' => "คืนเครดิตจากการชำระตั๋วประกัน #{$policyTicket->ticket_number}",
                ]);
                
                // แสดงข้อความแจ้งเตือน
                Notification::make()
                    ->title('คืนเครดิตสำเร็จ')
                    ->body("คืนเครดิต {$creditToRefund} บาทให้ลูกค้า {$customer->name}")
                    ->success()
                    ->send();
            }
        } else {
            // ชำระบางส่วน
            $policyTicket->payment_status = 'partial';
        }
        
        $policyTicket->save();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
