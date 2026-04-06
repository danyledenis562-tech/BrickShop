<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    /** @use HasFactory<\Database\Factories\ProductImageFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'path',
        'is_main',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (ProductImage $image) {
            if (! $image->is_main) {
                return;
            }

            $query = static::query()->where('product_id', $image->product_id);

            if ($image->exists) {
                $query->whereKeyNot($image->getKey());
            }

            $query->update(['is_main' => false]);
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
