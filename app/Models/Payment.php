<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
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
