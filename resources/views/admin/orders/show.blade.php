<x-admin-layout>
    <x-slot name="breadcrumb">Admin / Orders / #{{ $order->id }}</x-slot>

    <div class="lego-card p-6">
        <h1 class="text-2xl font-bold">{{ __('messages.orders') }} #{{ $order->id }}</h1>
        <p class="text-sm text-[color:var(--muted)]">{{ $order->user?->email }}</p>

        <div class="mt-4">
            <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="flex gap-2">
                @csrf
                @method('PUT')
                <select name="status" class="lego-input">
                    @foreach (['new','paid','processing','shipped','canceled'] as $status)
                        <option value="{{ $status }}" @selected($order->status === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <button class="lego-btn lego-btn-primary text-xs">{{ __('messages.update') }}</button>
            </form>
        </div>

        <div class="mt-6 space-y-2 text-sm">
            @foreach ($order->items as $item)
                <div class="flex justify-between">
                    <span>{{ $item->product?->name }} × {{ $item->quantity }}</span>
                    <span>{{ number_format($item->total, 2) }}</span>
                </div>
            @endforeach
        </div>
        <div class="mt-4 font-semibold">{{ __('messages.total') }}: {{ number_format($order->total, 2) }} грн</div>
    </div>
</x-admin-layout>
