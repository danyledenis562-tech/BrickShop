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
            $locale = app()->getLocale();

            $heroBanner = Cache::remember(
                "home.hero_banner.{$locale}",
                now()->addMinutes(5),
                function () use ($locale) {
                    return Banner::query()
                        ->where('is_active', true)
                        ->where('position', 'home_hero')
                        ->where(function ($q) use ($locale) {
                            $q->whereNull('locale')->orWhere('locale', $locale);
                        })
                        ->where(function ($q) {
                            $now = now();
                            $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                        })
                        ->where(function ($q) {
                            $now = now();
                            $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
                        })
                        ->orderBy('sort_order')
                        ->latest()
                        ->first();
                }
            );

            $promoBanners = Cache::remember(
                "home.promo_banners.{$locale}",
                now()->addMinutes(5),
                function () use ($locale) {
                    return Banner::query()
                        ->where('is_active', true)
                        ->where('position', 'home_middle')
                        ->where(function ($q) use ($locale) {
                            $q->whereNull('locale')->orWhere('locale', $locale);
                        })
                        ->where(function ($q) {
                            $now = now();
                            $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                        })
                        ->where(function ($q) {
                            $now = now();
                            $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
                        })
                        ->orderBy('sort_order')
                        ->latest()
                        ->take(3)
                        ->get();
                }
            );
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
