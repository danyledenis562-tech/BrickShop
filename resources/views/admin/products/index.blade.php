<x-admin-layout>
    <x-slot name="breadcrumb">Admin / Products</x-slot>

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">{{ __('messages.products') }}</h1>
        <a href="{{ route('admin.products.create') }}" class="lego-btn lego-btn-primary text-xs">{{ __('messages.new') }}</a>
    </div>

    <form class="mt-4 grid gap-3 md:grid-cols-4">
        <input name="search" value="{{ request('search') }}" class="lego-input" placeholder="{{ __('messages.search') }}">
        <select name="category" class="lego-input">
            <option value="">{{ __('messages.all_categories') }}</option>
            @foreach ($categories as $category)
                <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
            @endforeach
        </select>
        <button class="lego-btn lego-btn-secondary text-xs">{{ __('messages.filter') }}</button>
    </form>

    <div class="mt-6 overflow-x-auto admin-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.categories') }}</th>
                    <th>{{ __('messages.price') }}</th>
                    <th>{{ __('messages.stock') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category?->name }}</td>
                        <td>{{ $product->price }}</td>
                        <td>{{ $product->stock }}</td>
                        <td class="text-right">
                            <a href="{{ route('admin.products.edit', $product) }}" class="admin-icon-btn" aria-label="{{ __('messages.edit') }}">
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

    <div class="mt-4">{{ $products->appends(request()->query())->links() }}</div>
</x-admin-layout>
