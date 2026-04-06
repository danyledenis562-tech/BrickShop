<x-admin-layout>
    <x-slot name="breadcrumb">{{ __('messages.admin') }} / {{ __('messages.promo_codes') }} / {{ __('messages.edit') }}</x-slot>

    <h1 class="text-2xl font-bold">{{ __('messages.promo_code_edit') }}</h1>

    <form method="POST" action="{{ route('admin.promo-codes.update', $promoCode) }}" class="mt-6 grid gap-4 max-w-2xl">
        @csrf
        @method('PUT')
        @include('admin.promocodes.form', ['promoCode' => $promoCode])
    </form>

    <form method="POST" action="{{ route('admin.promo-codes.destroy', $promoCode) }}" class="mt-4">
        @csrf
        @method('DELETE')
        <button class="lego-btn lego-btn-secondary text-xs">{{ __('messages.delete') }}</button>
    </form>
</x-admin-layout>
