<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'type',
        'duration',
        'duration_display',
        'base_price',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Static methods
    public static function getDurations(): array
    {
        return [
            '3_months' => '3 เดือน',
            '6_months' => '6 เดือน', 
            '12_months' => '1 ปี',
            '15_months' => '15 เดือน',
        ];
    }

    public static function getTypes(): array
    {
        return [
            'MOU' => 'MOU',
            'มติ24' => 'มติ24',
        ];
    }

    // Helper methods
    public function getDisplayNameAttribute(): string
    {
        return "{$this->type} - {$this->duration_display}";
    }
}
