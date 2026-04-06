<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guest_email',
        'promo_code_id',
        'status',
        'total',
        'discount_amount',
        'shipping_amount',
        'bonus_spent',
        'bonus_earned',
        'full_name',
        'phone',
        'city',
        'address',
        'delivery_type',
        'payment_type',
        'note',
        'dont_call',
        'tracking_number',
        'tracking_url',
        'canceled_at',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'dont_call' => 'boolean',
        'canceled_at' => 'datetime',
    ];

    public function bonusTransactions()
    {
        return $this->hasMany(BonusTransaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class, 'promo_code_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function deliveryTypeLabel(): string
    {
        return match ($this->delivery_type) {
            'nova' => __('messages.delivery_nova'),
            'courier' => __('messages.delivery_courier_nova'),
            'ukrposhta' => __('messages.delivery_ukrposhta'),
            default => (string) $this->delivery_type,
        };
    }

    public function customerEmail(): ?string
    {
        return $this->guest_email ?: $this->user?->email;
    }
}
