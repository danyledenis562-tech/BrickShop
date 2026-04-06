<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'valid_from',
        'valid_until',
        'usage_limit',
        'times_used',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }
        if ($this->valid_from && now()->lt($this->valid_from)) {
            return false;
        }
        if ($this->valid_until && now()->gt($this->valid_until)) {
            return false;
        }
        if ($this->usage_limit !== null && $this->times_used >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function applyDiscount(float $subtotal): float
    {
        if ($this->type === 'percent') {
            $discount = round($subtotal * ((float) $this->value / 100), 2);
        } else {
            $discount = min((float) $this->value, $subtotal);
        }

        return round($discount, 2);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'promo_code_id');
    }
}
