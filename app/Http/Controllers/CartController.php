<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Models\Product;
use App\Services\Cart\CartReminderService;
use App\Services\Cart\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request, CartService $cart, CartReminderService $cartReminders): View
    {
        $lines = $cart->getLines($request);
        $total = $cart->total($lines);
        $cartReminders->syncFromSessionCart($request, $cart);

        return view('cart.index', ['cart' => $lines, 'total' => $total]);
    }

    public function add(Request $request, Product $product, CartService $cart): RedirectResponse
    {
        if ($product->stock <= 0) {
            return back()->with('toast', __('messages.cannot_add_out_of_stock'));
        }

        $cart->add($request, $product, 1);

        if ($request->string('redirect')->toString() === 'checkout') {
            return redirect()->route('checkout.index');
        }

        return back()->with('toast', __('messages.cart_added'));
    }

    public function update(CartRequest $request, Product $product, CartService $cart): RedirectResponse
    {
        $validated = $request->validated();
        $cart->updateQuantity($request, $product, (int) $validated['quantity']);

        return back()->with('toast', __('messages.cart_updated'));
    }

    public function remove(Request $request, Product $product, CartService $cart): RedirectResponse
    {
        $cart->remove($request, $product);

        return back()->with('toast', __('messages.cart_removed'));
    }
}
