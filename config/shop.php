<?php

return [
    'test_payment_webhook_secret' => env('SHOP_TEST_PAYMENT_WEBHOOK_SECRET', ''),
    'low_stock_threshold' => (int) env('SHOP_LOW_STOCK_THRESHOLD', 5),
    'liqpay' => [
        'public_key' => env('LIQPAY_PUBLIC_KEY', ''),
        'private_key' => env('LIQPAY_PRIVATE_KEY', ''),
        'sandbox' => filter_var(env('LIQPAY_SANDBOX', 'true'), FILTER_VALIDATE_BOOLEAN),
    ],
    'bonus_earn_rate' => env('SHOP_BONUS_EARN_RATE', 10),
    'delivery_prices' => [
        'nova' => 100,
        'courier' => 250,
        'ukrposhta' => 50,
    ],
];
