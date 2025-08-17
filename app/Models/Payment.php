<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Payment extends Model
{
    use LogsActivity;

    protected $fillable = [
        'customer_id',
        'policy_ticket_id',
        'amount',
        'payment_method',
        'payment_date',
        'reference_number',
        'notes',
        'status',
        'attachments',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'attachments' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['amount', 'payment_method', 'reference_number', 'status', 'payment_date'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => "สร้างการชำระเงินใหม่: {$this->amount} บาท สำหรับ {$this->customer?->name}",
                'updated' => "แก้ไขการชำระเงิน: {$this->amount} บาท ({$this->customer?->name})",
                'deleted' => "ลบการชำระเงิน: {$this->amount} บาท ({$this->customer?->name})",
                default => $eventName,
            });
    }

    protected static function booted()
    {
        static::created(function ($payment) {
            $payment->policyTicket?->updatePaymentStatus();
        });

        static::updated(function ($payment) {
            if ($payment->wasChanged('amount') || $payment->wasChanged('status')) {
                $payment->policyTicket?->updatePaymentStatus();
            }
        });

        static::deleted(function ($payment) {
            $payment->policyTicket?->updatePaymentStatus();
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function policyTicket(): BelongsTo
    {
        return $this->belongsTo(PolicyTicket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
