<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerFollowUp extends Model
{
    protected $fillable = [
        'customer_id',
        'type',
        'status', 
        'follow_up_date',
        'notes',
        'assigned_to',
        'completed_at',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('follow_up_date', today());
    }

    public function scopeOverdue($query)
    {
        return $query->where('follow_up_date', '<', today())
                    ->where('status', '!=', 'completed');
    }
}
