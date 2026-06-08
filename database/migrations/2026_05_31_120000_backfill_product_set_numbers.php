<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $next = 60300;

        Product::query()
            ->orderBy('id')
            ->where(function ($query) {
                $query->whereNull('set_number')->orWhere('set_number', '');
            })
            ->each(function (Product $product) use (&$next) {
                $product->update(['set_number' => (string) $next]);
                $next++;
            });
    }

    public function down(): void
    {
        //
    }
};
