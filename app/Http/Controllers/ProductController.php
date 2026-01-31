<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Request $request, Product $product): View
    {
        if ($request->user() && Schema::hasTable('recently_viewed')) {
            $request->user()->recentlyViewedProducts()->syncWithoutDetaching([
                $product->id => ['viewed_at' => now()],
            ]);
            $request->user()->recentlyViewedProducts()->updateExistingPivot($product->id, [
                'viewed_at' => now(),
            ]);
        } else {
            $recent = collect($request->session()->get('recently_viewed', []))
                ->filter()
                ->reject(fn ($id) => (int) $id === $product->id);
            $recent->prepend($product->id);
            $request->session()->put('recently_viewed', $recent->take(8)->values()->all());
        }

        $product->load(['images', 'category', 'reviews' => fn ($q) => $q->where('approved', true)->latest(), 'reviews.user']);

        $related = Product::query()
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->with('mainImage')
            ->withAvg(['reviews' => fn ($q) => $q->where('approved', true)], 'rating')
            ->withCount(['reviews' => fn ($q) => $q->where('approved', true)])
            ->take(4)
            ->get();

        $ratingAverage = round($product->reviews->avg('rating') ?? 0, 1);

        return view('product.show', compact('product', 'related', 'ratingAverage'));
    }

    public function review(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        $product->reviews()->create([
            'user_id' => $request->user()->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'],
            'approved' => false,
        ]);

        return back()->with('toast', __('messages.review_pending'));
    }
}
