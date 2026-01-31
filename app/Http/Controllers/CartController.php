<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cart = $this->getCart($request);
        $total = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);

        return view('cart.index', compact('cart', 'total'));
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        $cart = $this->getCart($request);
        $key = (string) $product->id;

        if (! isset($cart[$key])) {
            $cart[$key] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $product->price,
                'quantity' => 1,
                'image' => $product->mainImage?->path,
            ];
        } else {
            $cart[$key]['quantity'] += 1;
        }

        $request->session()->put('cart', $cart);

        if ($request->string('redirect')->toString() === 'checkout') {
            return redirect()->route('checkout.index');
        }

        return back()->with('toast', __('messages.cart_added'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cart = $this->getCart($request);
        $key = (string) $product->id;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = $data['quantity'];
            $request->session()->put('cart', $cart);
        }

        return back()->with('toast', __('messages.cart_updated'));
    }

    public function remove(Request $request, Product $product): RedirectResponse
    {
        $cart = $this->getCart($request);
        $key = (string) $product->id;

        if (isset($cart[$key])) {
            unset($cart[$key]);
            $request->session()->put('cart', $cart);
        }

        return back()->with('toast', __('messages.cart_removed'));
    }

    private function getCart(Request $request): array
    {
        return $request->session()->get('cart', []);
    }
}
