<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class CreditTransaction extends Model
{
    use LogsActivity;

    protected $fillable = [
        'customer_id',
        'policy_ticket_id',
        'type',
        'amount',
        'balance_after',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        $action = $this->type === 'credit' ? 'เพิ่มเครดิต' : 'ใช้เครดิต';
        return LogOptions::defaults()
            ->logOnly(['type', 'amount', 'balance_after', 'description'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => "{$action}: {$this->amount} บาท สำหรับ {$this->customer?->name} (คงเหลือ: {$this->balance_after})",
                'updated' => "แก้ไขรายการเครดิต: {$this->amount} บาท ({$this->customer?->name})",
                'deleted' => "ลบรายการเครดิต: {$action} {$this->amount} บาท ({$this->customer?->name})",
                default => $eventName,
            });
    }

    // Relationships
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

    // Helper methods
    public function isCredit(): bool
    {
        return $this->type === 'credit';
    }

    public function isDebit(): bool
    {
        return $this->type === 'debit';
    }
}
