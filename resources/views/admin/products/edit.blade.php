<x-admin-layout>
    <x-slot name="breadcrumb">Admin / Products / Edit</x-slot>

    <h1 class="text-2xl font-bold">{{ __('messages.edit') }} {{ __('messages.products') }}</h1>
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="mt-6 grid gap-4 lego-card p-6">
        @csrf
        @method('PUT')
        <input name="name" class="lego-input" placeholder="{{ __('messages.name') }}" value="{{ $product->name }}">
        <input name="slug" class="lego-input" placeholder="Slug" value="{{ $product->slug }}">
        <select name="category_id" class="lego-input">
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected($category->id === $product->category_id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <input name="price" class="lego-input" placeholder="{{ __('messages.price') }}" value="{{ $product->price }}">
        <input name="old_price" class="lego-input" placeholder="{{ __('messages.old_price') }}" value="{{ $product->old_price }}">
        <input name="stock" class="lego-input" placeholder="{{ __('messages.stock') }}" value="{{ $product->stock }}">
        <input name="age" class="lego-input" placeholder="{{ __('messages.age') }}" value="{{ $product->age }}">
        <input name="difficulty" class="lego-input" placeholder="{{ __('messages.difficulty') }}" value="{{ $product->difficulty }}">
        <input name="pieces" class="lego-input" placeholder="{{ __('messages.pieces') }}" value="{{ $product->pieces }}">
        <input name="brand" class="lego-input" placeholder="{{ __('messages.brand') }}" value="{{ $product->brand }}">
        <input name="series" class="lego-input" placeholder="{{ __('messages.series') }}" value="{{ $product->series }}">
        <input name="country" class="lego-input" placeholder="{{ __('messages.country') }}" value="{{ $product->country }}">
        <textarea name="description" class="lego-input" placeholder="{{ __('messages.description') }}">{{ $product->description }}</textarea>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_featured" value="1" @checked($product->is_featured)>
            Featured
        </label>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" @checked($product->is_active)>
            Active
        </label>
        <input type="file" name="images[]" multiple class="lego-input">
        <button class="lego-btn lego-btn-primary">{{ __('messages.update') }}</button>
    </form>

    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="mt-4" data-confirm>
        @csrf
        @method('DELETE')
        <button class="lego-btn lego-btn-secondary">{{ __('messages.delete') }}</button>
    </form>
</x-admin-layout>
