<?php

namespace App\Services\Profile;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class ProfileDashboardData
{
    public function __construct(
        public readonly LengthAwarePaginator $orders,
        /** @var Collection<int, \App\Models\Product> */
        public readonly Collection $favoritesPreview,
        /** @var Collection<int, \App\Models\Product> */
        public readonly Collection $recentlyViewedPreview,
        public readonly LengthAwarePaginator $bonusTransactions,
        public readonly string $activeTab,
    ) {}
}
