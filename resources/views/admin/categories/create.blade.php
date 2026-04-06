<x-admin-layout>
    <x-slot name="breadcrumb">{{ __('messages.admin') }} / {{ __('messages.categories') }} / {{ __('messages.new') }}</x-slot>

    <h1 class="text-2xl font-bold">{{ __('messages.new') }} {{ __('messages.categories') }}</h1>
    <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-6 grid gap-4 lego-card p-6">
        @csrf
        <input name="name" class="lego-input" placeholder="{{ __('messages.name') }}">
        <input name="slug" class="lego-input" placeholder="{{ __('messages.slug') }}">
        <textarea name="description" class="lego-input" placeholder="{{ __('messages.description') }}"></textarea>
        <input name="sort_order" class="lego-input" placeholder="{{ __('messages.sort_order') }}">
        <button class="lego-btn lego-btn-primary">{{ __('messages.save') }}</button>
    </form>
</x-admin-layout>
