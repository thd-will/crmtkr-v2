<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

class PolicyTicket extends Model
{
    use HasFactory, LogsActivity;
    
    protected $fillable = [
        'customer_id',
        'ticket_number',
        'access_code',
        'insurance_type',
        'duration', 
        'person_count',
        'base_price_per_person',
        'discount_per_person',
        'total_amount',
        'policy_count',
        'status',
        'payment_status',
        'paid_amount',
        'paid_at',
        'request_file_path',
        'policy_file_path',
        'staff_name',
        'staff_notes',
        'staff_file_path',
        'staff_updated_at',
        'our_notes',
        'tipaya_notes',
        'created_by',
        'submitted_by',
        'submitted_at',
        'processed_by',
        'processed_at',
        'completed_by',
        'completed_at',
        'assigned_to',
        'last_updated_by',
        'due_date',
        'priority',
        'reminder_sent_at',
        'revision_count'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'processed_at' => 'datetime', 
        'completed_at' => 'datetime',
        'paid_at' => 'datetime',
        'staff_updated_at' => 'datetime',
        'due_date' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'base_price_per_person' => 'decimal:2',
        'discount_per_person' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    /**
     * ความสัมพันธ์กับลูกค้า
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * ผู้สร้างตั๋ว
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * ผู้ส่งตั๋วให้ทิพย
     */
    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * ผู้ดำเนินการตั๋ว
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * ผู้ทำตั๋วให้เสร็จ
     */
    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * ผู้ที่ได้รับมอบหมาย
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * ผู้อัปเดตล่าสุด
     */
    public function lastUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    /**
     * คำนวณราคารวมใหม่ตามลูกค้าและข้อมูลปัจจุบัน
     */
    public function calculateTotalAmount(): float
    {
        if (!$this->customer || !$this->insurance_type || !$this->duration) {
            return 0;
        }

        $product = Product::where('type', $this->insurance_type)
                         ->where('duration', $this->duration)
                         ->first();
        
        if (!$product) return 0;

        $basePrice = $product->base_price;
        $discount = $this->customer->getDiscountFor($this->insurance_type, $this->duration);
        
        $this->base_price_per_person = $basePrice;
        $this->discount_per_person = $discount;
        $this->total_amount = ($basePrice - $discount) * ($this->person_count ?? 1);
        
        return $this->total_amount;
    }

    /**
     * ตรวจสอบว่าตั๋วสามารถแก้ไขได้หรือไม่
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft', 'rejected']);
    }

    /**
     * ตรวจสอบว่าตั๋วสามารถส่งได้หรือไม่
     */
    public function canBeSubmitted(): bool
    {
        return $this->status === 'draft' && 
               !empty($this->request_file_path) &&
               $this->person_count > 0;
    }

    /**
     * รับสถานะเป็นข้อความภาษาไทย
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'ร่าง',
            'submitted' => 'ส่งแล้ว',
            'processing' => 'กำลังดำเนินการ',
            'completed' => 'เสร็จแล้ว', 
            'rejected' => 'ถูกปฏิเสธ',
            default => $status,
        };
    }

    /**
     * Activity Log Configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(function(string $eventName) {
                $customerName = $this->customer ? $this->customer->name : 'ลูกค้าไม่ระบุ';
                return match($eventName) {
                    'created' => "สร้างตั๋วประกันใหม่: {$this->ticket_number} สำหรับ {$customerName}",
                    'updated' => "แก้ไขตั๋วประกัน: {$this->ticket_number} ({$customerName})",
                    'deleted' => "ลบตั๋วประกัน: {$this->ticket_number} ({$customerName})",
                    default => $eventName
                };
            })
            ->useLogName('policy_ticket');
    }

    /**
     * Tap into the activity being logged
     */
    public function tapActivity(Activity $activity, string $eventName): void
    {
        // บันทึก IP address
        $activity->properties = $activity->properties->put('ip_address', request()->ip());
        
        // บันทึก User Agent
        $activity->properties = $activity->properties->put('user_agent', request()->userAgent());
        
        // บันทึกผู้ใช้ที่ทำการแก้ไข (ถ้ามี)
        if (auth()->check()) {
            $activity->properties = $activity->properties->put('user_id', auth()->id());
            $activity->properties = $activity->properties->put('user_name', auth()->user()->name);
        }
        
        // บันทึกข้อมูลตั๋วที่เกี่ยวข้อง
        $activity->properties = $activity->properties->put('ticket_number', $this->ticket_number);
        $activity->properties = $activity->properties->put('customer_id', $this->customer_id);
        $activity->properties = $activity->properties->put('insurance_type', $this->insurance_type);
        $activity->properties = $activity->properties->put('status', $this->status);
        
        // บันทึกเวลาในรูปแบบ readable
        $activity->properties = $activity->properties->put('performed_at', now()->format('d/m/Y H:i:s'));
    }

    /**
     * รับไอคอนสถานะ
     */
    public function getStatusIconAttribute(): string
    {
        return match($this->status) {
            'draft' => '📝',
            'submitted' => '📤',
            'processing' => '⏳',
            'completed' => '✅',
            'rejected' => '❌',
            default => '❓'
        };
    }

    /**
     * Helper methods สำหรับสถานะการชำระเงิน
     */
    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isPartialPaid(): bool
    {
        return $this->payment_status === 'partial';
    }

    public function getRemainingAmount(): float
    {
        $totalPaid = $this->payments()
            ->where('status', 'confirmed')
            ->sum('amount');
        
        return max(0, $this->total_amount - $totalPaid);
    }

    public function getTotalPaidAmount(): float
    {
        return $this->payments()
            ->where('status', 'confirmed') 
            ->sum('amount');
    }

    public function updatePaymentStatus(): void
    {
        $totalPaid = $this->getTotalPaidAmount();
        
        if ($totalPaid == 0) {
            $this->payment_status = 'pending';
        } elseif ($totalPaid >= $this->total_amount) {
            $this->payment_status = 'paid';
            $this->paid_at = now();
        } else {
            $this->payment_status = 'partial';
        }
        
        $this->paid_amount = $totalPaid;
        $this->save();
    }

    public function canRefundCredit(): bool
    {
        return $this->isPaid() && $this->total_amount > 0;
    }

    /**
     * ความสัมพันธ์กับการชำระเงิน
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * ความสัมพันธ์กับธุรกรรมเครดิต
     */
    public function creditTransactions(): HasMany
    {
        return $this->hasMany(CreditTransaction::class);
    }
}
