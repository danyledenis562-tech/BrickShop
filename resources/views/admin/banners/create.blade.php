<x-admin-layout>
    <x-slot name="breadcrumb">Admin / Banners / Create</x-slot>

    <h1 class="text-2xl font-bold">{{ __('messages.banner_new') }}</h1>

    <form method="POST" action="{{ route('admin.banners.store') }}" class="mt-6 grid gap-4 max-w-2xl">
        @csrf
        @include('admin.banners.form')
    </form>
</x-admin-layout>
