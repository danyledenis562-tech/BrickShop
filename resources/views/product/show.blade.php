<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-10">
        @php
            $isNew = $product->created_at?->gt(now()->subDays(14));
            $isTop = $product->is_featured || $product->popularity >= 250;
            $isSale = $product->old_price && $product->old_price > $product->price;
        @endphp

        <div class="grid gap-8 lg:grid-cols-2">
            <div class="lego-card p-4 lego-zoom">
                <div class="relative lego-brick h-80 bg-[color:var(--lego-yellow)]">
                    <div class="absolute left-4 top-4 flex gap-2">
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
                    <x-product-image :path="$product->mainImage?->path" :alt="$product->name" class="h-full w-full object-cover" />
                </div>
                <div class="mt-4 grid grid-cols-3 gap-2">
                    @foreach ($product->images as $image)
                        <x-product-image :path="$image->path" :alt="$product->name" class="h-20 w-full rounded-lg object-cover" />
                    @endforeach
                </div>
            </div>

            <div class="lego-card p-6">
                <h1 class="text-3xl font-bold">{{ $product->name }}</h1>
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

                <div class="mt-4 flex items-center gap-3">
                    <form method="POST" action="{{ route('cart.add', $product) }}">
                        @csrf
                        <input type="hidden" name="redirect" value="checkout">
                        <button class="lego-btn lego-btn-primary">{{ __('messages.buy_now') }}</button>
                    </form>
                    <form method="POST" action="{{ route('favorites.store', $product) }}">
                        @csrf
                        <button class="lego-btn lego-btn-secondary">♥ {{ __('messages.favorite') }}</button>
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
