<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
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

        $this->applyProductSearch($query, $request->string('search')->toString());

        $categories = Cache::remember('catalog.categories', now()->addMinutes(15), fn () => Category::query()->orderBy('sort_order')->get());

        $currentCategory = null;
        if ($category = $request->string('category')->toString()) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $category));
            $currentCategory = $categories->firstWhere('slug', $category);
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

        return view('catalog.index', compact('products', 'categories', 'currentCategory'));
    }

    public function suggestions(Request $request): JsonResponse
    {
        $term = trim($request->string('q')->toString());
        if ($term === '') {
            return response()->json([]);
        }

        $items = Product::query()
            ->where('is_active', true)
            ->tap(fn (Builder $q) => $this->applyProductSearch($q, $term))
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

    /**
     * Case-insensitive partial match on name, series, brand, set number, description (all words must match somewhere).
     */
    private function applyProductSearch(Builder $query, string $search): void
    {
        $search = trim($search);
        if ($search === '') {
            return;
        }

        $terms = preg_split('/\s+/u', mb_strtolower($search, 'UTF-8'), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        foreach ($terms as $rawTerm) {
            $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $rawTerm);
            $pattern = '%'.$escaped.'%';
            $query->where(function (Builder $q) use ($pattern) {
                $q->whereRaw('LOWER(name) LIKE ?', [$pattern])
                    ->orWhereRaw('LOWER(COALESCE(series, \'\')) LIKE ?', [$pattern])
                    ->orWhereRaw('LOWER(COALESCE(brand, \'\')) LIKE ?', [$pattern])
                    ->orWhereRaw('LOWER(COALESCE(set_number, \'\')) LIKE ?', [$pattern])
                    ->orWhereRaw('LOWER(COALESCE(description, \'\')) LIKE ?', [$pattern]);
            });
        }
    }
}
