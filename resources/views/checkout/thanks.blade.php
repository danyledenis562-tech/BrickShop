<x-app-layout>
    <div class="mx-auto max-w-4xl px-4 py-10">
        <div class="lego-card p-6">
            <h1 class="text-3xl font-bold">{{ __('messages.thanks') }}</h1>
            <p class="mt-2 text-sm text-[color:var(--muted)]">{{ __('messages.order_received') }} #{{ $order->id }}</p>
            <p class="mt-1 text-sm font-medium">
                {{ __('messages.order_status_label') }}:
                @if ($order->status === \App\Enums\OrderStatus::Paid)
                    <span class="text-green-600">{{ __('messages.order_status_paid') }}</span>
                @else
                    <span class="text-amber-600">{{ __('messages.order_status_new') }}</span>
                @endif
            </p>
            <div class="mt-4 space-y-2">
                @foreach ($order->items as $item)
                    <div class="flex justify-between text-sm">
                        <span>{{ $item->product->name }} × {{ $item->quantity }}</span>
                        <span>{{ number_format($item->total, 2) }} грн</span>
                    </div>
                @endforeach
            </div>
            @if($order->discount_amount > 0)
                <div class="mt-2 text-sm text-[color:var(--muted)]">{{ __('messages.discount') }}: −{{ number_format($order->discount_amount, 2) }} грн</div>
            @endif
            <div class="mt-4 text-lg font-bold">{{ __('messages.total') }}: {{ number_format($order->total, 2) }} грн</div>
            @if ($order->tracking_number)
                <div class="mt-4 rounded-xl border border-[color:var(--border)] p-4 text-sm">
                    <div class="font-semibold">{{ __('messages.tracking_title') }}</div>
                    @if ($order->tracking_url)
                        <a href="{{ $order->tracking_url }}" target="_blank" rel="noopener" class="mt-2 inline-block font-mono text-[color:var(--lego-blue)] hover:underline">{{ $order->tracking_number }}</a>
                    @else
                        <div class="mt-2 font-mono">{{ $order->tracking_number }}</div>
                    @endif
                </div>
            @endif
            <a href="{{ route('catalog') }}" class="mt-4 inline-block lego-btn lego-btn-primary">{{ __('messages.back_to_catalog') }}</a>
        </div>
    </div>
</x-app-layout>
