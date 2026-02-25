<x-admin-layout>
    <x-slot name="breadcrumb">Admin / {{ __('messages.promo_codes') }}</x-slot>

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">{{ __('messages.promo_codes') }}</h1>
        <a href="{{ route('admin.promo-codes.create') }}" class="lego-btn lego-btn-primary text-xs">{{ __('messages.new') }}</a>
    </div>

    <div class="mt-6 overflow-x-auto admin-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>{{ __('messages.promo_code') }}</th>
                    <th>{{ __('messages.promo_type') }}</th>
                    <th>{{ __('messages.value') }}</th>
                    <th>{{ __('messages.valid_from') }}</th>
                    <th>{{ __('messages.valid_until') }}</th>
                    <th>{{ __('messages.times_used') }} / {{ __('messages.usage_limit') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($promoCodes as $promo)
                    <tr>
                        <td><code class="font-semibold">{{ $promo->code }}</code></td>
                        <td>{{ $promo->type === 'percent' ? __('messages.promo_type_percent') : __('messages.promo_type_fixed') }}</td>
                        <td>{{ $promo->type === 'percent' ? $promo->value . '%' : number_format($promo->value, 2) }}</td>
                        <td>{{ $promo->valid_from?->format('Y-m-d') ?? '—' }}</td>
                        <td>{{ $promo->valid_until?->format('Y-m-d') ?? '—' }}</td>
                        <td>{{ $promo->times_used }} / {{ $promo->usage_limit ?? '∞' }}</td>
                        <td>
                            <span class="admin-badge {{ $promo->is_active ? 'badge-paid' : 'badge-processing' }}">
                                {{ $promo->is_active ? __('messages.active') : __('messages.inactive') }}
                            </span>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('admin.promo-codes.edit', $promo) }}" class="admin-icon-btn" aria-label="{{ __('messages.edit') }}">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 20h9"/>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $promoCodes->appends(request()->query())->links() }}</div>
</x-admin-layout>
