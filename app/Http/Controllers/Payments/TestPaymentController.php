<?php

namespace App\Http\Controllers\Payments;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TestPaymentController extends Controller
{
    public function webhook(Request $request): JsonResponse|Response
    {
        $secret = (string) config('shop.test_payment_webhook_secret', '');
        if ($secret === '') {
            abort(503);
        }

        if (! hash_equals($secret, $request->header('X-Test-Payment-Secret', ''))) {
            abort(403);
        }

        $payload = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
        ]);

        $order = Order::query()->findOrFail($payload['order_id']);

        if ($order->status !== OrderStatus::New) {
            return response()->json([
                'ok' => false,
                'error' => 'order_not_pending',
            ], 422);
        }

        $order->update(['status' => OrderStatus::Paid]);

        return response()->json(['ok' => true]);
    }

    public function simulate(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id && (int) $order->user_id === (int) $request->user()->id, 403);

        if ($order->status !== OrderStatus::New) {
            return redirect()
                ->route('checkout.thanks', $order)
                ->with('toast', __('messages.order_not_pending_payment'));
        }

        $order->update(['status' => OrderStatus::Paid]);

        return redirect()
            ->route('checkout.thanks', $order)
            ->with('toast', __('messages.order_paid_test'));
    }
}
