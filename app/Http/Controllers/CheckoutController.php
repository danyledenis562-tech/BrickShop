<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\PromoCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('toast', __('messages.cart_empty'));
        }

        $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
        $discount = 0.0;
        $appliedPromo = null;

        if ($promoInput = $request->string('promo_code')->trim()->toString()) {
            $promo = PromoCode::where('code', $promoInput)->first();
            if ($promo && $promo->isValid()) {
                $appliedPromo = $promo;
                $discount = $promo->applyDiscount($subtotal);
            }
        }

        $total = round($subtotal - $discount, 2);

        return view('checkout.index', compact('cart', 'subtotal', 'discount', 'total', 'appliedPromo'));
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('toast', __('messages.cart_empty'));
        }

        $data = $request->validated();

        $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
        $discount = 0.0;
        $promoCodeId = null;

        if (! empty($data['promo_code'])) {
            $promo = PromoCode::where('code', $data['promo_code'])->first();
            if ($promo && $promo->isValid()) {
                $discount = $promo->applyDiscount($subtotal);
                $promoCodeId = $promo->id;
            }
        }

        $total = round($subtotal - $discount, 2);

        $order = DB::transaction(function () use ($request, $data, $cart, $total, $discount, $promoCodeId) {
            $order = Order::create([
                'user_id' => $request->user()->id,
                'promo_code_id' => $promoCodeId,
                'status' => 'new',
                'total' => $total,
                'discount_amount' => $discount,
                'full_name' => $data['full_name'],
                'phone' => $data['phone'],
                'city' => $data['city'],
                'address' => $data['address'],
                'delivery_type' => $data['delivery_type'],
                'payment_type' => $data['payment_type'],
                'note' => $data['note'] ?? null,
            ]);

            foreach ($cart as $item) {
                $product = Product::find($item['product_id']);
                if (! $product) {
                    continue;
                }

                $lineTotal = $item['price'] * $item['quantity'];
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $lineTotal,
                ]);
            }

            if ($promoCodeId) {
                PromoCode::where('id', $promoCodeId)->increment('times_used');
            }

            return $order;
        });

        $request->session()->forget('cart');

        return redirect()->route('checkout.thanks', $order);
    }

    public function thanks(Order $order): View
    {
        abort_unless($order->user_id === auth()->id(), 403);
        $order->load('items.product');

        return view('checkout.thanks', compact('order'));
    }
}
