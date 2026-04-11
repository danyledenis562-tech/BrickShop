<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Order;
use App\Services\Profile\ProfileDashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(Request $request, ProfileDashboardService $dashboard): View
    {
        $activeTab = $dashboard->resolveActiveTab($request);
        $overview = $dashboard->build($request->user(), $activeTab);

        return view('profile.index', [
            'user' => $request->user(),
            'orders' => $overview->orders,
            'favorites' => $overview->favoritesPreview,
            'recentlyViewed' => $overview->recentlyViewedPreview,
            'bonusTransactions' => $overview->bonusTransactions,
            'activeTab' => $overview->activeTab,
        ]);
    }

    public function favorites(): RedirectResponse
    {
        return redirect()->route('profile.index', ['tab' => 'favorites']);
    }

    public function recent(): RedirectResponse
    {
        return redirect()->route('profile.index', ['tab' => 'recent']);
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $request->user()->fill(Arr::except($validated, ['avatar']));

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

        $cancellable = in_array($order->status->value, ['new', 'paid', 'processing'], true);

        if (! $cancellable) {
            return back()->with('toast', __('messages.order_cannot_cancel'));
        }

        $user = $request->user();

        $order->update([
            'status' => 'canceled',
            'canceled_at' => now(),
        ]);

        if ($order->bonus_spent > 0) {
            $user->addBonus(
                (int) $order->bonus_spent,
                __('messages.bonus_refund_spent', ['id' => $order->id]),
                $order
            );
        }

        if ($order->bonus_earned > 0) {
            $user->spendBonus(
                (int) $order->bonus_earned,
                __('messages.bonus_revert_earned', ['id' => $order->id]),
                $order
            );
        }

        return back()->with('toast', __('messages.order_canceled'));
    }
}
