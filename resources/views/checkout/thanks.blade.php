<x-app-layout>
    <div class="mx-auto max-w-4xl px-4 py-10">
        <div class="lego-card p-6">
            <h1 class="text-3xl font-bold">{{ __('messages.thanks') }}</h1>
            <p class="mt-2 text-sm text-[color:var(--muted)]">{{ __('messages.order_received') }} #{{ $order->id }}</p>
            <div class="mt-4 space-y-2">
                @foreach ($order->items as $item)
                    <div class="flex justify-between text-sm">
                        <span>{{ $item->product->name }} × {{ $item->quantity }}</span>
                        <span>{{ number_format($item->total, 2) }} грн</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 text-lg font-bold">{{ __('messages.total') }}: {{ number_format($order->total, 2) }} грн</div>
            <a href="{{ route('catalog') }}" class="mt-4 inline-block lego-btn lego-btn-primary">{{ __('messages.back_to_catalog') }}</a>
        </div>
    </div>
</x-app-layout>
