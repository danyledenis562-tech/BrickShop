<x-admin-layout>
    <x-slot name="breadcrumb">Admin / Reviews</x-slot>

    <h1 class="text-2xl font-bold">{{ __('messages.reviews') }}</h1>
    <form class="mt-4 flex gap-2">
        <select name="approved" class="lego-input">
            <option value="">{{ __('messages.filter') }}</option>
            <option value="1" @selected(request('approved') === '1')>{{ __('messages.approved') }}</option>
            <option value="0" @selected(request('approved') === '0')>{{ __('messages.pending') }}</option>
        </select>
        <button class="lego-btn lego-btn-secondary text-xs">{{ __('messages.filter') }}</button>
    </form>

    <div class="mt-6 overflow-x-auto admin-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>{{ __('messages.products') }}</th>
                    <th>{{ __('messages.users') }}</th>
                    <th>{{ __('messages.rating') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reviews as $review)
                    <tr>
                        <td>{{ $review->product?->name }}</td>
                        <td>{{ $review->user?->email }}</td>
                        <td>{{ $review->rating }}</td>
                        <td>
                            <span class="admin-badge {{ $review->approved ? 'badge-paid' : 'badge-processing' }}">
                                {{ $review->approved ? __('messages.approved') : __('messages.pending') }}
                            </span>
                        </td>
                        <td class="text-right flex items-center gap-2 justify-end">
                            <form method="POST" action="{{ route('admin.reviews.update', $review) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="approved" value="{{ $review->approved ? 0 : 1 }}">
                                <button class="admin-icon-btn" aria-label="{{ $review->approved ? __('messages.unapprove') : __('messages.approve') }}">
                                    @if ($review->approved)
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M18 6L6 18M6 6l12 12"/>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 6L9 17l-5-5"/>
                                        </svg>
                                    @endif
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" data-confirm>
                                @csrf
                                @method('DELETE')
                                <button class="admin-icon-btn" aria-label="{{ __('messages.delete') }}">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 6h18M8 6v12m8-12v12M5 6l1 14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-14"/>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $reviews->appends(request()->query())->links() }}</div>
</x-admin-layout>
