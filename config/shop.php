<?php

return [
    /*
    | Логотип у шапці та на сторінках входу:
    | - SHOP_LOGO_URL — повний URL (CDN, Render disk тощо), має пріоритет
    | - SHOP_LOGO_PATH — файл у public/, напр. images/brickshop-logo.png
    */
    'logo_url' => env('SHOP_LOGO_URL'),
    // SVG у репо — завжди є на Render після deploy; PNG/JPG можна задати через SHOP_LOGO_PATH
    'logo_path' => env('SHOP_LOGO_PATH', 'images/brickshop-logo.svg'),
    /** Короткий текст поруч із маркою / якщо немає картинки */
    'logo_text' => env('SHOP_LOGO_TEXT', 'Brick Shop'),

    /** true — при повному логотипі (PNG з текстом) приховати підпис поруч */
    'logo_hide_wordmark' => filter_var(env('SHOP_LOGO_HIDE_WORDMARK', false), FILTER_VALIDATE_BOOLEAN),

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
