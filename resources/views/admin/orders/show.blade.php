<x-admin-layout>
    <x-slot name="breadcrumb">{{ __('messages.admin') }} / {{ __('messages.orders') }} / #{{ $order->id }}</x-slot>

    <div class="lego-card p-6">
        <h1 class="text-2xl font-bold">{{ __('messages.orders') }} #{{ $order->id }}</h1>
        <p class="text-sm text-[color:var(--muted)]">{{ $order->user?->email }}</p>

        <div class="mt-4">
            <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="grid gap-3 md:grid-cols-2">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-xs font-semibold text-[color:var(--muted)]">{{ __('messages.status') }}</label>
                    <select name="status" class="lego-input mt-1 w-full">
                        @foreach (['new','paid','processing','shipped','canceled'] as $status)
                            <option value="{{ $status }}" @selected($order->status->value === $status)>{{ __('messages.order_status_'.$status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-[color:var(--muted)]">{{ __('messages.tracking_number') }}</label>
                    <input type="text" name="tracking_number" class="lego-input mt-1 w-full" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="TTN">
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-[color:var(--muted)]">{{ __('messages.tracking_url') }}</label>
                    <input type="text" name="tracking_url" class="lego-input mt-1 w-full" value="{{ old('tracking_url', $order->tracking_url) }}" placeholder="https://">
                </div>
                <div class="md:col-span-2">
                    <button class="lego-btn lego-btn-primary text-xs">{{ __('messages.update') }}</button>
                </div>
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
