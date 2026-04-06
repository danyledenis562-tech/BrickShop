<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'city',
        'address',
        'avatar',
        'bonus_balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(Product::class, 'favorites')->withTimestamps();
    }

    public function recentlyViewedProducts()
    {
        return $this->belongsToMany(Product::class, 'recently_viewed')
            ->withPivot('viewed_at')
            ->withTimestamps();
    }

    public function bonusTransactions()
    {
        return $this->hasMany(BonusTransaction::class);
    }

    public function addBonus(int $amount, ?string $description = null, ?Order $order = null): void
    {
        if ($amount <= 0) {
            return;
        }

        $this->increment('bonus_balance', $amount);

        $this->bonusTransactions()->create([
            'order_id' => $order?->id,
            'type' => 'earn',
            'amount' => $amount,
            'description' => $description,
        ]);
    }

    public function spendBonus(int $amount, ?string $description = null, ?Order $order = null): int
    {
        $amount = max(0, $amount);
        $amount = min($amount, $this->bonus_balance);

        if ($amount <= 0) {
            return 0;
        }

        $this->decrement('bonus_balance', $amount);

        $this->bonusTransactions()->create([
            'order_id' => $order?->id,
            'type' => 'spend',
            'amount' => -$amount,
            'description' => $description,
        ]);

        return $amount;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
