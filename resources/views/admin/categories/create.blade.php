<x-admin-layout>
    <x-slot name="breadcrumb">Admin / Categories / Create</x-slot>

    <h1 class="text-2xl font-bold">{{ __('messages.new') }} {{ __('messages.categories') }}</h1>
    <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-6 grid gap-4 lego-card p-6">
        @csrf
        <input name="name" class="lego-input" placeholder="Name">
        <input name="slug" class="lego-input" placeholder="Slug">
        <textarea name="description" class="lego-input" placeholder="Description"></textarea>
        <input name="sort_order" class="lego-input" placeholder="Sort order">
        <button class="lego-btn lego-btn-primary">{{ __('messages.save') }}</button>
    </form>
</x-admin-layout>
