<x-admin-layout>
    <x-slot name="breadcrumb">Admin / Products / Create</x-slot>

    <h1 class="text-2xl font-bold">{{ __('messages.new') }} {{ __('messages.products') }}</h1>
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="mt-6 grid gap-4 lego-card p-6">
        @csrf
        <input name="name" class="lego-input" placeholder="{{ __('messages.name') }}">
        <input name="slug" class="lego-input" placeholder="Slug">
        <select name="category_id" class="lego-input">
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        <input name="price" class="lego-input" placeholder="{{ __('messages.price') }}">
        <input name="old_price" class="lego-input" placeholder="{{ __('messages.old_price') }}">
        <input name="stock" class="lego-input" placeholder="{{ __('messages.stock') }}">
        <input name="age" class="lego-input" placeholder="{{ __('messages.age') }}">
        <input name="difficulty" class="lego-input" placeholder="{{ __('messages.difficulty') }}">
        <input name="pieces" class="lego-input" placeholder="{{ __('messages.pieces') }}">
        <input name="brand" class="lego-input" placeholder="{{ __('messages.brand') }}">
        <input name="series" class="lego-input" placeholder="{{ __('messages.series') }}">
        <input name="country" class="lego-input" placeholder="{{ __('messages.country') }}">
        <textarea name="description" class="lego-input" placeholder="{{ __('messages.description') }}"></textarea>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_featured" value="1">
            Featured
        </label>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" checked>
            Active
        </label>
        <input type="file" name="images[]" multiple class="lego-input">
        <button class="lego-btn lego-btn-primary">{{ __('messages.save') }}</button>
    </form>
</x-admin-layout>
