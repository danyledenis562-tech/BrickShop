<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'user@brickshop.test')->first();
        if (! $user) {
            return;
        }

        $products = Product::take(2)->pluck('id')->toArray();
        $user->favorites()->sync($products);
    }
}
