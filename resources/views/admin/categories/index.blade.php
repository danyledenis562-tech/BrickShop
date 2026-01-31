<x-admin-layout>
    <x-slot name="breadcrumb">Admin / Categories</x-slot>

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">{{ __('messages.categories') }}</h1>
        <a href="{{ route('admin.categories.create') }}" class="lego-btn lego-btn-primary text-xs">{{ __('messages.new') }}</a>
    </div>

    <div class="mt-6 overflow-x-auto admin-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>Slug</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->slug }}</td>
                        <td class="text-right">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="admin-icon-btn" aria-label="{{ __('messages.edit') }}">
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

    <div class="mt-4">{{ $categories->appends(request()->query())->links() }}</div>
</x-admin-layout>
