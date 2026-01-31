<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile overview.
     */
    public function index(Request $request): View
    {
        $activeTab = $request->string('tab')->toString();
        if (! in_array($activeTab, ['data', 'orders', 'favorites', 'recent'], true)) {
            $activeTab = 'data';
        }

        $orders = $request->user()->orders()->with('items.product')->latest()->paginate(10);
        $favorites = $request->user()
            ->favorites()
            ->with('mainImage')
            ->withAvg(['reviews' => fn ($q) => $q->where('approved', true)], 'rating')
            ->withCount(['reviews' => fn ($q) => $q->where('approved', true)])
            ->latest()
            ->take(8)
            ->get();
        $recentlyViewed = $request->user()
            ->recentlyViewedProducts()
            ->with('mainImage')
            ->withAvg(['reviews' => fn ($q) => $q->where('approved', true)], 'rating')
            ->withCount(['reviews' => fn ($q) => $q->where('approved', true)])
            ->orderByDesc('recently_viewed.viewed_at')
            ->take(8)
            ->get();

        return view('profile.index', [
            'user' => $request->user(),
            'orders' => $orders,
            'favorites' => $favorites,
            'recentlyViewed' => $recentlyViewed,
            'activeTab' => $activeTab,
        ]);
    }

    public function favorites(Request $request): View
    {
        $orders = $request->user()->orders()->with('items.product')->latest()->paginate(10);
        $favorites = $request->user()
            ->favorites()
            ->with('mainImage')
            ->withAvg(['reviews' => fn ($q) => $q->where('approved', true)], 'rating')
            ->withCount(['reviews' => fn ($q) => $q->where('approved', true)])
            ->latest()
            ->paginate(12);
        $recentlyViewed = $request->user()
            ->recentlyViewedProducts()
            ->with('mainImage')
            ->withAvg(['reviews' => fn ($q) => $q->where('approved', true)], 'rating')
            ->withCount(['reviews' => fn ($q) => $q->where('approved', true)])
            ->orderByDesc('recently_viewed.viewed_at')
            ->take(8)
            ->get();

        return view('profile.index', [
            'user' => $request->user(),
            'orders' => $orders,
            'favorites' => $favorites,
            'recentlyViewed' => $recentlyViewed,
            'activeTab' => 'favorites',
        ]);
    }

    public function recent(Request $request): View
    {
        $orders = $request->user()->orders()->with('items.product')->latest()->paginate(10);
        $favorites = $request->user()
            ->favorites()
            ->with('mainImage')
            ->withAvg(['reviews' => fn ($q) => $q->where('approved', true)], 'rating')
            ->withCount(['reviews' => fn ($q) => $q->where('approved', true)])
            ->latest()
            ->take(8)
            ->get();
        $recentlyViewed = $request->user()
            ->recentlyViewedProducts()
            ->with('mainImage')
            ->withAvg(['reviews' => fn ($q) => $q->where('approved', true)], 'rating')
            ->withCount(['reviews' => fn ($q) => $q->where('approved', true)])
            ->orderByDesc('recently_viewed.viewed_at')
            ->paginate(12);

        return view('profile.index', [
            'user' => $request->user(),
            'orders' => $orders,
            'favorites' => $favorites,
            'recentlyViewed' => $recentlyViewed,
            'activeTab' => 'recent',
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $request->user()->fill(Arr::except($data, ['avatar']));

        if ($request->hasFile('avatar')) {
            $user = $request->user();
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function cancelOrder(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $cancellable = in_array($order->status, ['new', 'paid', 'processing'], true);

        if (! $cancellable) {
            return back()->with('toast', __('messages.order_cannot_cancel'));
        }

        $order->update([
            'status' => 'canceled',
            'canceled_at' => now(),
        ]);

        return back()->with('toast', __('messages.order_canceled'));
    }
}
