<x-admin-layout>
    <x-slot name="breadcrumb">{{ __('messages.admin') }} / {{ __('messages.categories') }} / {{ __('messages.edit') }}</x-slot>

    <h1 class="text-2xl font-bold">{{ __('messages.edit') }} {{ __('messages.categories') }}</h1>
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="mt-6 grid gap-4 lego-card p-6">
        @csrf
        @method('PUT')
        <input name="name" class="lego-input" placeholder="{{ __('messages.name') }}" value="{{ $category->name }}">
        <input name="slug" class="lego-input" placeholder="{{ __('messages.slug') }}" value="{{ $category->slug }}">
        <textarea name="description" class="lego-input" placeholder="{{ __('messages.description') }}">{{ $category->description }}</textarea>
        <input name="sort_order" class="lego-input" placeholder="{{ __('messages.sort_order') }}" value="{{ $category->sort_order }}">
        <button class="lego-btn lego-btn-primary">{{ __('messages.update') }}</button>
    </form>

    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="mt-4">
        @csrf
        @method('DELETE')
        <button class="lego-btn lego-btn-secondary">{{ __('messages.delete') }}</button>
    </form>
</x-admin-layout>
