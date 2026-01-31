<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $featured = Cache::remember('home.featured', now()->addMinutes(10), fn () => Product::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->with('mainImage')
            ->withAvg(['reviews' => fn ($q) => $q->where('approved', true)], 'rating')
            ->withCount(['reviews' => fn ($q) => $q->where('approved', true)])
            ->orderByDesc('popularity')
            ->take(8)
            ->get());

        $newArrivals = Cache::remember('home.new_arrivals', now()->addMinutes(10), fn () => Product::query()
            ->where('is_active', true)
            ->with('mainImage')
            ->withAvg(['reviews' => fn ($q) => $q->where('approved', true)], 'rating')
            ->withCount(['reviews' => fn ($q) => $q->where('approved', true)])
            ->latest()
            ->take(8)
            ->get());

        $hits = Cache::remember('home.hits', now()->addMinutes(10), fn () => Product::query()
            ->where('is_active', true)
            ->with('mainImage')
            ->withAvg(['reviews' => fn ($q) => $q->where('approved', true)], 'rating')
            ->withCount(['reviews' => fn ($q) => $q->where('approved', true)])
            ->orderByDesc('popularity')
            ->take(8)
            ->get());

        $heroBanner = null;
        $promoBanners = collect();
        if (Schema::hasTable('banners')) {
            $heroBanner = Cache::remember('home.hero_banner', now()->addMinutes(5), fn () => Banner::query()
                ->where('is_active', true)
                ->where('position', 'home_hero')
                ->latest()
                ->first());

            $promoBanners = Cache::remember('home.promo_banners', now()->addMinutes(5), fn () => Banner::query()
                ->where('is_active', true)
                ->where('position', 'home_middle')
                ->latest()
                ->take(3)
                ->get());
        }

        $categories = Cache::remember('home.categories', now()->addMinutes(15), fn () => Category::query()
            ->withCount('products')
            ->orderBy('sort_order')
            ->take(6)
            ->get());

        $recentlyViewed = collect();
        if ($request->user() && Schema::hasTable('recently_viewed')) {
            $recentlyViewed = $request->user()
                ->recentlyViewedProducts()
                ->with('mainImage')
                ->withAvg(['reviews' => fn ($q) => $q->where('approved', true)], 'rating')
                ->withCount(['reviews' => fn ($q) => $q->where('approved', true)])
                ->orderByDesc('recently_viewed.viewed_at')
                ->take(8)
                ->get();
        } else {
            $recentIds = collect($request->session()->get('recently_viewed', []))
                ->filter()
                ->values();
            if ($recentIds->isNotEmpty()) {
                $recentlyViewed = Product::query()
                    ->whereIn('id', $recentIds)
                    ->with('mainImage')
                    ->withAvg(['reviews' => fn ($q) => $q->where('approved', true)], 'rating')
                    ->withCount(['reviews' => fn ($q) => $q->where('approved', true)])
                    ->get()
                    ->sortBy(fn ($product) => $recentIds->search($product->id))
                    ->values();
            }
        }

        return view('welcome', compact('featured', 'newArrivals', 'hits', 'categories', 'heroBanner', 'promoBanners', 'recentlyViewed'));
    }
}
