<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-10">
        <h1 class="text-3xl font-bold">{{ __('messages.favorites') }}</h1>
        <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($favorites as $product)
                <div class="flex flex-col gap-3">
                    <x-product-card :product="$product" :show-actions="false" />
                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('cart.add', $product) }}">
                            @csrf
                            <button class="lego-btn lego-btn-primary text-xs">{{ __('messages.add_to_cart') }}</button>
                        </form>
                        <form method="POST" action="{{ route('favorites.destroy', $product) }}">
                            @csrf
                            @method('DELETE')
                            <button class="lego-btn lego-btn-secondary text-xs">{{ __('messages.remove') }}</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-sm text-[color:var(--muted)]">{{ __('messages.no_favorites') }}</p>
            @endforelse
        </div>
        <div class="mt-6">{{ $favorites->appends(request()->query())->links() }}</div>
    </div>
</x-app-layout>
