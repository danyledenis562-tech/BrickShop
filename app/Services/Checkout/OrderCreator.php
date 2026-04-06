<?php

namespace App\Services\Checkout;

use App\Exceptions\OutOfStockException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class OrderCreator
{
    public function __construct(
        private readonly DeliveryAddressNormalizer $addressNormalizer,
        private readonly CheckoutPricingService $pricing,
    ) {}

    public function createFromCheckout(?User $user, array $validated, array $cart): Order
    {
        if ($user === null) {
            $validated['bonus_to_spend'] = 0;
        }

        $quote = $this->pricing->quoteFromValidatedData($validated, $cart, $user);
        $normalizedAddress = $this->addressNormalizer->normalize($validated);

        return DB::transaction(function () use ($user, $validated, $cart, $quote, $normalizedAddress) {
            foreach ($cart as $item) {
                $productId = (int) ($item['product_id'] ?? 0);
                $qty = (int) ($item['quantity'] ?? 0);
                if ($productId <= 0 || $qty <= 0) {
                    continue;
                }

                $updated = Product::query()
                    ->whereKey($productId)
                    ->where('is_active', true)
                    ->where('stock', '>=', $qty)
                    ->decrement('stock', $qty);

                if ($updated < 1) {
                    throw new OutOfStockException($productId, $qty);
                }
            }

            $order = Order::create([
                'user_id' => $user?->id,
                'guest_email' => $user ? null : (string) ($validated['guest_email'] ?? ''),
                'promo_code_id' => $quote->appliedPromo?->id,
                'status' => 'new',
                'total' => $quote->total,
                'discount_amount' => $quote->discount,
                'shipping_amount' => $quote->shippingAmount,
                'bonus_spent' => $quote->bonusToSpend,
                'bonus_earned' => 0,
                'full_name' => $validated['full_name'],
                'phone' => $validated['phone'],
                'city' => $normalizedAddress['city'],
                'address' => $normalizedAddress['address'],
                'delivery_type' => $quote->deliveryType,
                'payment_type' => $validated['payment_type'],
                'note' => $validated['note'] ?? null,
            ]);

            foreach ($cart as $item) {
                $productId = (int) ($item['product_id'] ?? 0);
                if ($productId <= 0) {
                    continue;
                }

                $product = Product::find($productId);
                if (! $product || ! $product->is_active) {
                    continue;
                }

                $qty = (int) ($item['quantity'] ?? 0);
                if ($qty <= 0) {
                    continue;
                }

                $linePrice = (float) ($product->price ?? 0);
                $lineTotal = $linePrice * $qty;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $linePrice,
                    'total' => $lineTotal,
                ]);
            }

            if ($quote->appliedPromo) {
                $quote->appliedPromo->increment('times_used');
            }

            if ($user && $quote->bonusToSpend > 0) {
                $used = $user->spendBonus($quote->bonusToSpend, __('messages.bonus_spent_on_order', ['id' => $order->id]), $order);
                $order->bonus_spent = $used;
            }

            $earnRate = (int) config('shop.bonus_earn_rate', 10);
            if ($user && $earnRate > 0 && $order->total > 0) {
                $earned = (int) floor((float) $order->total / $earnRate);
                if ($earned > 0) {
                    $user->addBonus($earned, __('messages.bonus_earned_from_order', ['id' => $order->id]), $order);
                    $order->bonus_earned = $earned;
                }
            }

            $order->save();

            return $order;
        });
    }
}
