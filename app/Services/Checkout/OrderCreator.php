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
            $requestedItems = collect($cart)
                ->map(fn (array $item): array => [
                    'product_id' => (int) ($item['product_id'] ?? 0),
                    'quantity' => (int) ($item['quantity'] ?? 0),
                ])
                ->filter(fn (array $item): bool => $item['product_id'] > 0 && $item['quantity'] > 0)
                ->values();

            if ($requestedItems->isEmpty()) {
                throw new OutOfStockException(0, 0);
            }

            $products = Product::query()
                ->whereIn('id', $requestedItems->pluck('product_id')->all())
                ->where('is_active', true)
                ->get()
                ->keyBy('id');

            foreach ($cart as $item) {
                $productId = (int) ($item['product_id'] ?? 0);
                $qty = (int) ($item['quantity'] ?? 0);
                if ($productId <= 0 || $qty <= 0) {
                    continue;
                }

                if (! $products->has($productId)) {
                    throw new OutOfStockException($productId, $qty);
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
                'dont_call' => (bool) ($validated['dont_call'] ?? false),
            ]);

            $now = now();
            $orderItemsPayload = [];
            foreach ($requestedItems as $item) {
                $product = $products->get($item['product_id']);
                if (! $product) {
                    continue;
                }
                $linePrice = (float) ($product->price ?? 0);
                $lineTotal = $linePrice * $item['quantity'];
                $orderItemsPayload[] = [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $linePrice,
                    'total' => $lineTotal,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            if ($orderItemsPayload !== []) {
                OrderItem::query()->insert($orderItemsPayload);
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
