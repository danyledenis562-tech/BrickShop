@php
    $cartCount = collect(session('cart', []))->sum('quantity');
    $locale = app()->getLocale();
    $localeLabels = ['uk' => 'UA', 'en' => 'EN', 'pl' => 'PL', 'ru' => 'RU'];
@endphp

<header class="lego-nav">
    <div class="lego-header mx-auto flex max-w-7xl flex-col gap-4 px-4 py-4 md:flex-row md:items-center md:gap-6">
        <a href="{{ route('welcome') }}" class="lego-logo" aria-label="Brick Shop">
            <img src="{{ asset('images/brickshop-logo.png') }}" alt="Brick Shop" class="lego-logo-img">
        </a>

        @can('admin')
            <div class="ml-auto flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" class="lego-btn lego-btn-primary text-xs">{{ __('messages.admin') }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="lego-btn lego-btn-secondary text-xs">
                        {{ __('messages.logout') }}
                    </button>
                </form>
            </div>
        @else
            <form method="GET" action="{{ route('catalog') }}" class="lego-search relative flex-1">
                <input
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="{{ __('messages.search_products') }}"
                    class="lego-input h-12 w-full rounded-full bg-[color:var(--card)] pl-12 text-[color:var(--text)] placeholder:text-[color:var(--muted)]"
                    data-search-input
                    data-search-url="{{ route('catalog.suggestions') }}"
                    autocomplete="off"
                >
                <span class="lego-search-icon">🔎</span>
                <div class="absolute left-0 right-0 top-full z-50 hidden" data-search-suggestions>
                    <div class="lego-card mt-2 overflow-hidden p-2">
                        <div class="text-xs text-[color:var(--muted)] px-2 py-1">{{ __('messages.search_hint') }}</div>
                        <div data-search-items class="space-y-2"></div>
                    </div>
                </div>
            </form>

            <div class="lego-header-actions flex items-center gap-2">
                <details class="lego-dropdown relative text-xs font-semibold">
                    <summary class="lego-pill cursor-pointer">{{ $localeLabels[$locale] ?? 'UA' }} ▼</summary>
                    <div class="absolute right-0 mt-2 w-24 rounded-xl border border-[color:var(--border)] bg-[color:var(--card)] p-2 shadow">
                        @foreach ($localeLabels as $code => $label)
                            <a href="{{ route('locale.switch', $code) }}" class="block rounded-lg px-2 py-1 hover:bg-[color:var(--lego-yellow)]">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </details>

                <button type="button" data-theme-toggle data-theme-label-light="{{ __('messages.theme_dark') }}" data-theme-label-dark="{{ __('messages.theme_light') }}" class="lego-icon-btn" aria-label="{{ __('messages.toggle_theme') }}">
                    <span data-theme-icon>🌙</span>
                    <span class="sr-only" data-theme-label>{{ __('messages.toggle_theme') }}</span>
                </button>

                @auth
                    <a href="{{ route('profile.favorites') }}" class="lego-icon-btn" aria-label="{{ __('messages.favorites') }}">♥</a>
                @else
                    <a href="{{ route('login') }}" class="lego-icon-btn" aria-label="{{ __('messages.favorites') }}">♥</a>
                @endauth

                <a href="{{ route('cart.index') }}" class="lego-icon-btn relative" aria-label="{{ __('messages.cart') }}">
                    🛒
                    @if ($cartCount > 0)
                        <span class="lego-pill-count">{{ $cartCount }}</span>
                    @endif
                </a>

                @auth
                    <details class="lego-dropdown relative text-xs font-semibold">
                        <summary class="lego-icon-btn" aria-label="{{ __('messages.profile') }}">👤</summary>
                        <div class="absolute right-0 mt-2 w-48 rounded-xl border border-[color:var(--border)] bg-[color:var(--card)] p-2 shadow">
                            <a href="{{ route('profile.index') }}" class="block rounded-lg px-2 py-1 hover:bg-[color:var(--lego-yellow)]">
                                {{ __('messages.profile') }}
                            </a>
                            <a href="{{ route('profile.index', ['tab' => 'orders']) }}" class="block rounded-lg px-2 py-1 hover:bg-[color:var(--lego-yellow)]">
                                {{ __('messages.order_history') }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                                @csrf
                                <button type="submit" class="w-full rounded-lg px-2 py-1 text-left hover:bg-[color:var(--lego-yellow)]">
                                    {{ __('messages.logout') }}
                                </button>
                            </form>
                        </div>
                    </details>
                @else
                    <a href="{{ route('login') }}" class="lego-icon-btn" aria-label="{{ __('messages.login') }}">👤</a>
                @endauth
            </div>
        @endcan
    </div>
</header>
