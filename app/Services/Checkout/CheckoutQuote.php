<?php

namespace App\Services\Checkout;

use App\Models\PromoCode;

final class CheckoutQuote
{
    public function __construct(
        public readonly array $cart,
        public readonly float $subtotal,
        public readonly float $discount,
        public readonly float $shippingAmount,
        public readonly string $deliveryType,
        public readonly float $total,
        public readonly ?PromoCode $appliedPromo,
        public readonly int $bonusBalance,
        public readonly int $bonusToSpend,
        public readonly int $maxBonusUsable,
        public readonly int $previewBonusEarn,
        public readonly int $earnRate,
    ) {}
}
