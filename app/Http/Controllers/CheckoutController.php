<?php

namespace App\Http\Controllers;

use App\Exceptions\OutOfStockException;
use App\Http\Requests\CheckoutRequest;
use App\Mail\OrderPlacedMail;
use App\Models\Order;
use App\Services\Cart\CartService;
use App\Services\Checkout\CheckoutPricingService;
use App\Services\Checkout\OrderCreator;
use App\Services\Payments\LiqPayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    private function canSendMailNow(): bool
    {
        $default = (string) config('mail.default', 'smtp');
        if ($default !== 'smtp') {
            return true;
        }

        return filled(config('mail.mailers.smtp.username'))
            && filled(config('mail.mailers.smtp.password'))
            && filled(config('mail.mailers.smtp.host'));
    }

    public function index(Request $request, CheckoutPricingService $pricing, CartService $cartService): View|RedirectResponse
    {
        $lines = $cartService->getLines($request);

        if (empty($lines)) {
            return redirect()->route('cart.index')->with('toast', __('messages.cart_empty'));
        }

        $quote = $pricing->quoteFromLines($request, $lines);

        $subtotal = $quote->subtotal;
        $discount = $quote->discount;
        $shippingAmount = $quote->shippingAmount;
        $deliveryType = $quote->deliveryType;
        $total = $quote->total;
        $appliedPromo = $quote->appliedPromo;
        $bonusBalance = $quote->bonusBalance;
        $bonusToSpend = $quote->bonusToSpend;
        $maxBonusUsable = $quote->maxBonusUsable;
        $previewBonusEarn = $quote->previewBonusEarn;
        $earnRate = $quote->earnRate;

        $cart = $lines;

        return view('checkout.index', compact(
            'cart',
            'subtotal',
            'discount',
            'shippingAmount',
            'deliveryType',
            'total',
            'appliedPromo',
            'bonusBalance',
            'bonusToSpend',
            'maxBonusUsable',
            'previewBonusEarn',
            'earnRate'
        ));
    }

    public function store(CheckoutRequest $request, OrderCreator $creator, CartService $cartService, LiqPayService $liqPay): RedirectResponse|View
    {
        $lines = $cartService->getLines($request);

        if (empty($lines)) {
            return redirect()->route('cart.index')->with('toast', __('messages.cart_empty'));
        }

        $data = $request->validated();
        try {
            $order = $creator->createFromCheckout($request->user(), $data, $lines);
        } catch (OutOfStockException) {
            return back()
                ->withInput()
                ->with('toast', __('messages.product_out_of_stock'));
        }

        $cartService->clear($request);

        if ($this->canSendMailNow()) {
            $orderId = $order->id;
            dispatch(function () use ($orderId): void {
                $order = Order::query()->with('items.product')->find($orderId);
                if (! $order) {
                    return;
                }
                $email = $order->guest_email ?? $order->user?->email;
                if (! $email) {
                    return;
                }
                try {
                    Mail::to($email)->send(new OrderPlacedMail($order));
                } catch (Throwable $e) {
                    Log::warning('Order confirmation email failed', [
                        'order_id' => $order->id,
                        'email' => $email,
                        'message' => $e->getMessage(),
                    ]);
                }
            })->afterResponse();
        }

        if (($data['payment_type'] ?? '') === 'liqpay' && $liqPay->isConfigured()) {
            return view('checkout.liqpay-redirect', [
                'checkout' => $liqPay->checkoutFormPayload($order),
            ]);
        }

        if ($request->user()) {
            return redirect()->route('checkout.thanks', $order);
        }

        return redirect()->signedRoute('checkout.thanks', ['order' => $order]);
    }

    public function thanks(Request $request, Order $order): View
    {
        $allowed = false;
        if ($request->hasValidSignature()) {
            $allowed = true;
        } elseif (auth()->check() && $order->user_id && (int) $order->user_id === (int) auth()->id()) {
            $allowed = true;
        }

        abort_unless($allowed, 403);

        $order->load('items.product');

        return view('checkout.thanks', compact('order'));
    }
}
