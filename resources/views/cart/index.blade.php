<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-10">
        <h1 class="text-3xl font-bold">{{ __('messages.cart') }}</h1>

        @if (empty($cart))
            <p class="mt-4 text-sm text-[color:var(--muted)]">{{ __('messages.cart_empty') }}</p>
        @else
            <div class="mt-6 space-y-4">
                @foreach ($cart as $item)
                    <div class="lego-card flex flex-col gap-4 p-4 md:flex-row md:items-center md:justify-between">
                        <div class="flex items-center gap-4">
                            <div class="h-20 w-24 overflow-hidden rounded-xl bg-[color:var(--lego-yellow)]">
                                <x-product-image :path="$item['image']" :alt="$item['name']" class="h-full w-full object-cover" />
                            </div>
                            <div>
                                <div class="font-semibold">{{ $item['name'] }}</div>
                                <div class="text-sm text-[color:var(--muted)]">{{ $item['price'] }} грн</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <form method="POST" action="{{ route('cart.update', $item['slug']) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="quantity" min="1" max="99" value="{{ $item['quantity'] }}" class="lego-input w-20">
                                <button class="lego-btn lego-btn-secondary text-xs">{{ __('messages.update') }}</button>
                            </form>
                            <form method="POST" action="{{ route('cart.remove', $item['slug']) }}">
                                @csrf
                                @method('DELETE')
                                <button class="lego-btn lego-btn-primary text-xs">{{ __('messages.remove') }}</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 lego-card p-6">
                <div class="text-xl font-bold">{{ __('messages.total') }}: {{ number_format($total, 2) }} грн</div>
                <div class="mt-4">
                    @auth
                        <a href="{{ route('checkout.index') }}" class="lego-btn lego-btn-primary">{{ __('messages.checkout') }}</a>
                    @else
                        <div class="lego-alert">
                            {{ __('messages.checkout_login_required') }}
                        </div>
                        <a href="{{ route('login') }}" class="mt-3 inline-block lego-btn lego-btn-secondary">{{ __('messages.login_to_checkout') }}</a>
                    @endauth
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
