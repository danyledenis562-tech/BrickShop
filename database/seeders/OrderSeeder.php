<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
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

        $products = Product::take(3)->get();

        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'paid',
            'total' => 0,
            'full_name' => $user->name,
            'phone' => $user->phone ?? '+380000000000',
            'city' => $user->city ?? 'Kyiv',
            'address' => $user->address ?? 'Main street 1',
            'delivery_type' => 'nova',
            'payment_type' => 'card',
        ]);

        $total = 0;
        foreach ($products as $product) {
            $quantity = 1;
            $lineTotal = $product->price * $quantity;
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
                'total' => $lineTotal,
            ]);
            $total += $lineTotal;
        }

        $order->update(['total' => $total]);
    }
}
