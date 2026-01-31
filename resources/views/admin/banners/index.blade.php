<x-admin-layout>
    <x-slot name="breadcrumb">Admin / Banners</x-slot>

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">{{ __('messages.banners') }}</h1>
        <a href="{{ route('admin.banners.create') }}" class="lego-btn lego-btn-primary text-xs">{{ __('messages.new') }}</a>
    </div>

    <div class="mt-6 overflow-x-auto admin-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>{{ __('messages.title') }}</th>
                    <th>{{ __('messages.position') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($banners as $banner)
                    <tr>
                        <td>{{ $banner->title }}</td>
                        <td>{{ $banner->position }}</td>
                        <td>
                            <span class="admin-badge {{ $banner->is_active ? 'badge-paid' : 'badge-processing' }}">
                                {{ $banner->is_active ? __('messages.active') : __('messages.inactive') }}
                            </span>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('admin.banners.edit', $banner) }}" class="admin-icon-btn" aria-label="{{ __('messages.edit') }}">
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

    <div class="mt-4">{{ $banners->appends(request()->query())->links() }}</div>
</x-admin-layout>
