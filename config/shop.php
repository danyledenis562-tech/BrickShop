<?php

return [
    /*
    | Логотип у шапці та на сторінках входу:
    | - SHOP_LOGO_URL — повний URL (CDN, Render disk тощо), має пріоритет
    | - SHOP_LOGO_PATH — файл у public/, напр. images/brickshop-logo.png
    */
    'logo_url' => env('SHOP_LOGO_URL'),
    'logo_path' => env('SHOP_LOGO_PATH', 'images/brickshop-logo.png'),

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
