<?php

namespace App\Services\Payments;

use App\Models\Order;
use Illuminate\Support\Facades\URL;

final class LiqPayService
{
    public function isConfigured(): bool
    {
        $pub = (string) config('shop.liqpay.public_key', '');
        $priv = (string) config('shop.liqpay.private_key', '');

        return $pub !== '' && $priv !== '';
    }

    public function checkoutFormPayload(Order $order): array
    {
        $privateKey = (string) config('shop.liqpay.private_key');
        $publicKey = (string) config('shop.liqpay.public_key');
        $sandbox = (bool) config('shop.liqpay.sandbox', true);

        $user = $order->user;
        $resultUrl = $user !== null
            ? URL::route('checkout.thanks', $order, true)
            : URL::signedRoute('checkout.thanks', ['order' => $order], absolute: true);

        $serverUrl = URL::route('payments.liqpay.callback', [], true);

        $params = [
            'version' => 3,
            'public_key' => $publicKey,
            'action' => 'pay',
            'amount' => number_format((float) $order->total, 2, '.', ''),
            'currency' => 'UAH',
            'description' => __('messages.liqpay_order_description', ['id' => $order->id]),
            'order_id' => 'brickshop_'.$order->id,
            'result_url' => $resultUrl,
            'server_url' => $serverUrl,
            'sandbox' => $sandbox ? 1 : 0,
        ];

        $encoded = base64_encode(json_encode($params, JSON_THROW_ON_ERROR));
        $signature = base64_encode(sha1($privateKey.$encoded.$privateKey, true));

        return [
            'data' => $encoded,
            'signature' => $signature,
            'url' => 'https://www.liqpay.ua/api/3/checkout',
        ];
    }

    public function verifyCallbackSignature(string $data, string $signature): bool
    {
        $privateKey = (string) config('shop.liqpay.private_key');
        if ($privateKey === '') {
            return false;
        }

        $expected = base64_encode(sha1($privateKey.$data.$privateKey, true));

        return hash_equals($expected, $signature);
    }

    public function decodeData(string $data): ?array
    {
        $json = base64_decode($data, true);
        if ($json === false) {
            return null;
        }

        $decoded = json_decode($json, true);
        if (! is_array($decoded)) {
            return null;
        }

        return $decoded;
    }
}
