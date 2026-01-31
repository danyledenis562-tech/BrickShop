<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::query()
            ->where('is_active', true)
            ->with('mainImage', 'category')
            ->withAvg(['reviews' => fn ($q) => $q->where('approved', true)], 'rating')
            ->withCount(['reviews' => fn ($q) => $q->where('approved', true)]);

        if ($search = $request->string('search')->toString()) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($category = $request->string('category')->toString()) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $category));
        }

        if ($age = $request->integer('age')) {
            $query->where('age', '>=', $age);
        }

        if ($difficulty = $request->string('difficulty')->toString()) {
            $query->where('difficulty', $difficulty);
        }

        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->input('max_price'));
        }

        $sort = $request->string('sort')->toString();
        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'popular' => $query->orderByDesc('popularity'),
            default => $query->latest(),
        };

        $products = $query->paginate(12);
        $categories = Cache::remember('catalog.categories', now()->addMinutes(15), fn () => Category::query()->orderBy('sort_order')->get());

        $bannerTop = null;
        if (Schema::hasTable('banners')) {
            $bannerTop = Banner::query()
                ->where('is_active', true)
                ->where('position', 'catalog_top')
                ->latest()
                ->first();
        }

        return view('catalog.index', compact('products', 'categories', 'bannerTop'));
    }

    public function suggestions(Request $request): JsonResponse
    {
        $term = trim($request->string('q')->toString());
        if ($term === '') {
            return response()->json([]);
        }

        $items = Product::query()
            ->where('is_active', true)
            ->where('name', 'like', "%{$term}%")
            ->with('mainImage')
            ->orderByDesc('popularity')
            ->limit(5)
            ->get()
            ->map(function (Product $product) {
                $path = $product->mainImage?->path;
                $image = null;
                if ($path) {
                    $image = Str::startsWith($path, ['http://', 'https://'])
                        ? $path
                        : asset('storage/'.$path);
                }

                return [
                    'name' => $product->name,
                    'price' => $product->price,
                    'url' => route('product.show', $product),
                    'image' => $image,
                    'series' => $product->series,
                ];
            });

        return response()->json($items);
    }
}
