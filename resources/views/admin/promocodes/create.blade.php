<x-admin-layout>
    <x-slot name="breadcrumb">{{ __('messages.admin') }} / {{ __('messages.promo_codes') }} / {{ __('messages.new') }}</x-slot>

    <h1 class="text-2xl font-bold">{{ __('messages.promo_code_new') }}</h1>

    <form method="POST" action="{{ route('admin.promo-codes.store') }}" class="mt-6 grid gap-4 max-w-2xl">
        @csrf
        @include('admin.promocodes.form')
    </form>
</x-admin-layout>
