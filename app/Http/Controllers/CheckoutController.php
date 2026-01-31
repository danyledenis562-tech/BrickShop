<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
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

        $total = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);

        return view('checkout.index', compact('cart', 'total'));
    }

    public function store(Request $request): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('toast', __('messages.cart_empty'));
        }

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'city' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:255'],
            'delivery_type' => ['required', 'string', 'max:50'],
            'payment_type' => ['required', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $total = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);

        $order = DB::transaction(function () use ($request, $data, $cart, $total) {
            $order = Order::create([
                'user_id' => $request->user()->id,
                'status' => 'new',
                'total' => $total,
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
