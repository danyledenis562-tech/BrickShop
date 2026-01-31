<x-admin-layout>
    <x-slot name="breadcrumb">Admin / Orders</x-slot>

    <h1 class="text-2xl font-bold">{{ __('messages.orders') }}</h1>
    <form class="mt-4 grid gap-3 md:grid-cols-4">
        <input name="user" value="{{ request('user') }}" class="lego-input" placeholder="{{ __('messages.email') }}">
        <select name="status" class="lego-input">
            <option value="">{{ __('messages.status') }}</option>
            @foreach (['new','paid','processing','shipped','canceled'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
            @endforeach
        </select>
        <input name="date" value="{{ request('date') }}" type="date" class="lego-input">
        <button class="lego-btn lego-btn-secondary text-xs">{{ __('messages.filter') }}</button>
    </form>

    <div class="mt-6 overflow-x-auto admin-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('messages.users') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.total') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    @php
                        $badgeMap = [
                            'new' => 'badge-new',
                            'paid' => 'badge-paid',
                            'processing' => 'badge-processing',
                            'shipped' => 'badge-shipped',
                        ];
                        $badgeClass = $badgeMap[$order->status] ?? 'badge-processing';
                    @endphp
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->user?->email }}</td>
                        <td><span class="admin-badge {{ $badgeClass }}">{{ $order->status }}</span></td>
                        <td>{{ number_format($order->total, 2) }}</td>
                        <td class="text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="admin-icon-btn" aria-label="{{ __('messages.view') }}">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $orders->appends(request()->query())->links() }}</div>
</x-admin-layout>
