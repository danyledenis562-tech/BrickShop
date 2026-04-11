<?php

namespace App\Services\Profile;

use App\Models\User;
use Illuminate\Http\Request;

final class ProfileDashboardService
{
    public function resolveActiveTab(Request $request): string
    {
        $activeTab = $request->string('tab')->toString();

        return in_array($activeTab, ['data', 'orders', 'favorites', 'recent', 'bonus'], true)
            ? $activeTab
            : 'data';
    }

    public function build(User $user, string $activeTab): ProfileDashboardData
    {
        $orders = $user->orders()
            ->with('items.product')
            ->latest()
            ->paginate(10);

        $favoritesPreview = $user
            ->favorites()
            ->with('coverImage')
            ->withAvg(['reviews' => fn ($q) => $q->where('approved', true)], 'rating')
            ->withCount(['reviews' => fn ($q) => $q->where('approved', true)])
            ->latest()
            ->take(8)
            ->get();

        $recentlyViewedPreview = $user
            ->recentlyViewedProducts()
            ->with('coverImage')
            ->withAvg(['reviews' => fn ($q) => $q->where('approved', true)], 'rating')
            ->withCount(['reviews' => fn ($q) => $q->where('approved', true)])
            ->orderByDesc('recently_viewed.viewed_at')
            ->take(8)
            ->get();

        $bonusTransactions = $user
            ->bonusTransactions()
            ->with('order')
            ->latest()
            ->paginate(10);

        return new ProfileDashboardData(
            orders: $orders,
            favoritesPreview: $favoritesPreview,
            recentlyViewedPreview: $recentlyViewedPreview,
            bonusTransactions: $bonusTransactions,
            activeTab: $activeTab,
        );
    }
}
