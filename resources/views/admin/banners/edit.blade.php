<x-admin-layout>
    <x-slot name="breadcrumb">{{ __('messages.admin') }} / {{ __('messages.banners') }} / {{ __('messages.edit') }}</x-slot>

    <h1 class="text-2xl font-bold">{{ __('messages.banner_edit') }}</h1>

    <form method="POST" action="{{ route('admin.banners.update', $banner) }}" class="mt-6 grid gap-4 max-w-2xl">
        @csrf
        @method('PUT')
        @include('admin.banners.form', ['banner' => $banner])
    </form>

    <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" class="mt-4">
        @csrf
        @method('DELETE')
        <button class="lego-btn lego-btn-secondary text-xs">{{ __('messages.delete') }}</button>
    </form>
</x-admin-layout>
