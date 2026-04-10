<?php

namespace App\Services\Cart;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

final class CartService
{
    private const SESSION_KEY = 'cart';

    /**
     * Session shape (canonical):
     *  [
     *    "123" => ["product_id" => 123, "quantity" => 2],
     *  ]
     */
    public function getSessionCart(Request $request): array
    {
        $raw = (array) $request->session()->get(self::SESSION_KEY, []);

        // Backward-compat: previous shape included name/slug/price/image.
        $normalized = [];
        foreach ($raw as $key => $row) {
            if (! is_array($row)) {
                continue;
            }
            $productId = (int) ($row['product_id'] ?? $key);
            $qty = (int) ($row['quantity'] ?? 0);
            if ($productId <= 0 || $qty <= 0) {
                continue;
            }
            $normalized[(string) $productId] = [
                'product_id' => $productId,
                'quantity' => max(1, min($qty, 99)),
            ];
        }

        if ($normalized !== $raw) {
            $request->session()->put(self::SESSION_KEY, $normalized);
        }

        return $normalized;
    }

    public function add(Request $request, Product $product, int $qty = 1): void
    {
        $qty = max(1, min($qty, 99));
        $cart = $this->getSessionCart($request);
        $key = (string) $product->id;

        $current = (int) ($cart[$key]['quantity'] ?? 0);
        $cart[$key] = [
            'product_id' => (int) $product->id,
            'quantity' => max(1, min($current + $qty, 99)),
        ];

        $request->session()->put(self::SESSION_KEY, $cart);
        App::make(CartReminderService::class)->syncFromSessionCart($request, $this);
    }

    public function updateQuantity(Request $request, Product $product, int $qty): void
    {
        $qty = max(1, min($qty, 99));
        $cart = $this->getSessionCart($request);
        $key = (string) $product->id;

        if (! isset($cart[$key])) {
            return;
        }

        $cart[$key]['quantity'] = $qty;
        $request->session()->put(self::SESSION_KEY, $cart);
        App::make(CartReminderService::class)->syncFromSessionCart($request, $this);
    }

    public function remove(Request $request, Product $product): void
    {
        $cart = $this->getSessionCart($request);
        $key = (string) $product->id;

        if (isset($cart[$key])) {
            unset($cart[$key]);
            $request->session()->put(self::SESSION_KEY, $cart);
        }
        App::make(CartReminderService::class)->syncFromSessionCart($request, $this);
    }

    public function clear(Request $request): void
    {
        $request->session()->forget(self::SESSION_KEY);
        if ($uid = $request->user()?->id) {
            dispatch(function () use ($uid): void {
                App::make(CartReminderService::class)->clearForUserId($uid);
            })->afterResponse();
        }
    }

    /**
     * Returns cart lines for rendering/pricing. Price is always taken from DB (anti-tamper).
     *
     * @return array<int, array{product_id:int, name:string, slug:string, price:float, quantity:int, image:?string, product:Product}>
     */
    public function getLines(Request $request): array
    {
        $cart = $this->getSessionCart($request);
        if ($cart === []) {
            return [];
        }

        $ids = array_map(fn ($row) => (int) $row['product_id'], array_values($cart));

        /** @var Collection<int, Product> $products */
        $products = Product::query()
            ->whereIn('id', $ids)
            ->where('is_active', true)
            ->with('mainImage')
            ->get()
            ->keyBy('id');

        $lines = [];
        foreach ($cart as $row) {
            $product = $products->get((int) $row['product_id']);
            if (! $product) {
                continue;
            }

            $qty = (int) $row['quantity'];
            if ($qty <= 0) {
                continue;
            }

            // Soft stock cap: don't allow ordering above current stock (if stock tracked).
            if (is_numeric($product->stock)) {
                $qty = max(1, min($qty, (int) $product->stock ?: 1));
            }

            $lines[] = [
                'product_id' => (int) $product->id,
                'name' => (string) $product->name,
                'slug' => (string) $product->slug,
                'price' => (float) $product->price,
                'quantity' => $qty,
                'image' => $product->mainImage?->hasEmbeddedData()
                    ? route('media.product-image', ['image' => $product->mainImage->id])
                    : $product->mainImage?->path,
                'product' => $product,
            ];
        }

        return $lines;
    }

    public function total(array $lines): float
    {
        return (float) collect($lines)->sum(fn ($item) => ((float) $item['price']) * ((int) $item['quantity']));
    }
}
