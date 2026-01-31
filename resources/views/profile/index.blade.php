<x-app-layout>
    @php
        $avatarUrl = $user->avatar ? asset('storage/'.$user->avatar) : null;
    @endphp
    <div class="mx-auto max-w-6xl px-4 py-10">
        <div class="lego-card p-6 profile-hero">
            <div class="profile-hero-main">
                <div class="profile-avatar-lg">
                    @if ($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="profile-avatar-img">
                    @else
                        <span class="profile-avatar-fallback">{{ mb_substr($user->name, 0, 1) }}</span>
                    @endif
                </div>
                <div>
                    <h1 class="text-3xl font-bold">{{ $user->name }}</h1>
                    <p class="text-sm text-[color:var(--muted)]">{{ $user->email }}</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('profile.edit') }}" class="lego-btn lego-btn-secondary text-xs">{{ __('messages.edit_profile') }}</a>
                        <a href="{{ route('profile.index', ['tab' => 'orders']) }}" class="lego-btn lego-btn-primary text-xs">{{ __('messages.order_history') }}</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="lego-btn lego-btn-secondary text-xs">{{ __('messages.logout') }}</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="profile-hero-stats">
                <div class="profile-stat">
                    <div class="profile-stat-value">{{ $orders->total() }}</div>
                    <div class="profile-stat-label">{{ __('messages.order_history') }}</div>
                </div>
                <div class="profile-stat">
                    <div class="profile-stat-value">{{ $favorites->count() }}</div>
                    <div class="profile-stat-label">{{ __('messages.favorites') }}</div>
                </div>
                <div class="profile-stat">
                    <div class="profile-stat-value">{{ $recentlyViewed->count() }}</div>
                    <div class="profile-stat-label">{{ __('messages.recently_viewed') }}</div>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <div class="flex flex-wrap gap-2" data-tabs data-default-tab="{{ $activeTab ?? 'data' }}">
                <button class="lego-tab is-active" data-tab="data">{{ __('messages.profile_data') }}</button>
                <button class="lego-tab" data-tab="orders">{{ __('messages.order_history') }}</button>
                <button class="lego-tab" data-tab="favorites">{{ __('messages.favorites') }}</button>
                <button class="lego-tab" data-tab="recent">{{ __('messages.recently_viewed') }}</button>
            </div>
        </div>

        <section class="mt-6 lego-card p-6" data-tab-content="data">
            <div class="grid gap-6 md:grid-cols-[1.2fr,0.8fr]">
                <div>
                    <h2 class="text-xl font-bold">{{ __('messages.profile_data') }}</h2>
                    <p class="mt-2 text-sm text-[color:var(--muted)]">{{ __('messages.profile_data_desc') }}</p>
                    <div class="mt-4 grid gap-3 text-sm">
                        <div><span class="text-[color:var(--muted)]">{{ __('messages.name') }}:</span> {{ $user->name }}</div>
                        <div><span class="text-[color:var(--muted)]">{{ __('messages.email') }}:</span> {{ $user->email }}</div>
                        <div><span class="text-[color:var(--muted)]">{{ __('messages.phone') }}:</span> {{ $user->phone ?? '-' }}</div>
                        <div><span class="text-[color:var(--muted)]">{{ __('messages.city') }}:</span> {{ $user->city ?? '-' }}</div>
                        <div><span class="text-[color:var(--muted)]">{{ __('messages.address') }}:</span> {{ $user->address ?? '-' }}</div>
                    </div>
                </div>
                <div class="flex flex-col justify-between gap-3 rounded-2xl border border-[color:var(--border)] bg-[color:var(--card)] p-4">
                    <div>
                        <div class="text-xs font-semibold text-[color:var(--muted)]">{{ __('messages.profile_status') }}</div>
                        <div class="mt-2 text-lg font-semibold">{{ __('messages.profile_ready') }}</div>
                        <p class="mt-2 text-sm text-[color:var(--muted)]">{{ __('messages.profile_ready_desc') }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="lego-btn lego-btn-secondary">{{ __('messages.edit_profile') }}</a>
                </div>
            </div>
        </section>

        <section class="mt-6 lego-card p-6 hidden" data-tab-content="orders">
            <h2 class="text-xl font-bold">{{ __('messages.order_history') }}</h2>
            <div class="mt-4 space-y-4">
                @forelse ($orders as $order)
                    <div class="rounded-xl border border-[color:var(--border)] p-4">
                        <div class="flex items-center justify-between">
                            <div class="font-semibold">#{{ $order->id }} • {{ $order->status }}</div>
                            <div class="text-sm text-[color:var(--muted)]">{{ $order->created_at->format('d.m.Y') }}</div>
                        </div>
                        <div class="mt-2 text-sm">
                            @foreach ($order->items as $item)
                                <div>{{ $item->product->name }} × {{ $item->quantity }}</div>
                            @endforeach
                        </div>
                        <div class="mt-2 font-semibold">{{ __('messages.total') }}: {{ number_format($order->total, 2) }} грн</div>
                        @if (in_array($order->status, ['new', 'paid', 'processing'], true))
                            <form method="POST" action="{{ route('profile.orders.cancel', $order) }}" class="mt-3">
                                @csrf
                                <button class="lego-btn lego-btn-primary text-xs">{{ __('messages.cancel_order') }}</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-[color:var(--muted)]">{{ __('messages.no_orders') }}</p>
                @endforelse
            </div>
            <div class="mt-4">{{ $orders->appends(request()->query())->links() }}</div>
        </section>

        <section class="mt-6 lego-card p-6 hidden" data-tab-content="favorites">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold">{{ __('messages.favorites') }}</h2>
                <a href="{{ route('profile.favorites') }}" class="text-xs font-semibold text-[color:var(--lego-blue)]">{{ __('messages.open_favorites') }}</a>
            </div>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($favorites as $product)
                    <x-product-card :product="$product" size="compact" />
                @empty
                    <p class="text-sm text-[color:var(--muted)]">{{ __('messages.no_favorites') }}</p>
                @endforelse
            </div>
            @if (method_exists($favorites, 'links'))
                <div class="mt-4">{{ $favorites->appends(request()->query())->links() }}</div>
            @endif
        </section>

        <section class="mt-6 lego-card p-6 hidden" data-tab-content="recent">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold">{{ __('messages.recently_viewed') }}</h2>
                <a href="{{ route('profile.recent') }}" class="text-xs font-semibold text-[color:var(--lego-blue)]">{{ __('messages.open_recent') }}</a>
            </div>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($recentlyViewed as $product)
                    <x-product-card :product="$product" size="compact" />
                @empty
                    <p class="text-sm text-[color:var(--muted)]">{{ __('messages.no_recent') }}</p>
                @endforelse
            </div>
            @if (method_exists($recentlyViewed, 'links'))
                <div class="mt-4">{{ $recentlyViewed->appends(request()->query())->links() }}</div>
            @endif
        </section>
    </div>
</x-app-layout>
