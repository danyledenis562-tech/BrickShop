<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(Request $request): View
    {
        $favorites = $request->user()
            ->favorites()
            ->with('mainImage')
            ->withAvg(['reviews' => fn ($q) => $q->where('approved', true)], 'rating')
            ->withCount(['reviews' => fn ($q) => $q->where('approved', true)])
            ->paginate(12);

        return view('favorites.index', compact('favorites'));
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $user = $request->user();
        if ($user) {
            $user->favorites()->syncWithoutDetaching([$product->id]);

            return back()->with('toast', __('messages.favorite_added'));
        }

        $favorites = collect($request->session()->get('favorites', []))
            ->filter()
            ->reject(fn ($id) => (int) $id === $product->id);
        $favorites->prepend($product->id);
        $request->session()->put('favorites', $favorites->take(20)->values()->all());

        return back()->with('toast', __('messages.favorite_added'));
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $user = $request->user();
        if ($user) {
            $user->favorites()->detach($product->id);

            return back()->with('toast', __('messages.favorite_removed'));
        }

        $favorites = collect($request->session()->get('favorites', []))
            ->filter(fn ($id) => (int) $id !== $product->id);
        $request->session()->put('favorites', $favorites->values()->all());

        return back()->with('toast', __('messages.favorite_removed'));
    }
}
