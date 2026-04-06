<x-admin-layout>
    <x-slot name="breadcrumb">{{ __('messages.admin') }} / {{ __('messages.orders') }} / #{{ $order->id }}</x-slot>

    <div class="lego-card p-6">
        <h1 class="text-2xl font-bold">{{ __('messages.orders') }} #{{ $order->id }}</h1>
        <p class="text-xs text-[color:var(--muted)]">{{ $order->created_at->format('d.m.Y H:i') }}</p>

        <div class="mt-6 rounded-2xl border border-[color:var(--border)] bg-[color:var(--card)] p-4">
            <div class="text-xs font-semibold uppercase tracking-wide text-[color:var(--muted)]">{{ __('messages.admin_order_customer_block') }}</div>
            <div class="mt-3 grid gap-3 text-sm md:grid-cols-2">
                <div>
                    <div class="text-xs text-[color:var(--muted)]">{{ __('messages.full_name') }}</div>
                    <div class="mt-0.5 font-semibold">{{ $order->full_name }}</div>
                </div>
                <div>
                    <div class="text-xs text-[color:var(--muted)]">{{ __('messages.phone') }}</div>
                    <div class="mt-0.5 font-semibold"><a href="tel:{{ $order->phone }}" class="text-[color:var(--lego-blue)] hover:underline">{{ $order->phone }}</a></div>
                </div>
                <div class="md:col-span-2">
                    <div class="text-xs text-[color:var(--muted)]">{{ __('messages.email') }}</div>
                    <div class="mt-0.5 font-semibold">{{ $order->customerEmail() ?? '—' }}</div>
                    @if ($order->user)
                        <div class="mt-1 text-xs text-[color:var(--muted)]">{{ __('messages.admin_order_registered_user') }}</div>
                    @else
                        <div class="mt-1 text-xs text-[color:var(--muted)]">{{ __('messages.admin_order_guest_checkout') }}</div>
                    @endif
                </div>
                <div class="md:col-span-2">
                    <div class="text-xs text-[color:var(--muted)]">{{ __('messages.delivery_method') }}</div>
                    <div class="mt-0.5 font-semibold">{{ $order->deliveryTypeLabel() }}</div>
                </div>
                <div>
                    <div class="text-xs text-[color:var(--muted)]">{{ __('messages.city') }}</div>
                    <div class="mt-0.5">{{ $order->city ?: '—' }}</div>
                </div>
                <div class="md:col-span-2">
                    <div class="text-xs text-[color:var(--muted)]">{{ __('messages.shipping_address') }}</div>
                    <div class="mt-0.5 whitespace-pre-wrap">{{ $order->address ?: '—' }}</div>
                </div>
                <div class="md:col-span-2">
                    <div class="text-xs text-[color:var(--muted)]">{{ __('messages.payment_type') }}</div>
                    <div class="mt-0.5 font-semibold">{{ __('messages.payment_'.$order->payment_type) }}</div>
                </div>
                @if ($order->note)
                    <div class="md:col-span-2">
                        <div class="text-xs text-[color:var(--muted)]">{{ __('messages.note') }}</div>
                        <div class="mt-0.5 whitespace-pre-wrap">{{ $order->note }}</div>
                    </div>
                @endif
                <div class="md:col-span-2">
                    <div class="text-xs text-[color:var(--muted)]">{{ __('messages.admin_order_call_policy') }}</div>
                    @if ($order->dont_call)
                        <div class="mt-2 inline-flex items-center gap-2 rounded-xl border border-amber-500/40 bg-amber-500/10 px-3 py-2 text-sm font-semibold text-amber-800 dark:text-amber-200">
                            <span aria-hidden="true">📵</span>
                            {{ __('messages.admin_do_not_call_badge') }}
                        </div>
                    @else
                        <div class="mt-2 inline-flex items-center gap-2 rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-sm font-semibold text-emerald-800 dark:text-emerald-200">
                            <span aria-hidden="true">📞</span>
                            {{ __('messages.admin_may_call_badge') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6">
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
                    <input type="text" name="tracking_number" class="lego-input mt-1 w-full font-mono" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="59001234567890" autocomplete="off">
                    <p class="mt-1 text-xs text-[color:var(--muted)]">{{ __('messages.admin_tracking_hint') }}</p>
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
