<?php

namespace App\Services\Checkout;

use App\Models\PromoCode;
use App\Models\User;
use Illuminate\Http\Request;

final class CheckoutPricingService
{
    public function deliveryPrices(): array
    {
        return (array) config('shop.delivery_prices', []);
    }

    public function deliveryPriceFor(string $type): float
    {
        $prices = $this->deliveryPrices();

        return (float) ($prices[$type] ?? 0);
    }

    public function resolveDeliveryType(?string $value): string
    {
        $value = (string) $value;

        return in_array($value, ['nova', 'courier', 'ukrposhta'], true) ? $value : 'nova';
    }

    /**
     * @param  array<int, array{price:float, quantity:int}>  $lines
     */
    public function quoteFromLines(Request $request, array $lines): CheckoutQuote
    {
        $subtotal = (float) collect($lines)->sum(fn ($item) => ((float) ($item['price'] ?? 0)) * ((int) ($item['quantity'] ?? 0)));

        $deliveryType = $this->resolveDeliveryType($request->string('delivery_type')->toString());
        $shippingAmount = $this->deliveryPriceFor($deliveryType);

        $discount = 0.0;
        $appliedPromo = null;
        $promoInput = $request->string('promo_code')->trim()->toString();
        if ($promoInput !== '') {
            $promo = PromoCode::where('code', $promoInput)->first();
            if ($promo && $promo->isValid()) {
                $appliedPromo = $promo;
                $discount = $promo->applyDiscount($subtotal);
            }
        }

        $user = $request->user();
        $bonusBalance = (int) (($user?->bonus_balance) ?? 0);
        $bonusToSpend = (int) $request->integer('bonus_to_spend');

        $totalBeforeBonus = round($subtotal - $discount + $shippingAmount, 2);
        $maxBonusUsable = (int) floor($totalBeforeBonus);
        $bonusToSpend = max(0, min($bonusToSpend, $bonusBalance, $maxBonusUsable));
        $total = max(0, $totalBeforeBonus - $bonusToSpend);

        $earnRate = (int) config('shop.bonus_earn_rate', 10);
        $previewBonusEarn = $earnRate > 0 ? (int) floor($total / $earnRate) : 0;

        return new CheckoutQuote(
            cart: $lines,
            subtotal: $subtotal,
            discount: $discount,
            shippingAmount: $shippingAmount,
            deliveryType: $deliveryType,
            total: (float) $total,
            appliedPromo: $appliedPromo,
            bonusBalance: $bonusBalance,
            bonusToSpend: $bonusToSpend,
            maxBonusUsable: $maxBonusUsable,
            previewBonusEarn: $previewBonusEarn,
            earnRate: $earnRate,
        );
    }

    public function quoteFromValidatedData(array $validated, array $cart, ?User $user): CheckoutQuote
    {
        $subtotal = (float) collect($cart)->sum(fn ($item) => ((float) ($item['price'] ?? 0)) * ((int) ($item['quantity'] ?? 0)));

        $deliveryType = $this->resolveDeliveryType((string) ($validated['delivery_type'] ?? 'nova'));
        $shippingAmount = $this->deliveryPriceFor($deliveryType);

        $discount = 0.0;
        $appliedPromo = null;
        $promoInput = trim((string) ($validated['promo_code'] ?? ''));
        if ($promoInput !== '') {
            $promo = PromoCode::where('code', $promoInput)->first();
            if ($promo && $promo->isValid()) {
                $appliedPromo = $promo;
                $discount = $promo->applyDiscount($subtotal);
            }
        }

        $bonusBalance = (int) (($user?->bonus_balance) ?? 0);
        $bonusToSpend = $user ? (int) ($validated['bonus_to_spend'] ?? 0) : 0;

        $totalBeforeBonus = round($subtotal - $discount + $shippingAmount, 2);
        $maxBonusUsable = (int) floor($totalBeforeBonus);
        $bonusToSpend = max(0, min($bonusToSpend, $bonusBalance, $maxBonusUsable));
        $total = max(0, $totalBeforeBonus - $bonusToSpend);

        $earnRate = (int) config('shop.bonus_earn_rate', 10);
        $previewBonusEarn = $earnRate > 0 ? (int) floor($total / $earnRate) : 0;

        return new CheckoutQuote(
            cart: $cart,
            subtotal: $subtotal,
            discount: $discount,
            shippingAmount: $shippingAmount,
            deliveryType: $deliveryType,
            total: (float) $total,
            appliedPromo: $appliedPromo,
            bonusBalance: $bonusBalance,
            bonusToSpend: $bonusToSpend,
            maxBonusUsable: $maxBonusUsable,
            previewBonusEarn: $previewBonusEarn,
            earnRate: $earnRate,
        );
    }
}
