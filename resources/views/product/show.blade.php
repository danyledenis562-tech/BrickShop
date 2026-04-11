@section('title', $product->name.' — '.config('app.name'))
@php
    $isNew = $product->created_at?->gt(now()->subDays(14));
    $isTop = $product->is_featured || $product->popularity >= 250;
    $isSale = $product->old_price && $product->old_price > $product->price;
    $galleryRows = $product->images->sort(function ($a, $b) {
        if ($a->is_main !== $b->is_main) {
            return $b->is_main <=> $a->is_main;
        }

        return $a->id <=> $b->id;
    })->values();
    if ($galleryRows->isEmpty() && $product->mainImage) {
        $galleryRows = collect([$product->mainImage]);
    }
    $galleryUrl = function ($image): ?string {
        if (! $image) {
            return null;
        }
        $path = (string) ($image->path ?? '');
        if ($path === '') {
            return null;
        }
        $normalizedPath = ltrim($path, '/');
        if (\Illuminate\Support\Str::startsWith($normalizedPath, 'storage/')) {
            $normalizedPath = \Illuminate\Support\Str::after($normalizedPath, 'storage/');
        }

        return match (true) {
            \Illuminate\Support\Str::startsWith($path, ['http://', 'https://']) => $path,
            \Illuminate\Support\Str::startsWith($path, ['images/', '/images/', 'build/', '/build/']) => asset(ltrim($path, '/')),
            default => route('media.public', ['path' => $normalizedPath]),
        };
    };
    $firstUrl = $galleryUrl($galleryRows->first());
    $schemaImageUrls = $galleryRows->map(fn ($img) => $galleryUrl($img))->filter()->values()->all();
    $metaDesc = \Illuminate\Support\Str::limit(strip_tags((string) $product->description), 160, '…');
    $schemaProduct = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->name,
        'sku' => $product->set_number ?? (string) $product->id,
        'description' => $metaDesc,
        'image' => count($schemaImageUrls) > 0 ? $schemaImageUrls : ($firstUrl ? [$firstUrl] : []),
        'offers' => [
            '@type' => 'Offer',
            'priceCurrency' => 'UAH',
            'price' => (string) $product->price,
            'availability' => $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'url' => url()->current(),
        ],
    ];
    if ($product->reviews->count() > 0) {
        $schemaProduct['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => (string) $ratingAverage,
            'reviewCount' => (string) $product->reviews->count(),
        ];
    }
    $breadcrumbItems = [
        ['label' => __('messages.breadcrumb_home'), 'url' => route('welcome')],
        ['label' => __('messages.catalog'), 'url' => route('catalog')],
    ];
    if ($product->category) {
        $breadcrumbItems[] = [
            'label' => $product->category->name,
            'url' => route('catalog', ['category' => $product->category->slug]),
        ];
    }
    $breadcrumbItems[] = ['label' => $product->name, 'url' => null];
@endphp
<x-app-layout>
    <x-slot name="head">
        <meta name="description" content="{{ $metaDesc }}">
        <link rel="canonical" href="{{ url()->current() }}">
        <meta property="og:type" content="product">
        <meta property="og:title" content="{{ $product->name }}">
        <meta property="og:description" content="{{ $metaDesc }}">
        <meta property="og:url" content="{{ url()->current() }}">
        @if ($firstUrl)
            <meta property="og:image" content="{{ $firstUrl }}">
        @endif
        <script type="application/ld+json">
{!! json_encode($schemaProduct, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-10">
        <x-breadcrumbs :items="$breadcrumbItems" />

        <div class="grid gap-8 lg:grid-cols-2">
            <div class="lego-card p-4" data-product-gallery tabindex="0" aria-label="{{ __('messages.product_gallery') }}">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-stretch">
                    @if ($galleryRows->count() > 1)
                        <div class="product-gallery-thumbs order-2 lg:order-1 lg:w-[5.25rem]" role="tablist" aria-label="{{ __('messages.product_gallery_thumbs') }}">
                            @foreach ($galleryRows as $idx => $imgRow)
                                @php $thumbUrl = $galleryUrl($imgRow); @endphp
                                @if ($thumbUrl)
                                    <button
                                        type="button"
                                        role="tab"
                                        class="product-gallery-thumb {{ $idx === 0 ? 'is-active' : '' }}"
                                        data-gallery-thumb
                                        data-gallery-src="{{ $thumbUrl }}"
                                        data-gallery-alt="{{ $product->name }}"
                                        aria-selected="{{ $idx === 0 ? 'true' : 'false' }}"
                                        tabindex="{{ $idx === 0 ? '0' : '-1' }}"
                                    >
                                        <img src="{{ $thumbUrl }}" alt="" loading="lazy" width="72" height="72">
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    @endif
                    <div class="relative min-w-0 flex-1 order-1 lg:order-2">
                        <div class="relative lego-brick lego-product-photo h-72 min-h-[18rem] sm:h-80 lg:min-h-[22rem]">
                            <div class="absolute inset-x-4 top-4 z-20 flex flex-wrap gap-2 max-w-full">
                                @if ($isNew)
                                    <span class="lego-badge lego-badge-new">{{ __('messages.badge_new') }}</span>
                                @endif
                                @if ($isTop)
                                    <span class="lego-badge lego-badge-top">{{ __('messages.badge_top') }}</span>
                                @endif
                                @if ($isSale)
                                    <span class="lego-badge lego-badge-sale">{{ __('messages.badge_sale') }}</span>
                                @endif
                            </div>
                            <div class="product-magnifier relative h-full w-full cursor-crosshair" id="product-magnifier">
                                @if ($firstUrl)
                                    <img
                                        src="{{ $firstUrl }}"
                                        alt="{{ $product->name }}"
                                        class="product-gallery-main-img magnifier-source h-full w-full object-cover"
                                        data-magnifier-source
                                        data-gallery-main
                                        loading="eager"
                                    >
                                @else
                                    <x-product-image :path="null" :alt="$product->name" class="magnifier-source h-full w-full object-cover" data-magnifier-source />
                                @endif
                                <div class="magnifier-lens" id="magnifier-lens" aria-hidden="true"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lego-card p-6">
                <h1 class="text-3xl font-bold">{{ $product->name }}</h1>
                @if ($product->set_number)
                    <p class="mt-1 text-sm text-[color:var(--muted)]">
                        {{ __('messages.set_number') }}:
                        <span class="font-mono font-semibold text-[color:var(--text-main)]">{{ $product->set_number }}</span>
                    </p>
                @endif
                <div class="mt-2 flex items-center gap-2 text-sm text-[color:var(--muted)]">
                    <span>{{ $product->series ?? 'LEGO' }}</span>
                    <span>•</span>
                    <span>{{ $product->reviews->count() }} {{ __('messages.reviews') }}</span>
                    <span class="text-[color:var(--lego-yellow)]">
                        @for ($i = 1; $i <= 5; $i++)
                            <span>{{ $i <= round($ratingAverage) ? '★' : '☆' }}</span>
                        @endfor
                    </span>
                </div>
                <div class="mt-4 flex items-center gap-3">
                    <span class="text-3xl font-extrabold">{{ $product->price }} грн</span>
                    @if ($isSale)
                        <span class="text-sm line-through text-[color:var(--muted)]">{{ $product->old_price }} грн</span>
                    @endif
                </div>
                <p class="mt-2 text-sm {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $product->stock > 0 ? __('messages.in_stock') : __('messages.out_of_stock') }}
                </p>
                @php $lowTh = (int) config('shop.low_stock_threshold', 5); @endphp
                @if ($product->stock > 0 && $product->stock <= $lowTh)
                    <p class="mt-1 text-sm font-semibold text-amber-600">{{ __('messages.low_stock_left', ['count' => $product->stock]) }}</p>
                @endif

                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <form method="POST" action="{{ route('cart.add', $product) }}">
                        @csrf
                        <input type="hidden" name="redirect" value="checkout">
                        <button
                            type="submit"
                            class="lego-btn lego-btn-primary {{ $product->stock <= 0 ? 'lego-btn-disabled' : '' }}"
                            @if ($product->stock <= 0) disabled aria-disabled="true" @endif
                        >{{ __('messages.buy_now') }}</button>
                    </form>
                    <form method="POST" action="{{ route('favorites.store', $product) }}">
                        @csrf
                        <button type="submit" class="lego-btn lego-btn-secondary">♥ {{ __('messages.favorite') }}</button>
                    </form>
                </div>

                <div class="mt-6 grid gap-3 md:grid-cols-2">
                    <div class="lego-card p-4">
                        <div class="text-xs font-semibold text-[color:var(--muted)]">{{ __('messages.delivery') }}</div>
                        <div class="mt-2 text-sm">{{ __('messages.delivery_fast') }}</div>
                        <div class="mt-1 text-xs text-[color:var(--muted)]">{{ __('messages.delivery_details') }}</div>
                    </div>
                    <div class="lego-card p-4">
                        <div class="text-xs font-semibold text-[color:var(--muted)]">{{ __('messages.payment') }}</div>
                        <div class="mt-2 text-sm">{{ __('messages.payment_methods') }}</div>
                        <div class="mt-1 text-xs text-[color:var(--muted)]">{{ __('messages.payment_details') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <section class="mt-10 lego-card p-6">
            <div class="flex flex-wrap gap-2" data-tabs>
                <button class="lego-tab is-active" data-tab="description">{{ __('messages.description') }}</button>
                <button class="lego-tab" data-tab="specs">{{ __('messages.specs') }}</button>
                <button class="lego-tab" data-tab="reviews">{{ __('messages.reviews') }}</button>
                <button class="lego-tab" data-tab="shipping">{{ __('messages.shipping_payment') }}</button>
            </div>

            <div class="mt-6" data-tab-content="description">
                <p class="text-sm text-[color:var(--muted)]">{{ $product->description }}</p>
            </div>

            <div class="mt-6 hidden" data-tab-content="specs">
                <table class="w-full text-sm">
                    <tr class="border-b border-[color:var(--border)]"><td class="py-2 text-[color:var(--muted)]">{{ __('messages.set_number') }}</td><td class="py-2 text-right font-mono">{{ $product->set_number ?? '—' }}</td></tr>
                    <tr class="border-b border-[color:var(--border)]"><td class="py-2 text-[color:var(--muted)]">{{ __('messages.age') }}</td><td class="py-2 text-right">{{ $product->age ?? '-' }}</td></tr>
                    <tr class="border-b border-[color:var(--border)]"><td class="py-2 text-[color:var(--muted)]">{{ __('messages.difficulty') }}</td><td class="py-2 text-right">{{ $product->difficulty ?? '-' }}</td></tr>
                    <tr class="border-b border-[color:var(--border)]"><td class="py-2 text-[color:var(--muted)]">{{ __('messages.pieces') }}</td><td class="py-2 text-right">{{ $product->pieces ?? '-' }}</td></tr>
                    <tr class="border-b border-[color:var(--border)]"><td class="py-2 text-[color:var(--muted)]">{{ __('messages.brand') }}</td><td class="py-2 text-right">{{ $product->brand }}</td></tr>
                    <tr class="border-b border-[color:var(--border)]"><td class="py-2 text-[color:var(--muted)]">{{ __('messages.series') }}</td><td class="py-2 text-right">{{ $product->series ?? '-' }}</td></tr>
                    <tr><td class="py-2 text-[color:var(--muted)]">{{ __('messages.country') }}</td><td class="py-2 text-right">{{ $product->country ?? '-' }}</td></tr>
                </table>
            </div>

            <div class="mt-6 hidden" data-tab-content="reviews">
                <div class="flex items-center gap-2 text-sm text-[color:var(--muted)]">
                    <span>{{ __('messages.rating') }}: {{ $ratingAverage }}</span>
                    <span class="text-[color:var(--lego-yellow)]">
                        @for ($i = 1; $i <= 5; $i++)
                            <span>{{ $i <= round($ratingAverage) ? '★' : '☆' }}</span>
                        @endfor
                    </span>
                </div>

                @auth
                    <form method="POST" action="{{ route('product.review', $product) }}" class="mt-4 grid gap-3 md:grid-cols-6">
                        @csrf
                        <select name="rating" class="lego-input md:col-span-1">
                            @for ($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                        <textarea name="comment" class="lego-input md:col-span-4" rows="2" placeholder="{{ __('messages.review_placeholder') }}"></textarea>
                        <button class="lego-btn lego-btn-primary md:col-span-1">{{ __('messages.send') }}</button>
                    </form>
                @else
                    <p class="mt-4 text-sm text-[color:var(--muted)]">{{ __('messages.login_to_review') }}</p>
                @endauth

                <div class="mt-6 space-y-4">
                    @forelse ($product->reviews as $review)
                        <div class="rounded-2xl border border-[color:var(--border)] p-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-[color:var(--lego-yellow)] text-center text-sm font-bold leading-10">
                                    {{ mb_substr($review->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-sm font-semibold">{{ $review->user->name }}</div>
                                    <div class="text-xs text-[color:var(--muted)]">{{ $review->created_at->format('d.m.Y') }}</div>
                                </div>
                            </div>
                            <div class="mt-2 text-[color:var(--lego-yellow)]">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
                                @endfor
                            </div>
                            <p class="mt-2 text-sm text-[color:var(--muted)]">{{ $review->comment }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-[color:var(--muted)]">{{ __('messages.no_reviews') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="mt-6 hidden" data-tab-content="shipping">
                <div class="space-y-3 text-sm text-[color:var(--muted)]">
                    <p>{{ __('messages.shipping_text_1') }}</p>
                    <p>{{ __('messages.shipping_text_2') }}</p>
                    <p>{{ __('messages.shipping_text_3') }}</p>
                </div>
            </div>
        </section>

        <section class="mt-10">
            <h2 class="text-xl font-bold">{{ __('messages.related') }}</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($related as $item)
                    <x-product-card :product="$item" size="compact" />
                @endforeach
            </div>
        </section>
    </div>
</x-app-layout>
