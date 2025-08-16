<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

class Customer extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address', 
        'line_id', 
        'purchase_type',
        'contact_channels',
        'contact_from_customer',
        'discounts',
        'default_discount_mou',     // เพิ่มกลับเข้าไป
        'default_discount_moti24',  // เพิ่มกลับเข้าไป
        'current_credit',
        'days_missing',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'purchase_type' => 'array', // เปลี่ยนเป็น array
        'discounts' => 'json',      // เปลี่ยนจาก array เป็น json
        'current_credit' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function policyTickets(): HasMany
    {
        return $this->hasMany(PolicyTicket::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(CustomerFollowUp::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * ตรวจสอบว่าลูกค้าสนใจประเภทประกันนี้หรือไม่
     */
    public function interestedInInsuranceType(string $type): bool
    {
        return is_array($this->purchase_type) && in_array($type, $this->purchase_type);
    }

    /**
     * รับประเภทประกันที่สนใจทั้งหมด
     */
    public function getInterestedInsuranceTypes(): array
    {
        return is_array($this->purchase_type) ? $this->purchase_type : [];
    }

    /**
     * รับประเภทประกันหลักแรก (สำหรับ backward compatibility)
     */
    public function getPrimaryInsuranceType(): ?string
    {
        $types = $this->getInterestedInsuranceTypes();
        return isset($types[0]) ? $types[0] : null;
    }

    public function creditTransactions(): HasMany
    {
        return $this->hasMany(CreditTransaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper Methods
    public function getDiscountFor($insuranceType, $duration): float
    {
        if (!$this->discounts) {
            // ไม่มีส่วนลดพิเศษ ใช้ส่วนลดเริ่มต้น
            return $this->getDefaultDiscount($insuranceType);
        }
        
        // ตรวจสอบรูปแบบข้อมูล
        $discounts = $this->discounts;
        if (is_string($discounts)) {
            $discounts = json_decode($discounts, true);
        }
        
        if (!is_array($discounts) || empty($discounts)) {
            return $this->getDefaultDiscount($insuranceType);
        }
        
        // รูปแบบใหม่: array ของ objects [{"insurance_type":"MOU",...}]
        if (isset($discounts[0]) && is_array($discounts[0]) && isset($discounts[0]['insurance_type'])) {
            // วนหาส่วนลดที่ตรงกับ insurance_type และ duration (reverse loop)
            for ($i = count($discounts) - 1; $i >= 0; $i--) {
                $discount = $discounts[$i];
                
                if (is_array($discount) &&
                    isset($discount['insurance_type']) && 
                    isset($discount['duration']) && 
                    isset($discount['discount_amount']) &&
                    $discount['insurance_type'] === $insuranceType && 
                    $discount['duration'] === $duration) {
                    return (float) $discount['discount_amount'];
                }
            }
        }
        // รูปแบบเก่า: nested object {"MOU":{"3_months":50,...}}
        else if (isset($discounts[$insuranceType]) && 
                 is_array($discounts[$insuranceType]) && 
                 isset($discounts[$insuranceType][$duration])) {
            return (float) $discounts[$insuranceType][$duration];
        }
        
        // ไม่พบส่วนลดพิเศษ ใช้ส่วนลดเริ่มต้น
        return $this->getDefaultDiscount($insuranceType);
    }
    
    private function getDefaultDiscount($insuranceType): float
    {
        if ($insuranceType === 'MOU') {
            return (float) ($this->default_discount_mou ?? 0);
        } elseif ($insuranceType === 'มติ24') {
            return (float) ($this->default_discount_moti24 ?? 0);
        }
        
        return 0;
    }

    public function calculatePrice($insuranceType, $duration, $personCount): array
    {
        $product = \App\Models\Product::where('type', $insuranceType)
                                     ->where('duration', $duration)
                                     ->first();
        
        if (!$product) {
            return ['error' => 'ไม่พบผลิตภัณฑ์'];
        }

        $basePrice = $product->base_price;
        $discount = $this->getDiscountFor($insuranceType, $duration);
        $pricePerPerson = $basePrice - $discount;
        $totalAmount = $pricePerPerson * $personCount;

        return [
            'base_price_per_person' => $basePrice,
            'discount_per_person' => $discount,
            'price_per_person' => $pricePerPerson,
            'person_count' => $personCount,
            'total_amount' => $totalAmount,
            'product' => $product
        ];
    }

    // Accessors
    public function getContactChannelsListAttribute(): string
    {
        if (!$this->contact_channels) return '-';
        return implode(', ', $this->contact_channels);
    }

    // Activity Log Configuration
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(function(string $eventName) {
                return match($eventName) {
                    'created' => "สร้างลูกค้าใหม่: {$this->name}",
                    'updated' => "แก้ไขข้อมูลลูกค้า: {$this->name}",
                    'deleted' => "ลบลูกค้า: {$this->name}",
                    default => $eventName
                };
            })
            ->useLogName('customer');
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
        
        // บันทึกเวลาในรูปแบบ readable
        $activity->properties = $activity->properties->put('performed_at', now()->format('d/m/Y H:i:s'));
    }
}
