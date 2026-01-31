<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-12">
        @php
            $fallbackHeroImage = 'https://images.unsplash.com/photo-1506224774223-6f147960c8c9?auto=format&fit=crop&w=900&q=80';
            $heroImage = $heroBanner?->image
                ? (\Illuminate\Support\Str::startsWith($heroBanner->image, ['http://', 'https://'])
                    ? $heroBanner->image
                    : asset('storage/'.$heroBanner->image))
                : ($featured->first()?->mainImage?->path
                    ? asset('storage/'.$featured->first()->mainImage->path)
                    : $fallbackHeroImage);
            $weeklyBannerImage = 'https://www.lego.com/cdn/cs/set/assets/blt519dac201a3dd4c1/42172.png?fit=bounds&format=png&width=1200&height=900&dpr=1';
            $promoVisuals = [
                'lego-technic' => [
                    'image' => 'https://www.lego.com/cdn/cs/set/assets/blt519dac201a3dd4c1/42172.png?fit=bounds&format=png&width=1200&height=900&dpr=1',
                    'glow' => 'rgba(255, 214, 0, 0.35)',
                    'gradient' => 'linear-gradient(135deg, #111827 0%, #1f2937 55%, #111827 100%)',
                ],
                'lego-star-wars' => [
                    'image' => 'https://www.lego.com/cdn/cs/set/assets/blt3e07af4c83a87efd/75355.png?fit=bounds&format=png&width=1200&height=900&dpr=1',
                    'glow' => 'rgba(14, 165, 233, 0.35)',
                    'gradient' => 'linear-gradient(135deg, #020617 0%, #0f172a 55%, #111827 100%)',
                ],
                'lego-city' => [
                    'image' => 'https://www.lego.com/cdn/cs/set/assets/blt402b1fe599d2d64e/60419_alt1.png?fit=bounds&format=png&width=1200&height=900&dpr=1',
                    'glow' => 'rgba(251, 191, 36, 0.35)',
                    'gradient' => 'linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 55%, #0f172a 100%)',
                ],
            ];
            $categoryScenes = [
                'lego-city' => asset('images/categories/real/lego-city.png'),
                'lego-star-wars' => asset('images/categories/real/lego-star-wars.png'),
                'lego-technic' => asset('images/categories/real/lego-technic.png'),
                'lego-friends' => asset('images/categories/real/lego-friends.png'),
                'lego-creator' => asset('images/categories/real/lego-creator.png'),
                'lego-ninjago' => asset('images/categories/real/lego-ninjago.png'),
            ];
            $promoCategories = ['lego-technic', 'lego-star-wars', 'lego-city'];
        @endphp

        <section class="lego-hero lego-pattern rounded-[32px] p-8 md:p-12 shadow-xl" data-animate>
            <div class="relative z-10 grid gap-10 md:grid-cols-[1.1fr,0.9fr] md:items-center">
                <div data-animate>
                    <span class="lego-badge">{{ __('messages.hero_badge') }}</span>
                    <h1 class="mt-4 text-4xl font-extrabold md:text-6xl">
                        {{ __('messages.hero_title_new') }}
                    </h1>
                    <p class="mt-4 text-lg text-[color:var(--text-main)]">
                        {{ __('messages.hero_subtitle_new') }}
                    </p>
                    <p class="mt-3 text-sm text-[color:var(--muted)]">
                        {{ __('messages.hero_description') }}
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('catalog') }}" class="lego-btn lego-btn-primary">{{ __('messages.go_catalog') }}</a>
                        <a href="{{ route('about') }}" class="lego-btn lego-btn-secondary">{{ __('messages.about_company') }}</a>
                    </div>
                </div>
                <div class="lego-weekly-banner" style="--banner-image: url('{{ $weeklyBannerImage }}');" data-animate>
                    <div class="lego-weekly-content">
                        <div class="lego-weekly-eyebrow">Банер тижня</div>
                        <h3 class="mt-3 text-2xl font-extrabold text-white md:text-3xl">LEGO Technic — сила інженерії</h3>
                        <p class="mt-3 text-sm text-white/85 md:text-base">Потужні моделі для тих, хто хоче більше</p>
                        <a href="{{ route('catalog', ['category' => 'lego-technic']) }}" class="mt-5 inline-flex lego-btn lego-btn-primary text-xs md:text-sm">До каталогу</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="lego-section" data-animate>
            <div class="flex items-center justify-between">
                <h2 class="lego-section-title">Головні добірки</h2>
                <a href="{{ route('catalog') }}" class="text-sm font-semibold text-[color:var(--lego-blue)]">{{ __('messages.view_all') }}</a>
            </div>
            <div class="mt-6 grid gap-4 md:grid-cols-3">
                @forelse ($promoBanners as $banner)
                    @php
                        $promoCategory = $promoCategories[$loop->index % count($promoCategories)];
                        $bannerImage = $banner->image
                            ? (\Illuminate\Support\Str::startsWith($banner->image, ['http://', 'https://'])
                                ? $banner->image
                                : asset('storage/'.$banner->image))
                            : null;
                        $promoVisual = $promoVisuals[$promoCategory] ?? null;
                        $posterImage = $bannerImage ?: ($promoVisual['image'] ?? null);
                        $posterGradient = $promoVisual['gradient'] ?? 'linear-gradient(135deg, #0f172a 0%, #111827 55%, #0b1326 100%)';
                        $posterGlow = $promoVisual['glow'] ?? 'rgba(255, 255, 255, 0.2)';
                        $posterLink = $banner->link_url ?: route('catalog', ['category' => $promoCategory]);
                    @endphp
                    <a href="{{ $posterLink }}" class="lego-poster block" style="--poster-image: url('{{ $posterImage }}'); --poster-glow: {{ $posterGlow }}; --poster-gradient: {{ $posterGradient }};">
                        <div class="lego-poster-content">
                            <div class="text-xs uppercase tracking-widest text-white/70">LEGO Featured</div>
                            <div class="mt-2 text-2xl font-extrabold">{{ $banner->title }}</div>
                            <p class="mt-2 text-sm text-white/90">{{ $banner->subtitle }}</p>
                            <span class="mt-4 inline-flex lego-btn lego-btn-secondary text-xs">{{ __('messages.shop_now') }}</span>
                        </div>
                    </a>
                @empty
                    <a href="{{ route('catalog', ['category' => 'lego-technic']) }}" class="lego-poster block" style="--poster-image: url('{{ $promoVisuals['lego-technic']['image'] }}'); --poster-glow: {{ $promoVisuals['lego-technic']['glow'] }}; --poster-gradient: {{ $promoVisuals['lego-technic']['gradient'] }};">
                        <div class="lego-poster-content">
                            <div class="text-xs uppercase tracking-widest text-white/70">LEGO Technic</div>
                            <div class="mt-2 text-2xl font-extrabold">{{ __('messages.promo_technic') }}</div>
                            <p class="mt-2 text-sm text-white/90">{{ __('messages.promo_technic_desc') }}</p>
                            <span class="mt-4 inline-flex lego-btn lego-btn-secondary text-xs">{{ __('messages.shop_now') }}</span>
                        </div>
                    </a>
                    <a href="{{ route('catalog', ['category' => 'lego-star-wars']) }}" class="lego-poster block" style="--poster-image: url('{{ $promoVisuals['lego-star-wars']['image'] }}'); --poster-glow: {{ $promoVisuals['lego-star-wars']['glow'] }}; --poster-gradient: {{ $promoVisuals['lego-star-wars']['gradient'] }};">
                        <div class="lego-poster-content">
                            <div class="text-xs uppercase tracking-widest text-white/70">Star Wars</div>
                            <div class="mt-2 text-2xl font-extrabold">{{ __('messages.promo_starwars') }}</div>
                            <p class="mt-2 text-sm text-white/90">{{ __('messages.promo_starwars_desc') }}</p>
                            <span class="mt-4 inline-flex lego-btn lego-btn-secondary text-xs">{{ __('messages.shop_now') }}</span>
                        </div>
                    </a>
                    <a href="{{ route('catalog', ['category' => 'lego-city']) }}" class="lego-poster block" style="--poster-image: url('{{ $promoVisuals['lego-city']['image'] }}'); --poster-glow: {{ $promoVisuals['lego-city']['glow'] }}; --poster-gradient: {{ $promoVisuals['lego-city']['gradient'] }};">
                        <div class="lego-poster-content">
                            <div class="text-xs uppercase tracking-widest text-white/70">LEGO City</div>
                            <div class="mt-2 text-2xl font-extrabold">{{ __('messages.promo_new') }}</div>
                            <p class="mt-2 text-sm text-white/90">{{ __('messages.promo_new_desc') }}</p>
                            <span class="mt-4 inline-flex lego-btn lego-btn-secondary text-xs">{{ __('messages.shop_now') }}</span>
                        </div>
                    </a>
                @endforelse
            </div>
        </section>

        <section class="lego-section lego-categories" data-animate>
            <h2 class="lego-section-title">{{ __('messages.categories') }}</h2>
            <div class="lego-categories-grid mt-8 grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($categories as $category)
                    @php
                        $sceneImage = $categoryScenes[$category->slug] ?? null;
                    @endphp
                    <a href="{{ route('catalog', ['category' => $category->slug]) }}" class="lego-category-link" aria-label="{{ $category->name }}">
                        @if ($sceneImage)
                            <img src="{{ $sceneImage }}" alt="{{ $category->name }}" class="lego-category-figure">
                        @endif
                        <div class="lego-category-text">
                            <div class="lego-category-name">{{ $category->name }}</div>
                            <div class="lego-category-count">{{ $category->products_count }} {{ __('messages.products') }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="lego-section" data-animate>
            <div class="flex items-center justify-between">
                <h2 class="lego-section-title">{{ __('messages.best_sellers') }}</h2>
                <a href="{{ route('catalog') }}" class="text-sm font-semibold text-[color:var(--lego-blue)]">{{ __('messages.go_catalog') }}</a>
            </div>
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($hits as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            <div class="mt-6">
                <a href="{{ route('catalog') }}" class="lego-btn lego-btn-primary">{{ __('messages.go_catalog') }}</a>
            </div>
        </section>

        @if ($recentlyViewed->isNotEmpty())
            <section class="lego-section" data-animate>
                <h2 class="lego-section-title">{{ __('messages.recently_viewed') }}</h2>
                <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($recentlyViewed as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </section>
        @endif

        <section class="lego-section" data-animate>
            <div class="grid gap-8 md:grid-cols-[1.2fr,0.8fr]">
                <div class="lego-card p-8">
                    <h2 class="lego-section-title">{{ __('messages.about_short') }}</h2>
                    <p class="mt-4 text-sm text-[color:var(--muted)]">
                        {{ __('messages.about_short_text') }}
                    </p>
                    <a href="{{ route('about') }}" class="mt-6 inline-flex lego-btn lego-btn-secondary">{{ __('messages.learn_more') }}</a>
                </div>
                <div class="grid gap-4">
                    <div class="lego-card p-6">
                        <div class="text-2xl">🚚</div>
                        <div class="mt-2 font-semibold">{{ __('messages.benefit_fast') }}</div>
                        <p class="mt-1 text-sm text-[color:var(--muted)]">{{ __('messages.benefit_fast_desc') }}</p>
                    </div>
                    <div class="lego-card p-6">
                        <div class="text-2xl">✅</div>
                        <div class="mt-2 font-semibold">{{ __('messages.benefit_official') }}</div>
                        <p class="mt-1 text-sm text-[color:var(--muted)]">{{ __('messages.benefit_official_desc') }}</p>
                    </div>
                    <div class="lego-card p-6">
                        <div class="text-2xl">🛡️</div>
                        <div class="mt-2 font-semibold">{{ __('messages.benefit_return') }}</div>
                        <p class="mt-1 text-sm text-[color:var(--muted)]">{{ __('messages.benefit_return_desc') }}</p>
                    </div>
                    <div class="lego-card p-6">
                        <div class="text-2xl">💬</div>
                        <div class="mt-2 font-semibold">{{ __('messages.benefit_support') }}</div>
                        <p class="mt-1 text-sm text-[color:var(--muted)]">{{ __('messages.benefit_support_desc') }}</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
