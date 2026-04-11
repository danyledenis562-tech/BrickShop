@props([
    'product',
    'size' => 'default',
    'showBadges' => true,
    'showActions' => true,
])

@php
    $isNew = $product->created_at?->gt(now()->subDays(14));
    $isTop = $product->is_featured || $product->popularity >= 250;
    $isSale = $product->old_price && $product->old_price > $product->price;
    $rating = number_format($product->reviews_avg_rating ?? 0, 1);
    $imageHeight = $size === 'compact' ? 'h-36' : 'h-44';
    $lowTh = (int) config('shop.low_stock_threshold', 5);
    $isLowStock = $product->stock > 0 && $product->stock <= $lowTh;
@endphp

<div class="lego-card lego-product relative p-4">
    <a href="{{ route('product.show', $product) }}" class="lego-card-link" aria-label="{{ $product->name }}"></a>

    <div class="lego-product-content">
        @if ($showBadges)
            <div class="absolute inset-x-4 top-4 z-20 flex justify-between items-start gap-2">
                <div class="flex flex-wrap gap-2 min-w-0 max-w-[55%]">
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
                <span class="lego-badge shrink-0">{{ $product->series ?? 'LEGO' }}</span>
            </div>
        @endif

        <div class="lego-brick lego-product-photo {{ $imageHeight }}">
            <x-product-image
                :path="$product->mainImage?->path"
                :alt="$product->name"
                class="h-full w-full object-cover"
            />
            @if ($showActions)
                <div class="lego-card-actions z-20">
                    @if ($product->stock > 0)
                        <form method="POST" action="{{ route('cart.add', $product) }}">
                            @csrf
                            <button type="submit" class="lego-btn lego-btn-primary text-xs cursor-pointer">{{ __('messages.add_to_cart') }}</button>
                        </form>
                    @else
                        <button type="button" class="lego-btn lego-btn-primary lego-btn-disabled text-xs" disabled aria-disabled="true">{{ __('messages.add_to_cart') }}</button>
                    @endif
                    <form method="POST" action="{{ route('favorites.store', $product) }}">
                        @csrf
                        <button class="lego-btn lego-btn-secondary text-xs">♥ {{ __('messages.favorite') }}</button>
                    </form>
                </div>
            @endif
        </div>

        <div class="mt-4 relative z-20">
            <h3 class="font-semibold">{{ $product->name }}</h3>
            <div class="mt-2 flex items-center gap-2 text-sm">
                <span class="text-lg font-bold">{{ $product->price }} грн</span>
                @if ($isSale)
                    <span class="text-xs line-through text-[color:var(--muted)]">{{ $product->old_price }} грн</span>
                @endif
            </div>
            <div class="mt-1 flex items-center justify-between text-xs text-[color:var(--muted)]">
                <span>
                    @if ($product->stock <= 0)
                        {{ __('messages.out_of_stock') }}
                    @elseif ($isLowStock)
                        {{ __('messages.low_stock_left', ['count' => $product->stock]) }}
                    @else
                        {{ __('messages.in_stock') }}
                    @endif
                </span>
                <span>★ {{ $rating }} ({{ $product->reviews_count ?? 0 }})</span>
            </div>
        </div>
    </div>
</div>
