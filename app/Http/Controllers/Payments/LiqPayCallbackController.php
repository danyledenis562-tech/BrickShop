<?php

namespace App\Http\Controllers\Payments;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payments\LiqPayService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LiqPayCallbackController extends Controller
{
    public function callback(Request $request, LiqPayService $liqPay): Response
    {
        $encoded = (string) $request->input('data', '');
        $signature = (string) $request->input('signature', '');

        if ($encoded === '' || $signature === '' || ! $liqPay->verifyCallbackSignature($encoded, $signature)) {
            return response('invalid signature', 400);
        }

        $payload = $liqPay->decodeData($encoded);
        if ($payload === null) {
            return response('invalid data', 400);
        }

        $orderIdRaw = (string) ($payload['order_id'] ?? '');
        if (! str_starts_with($orderIdRaw, 'brickshop_')) {
            return response('ok', 200);
        }

        $id = (int) substr($orderIdRaw, strlen('brickshop_'));
        if ($id <= 0) {
            return response('ok', 200);
        }

        $order = Order::query()->find($id);
        if (! $order) {
            return response('ok', 200);
        }

        $status = (string) ($payload['status'] ?? '');
        if (in_array($status, ['success', 'sandbox'], true) && $order->status === OrderStatus::New) {
            $order->update(['status' => OrderStatus::Paid]);
        }

        return response('ok', 200);
    }
}
