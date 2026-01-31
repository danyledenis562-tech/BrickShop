<x-admin-layout>
    <x-slot name="breadcrumb">Admin / Settings</x-slot>

    <h1 class="text-2xl font-bold">{{ __('messages.settings') }}</h1>
    <form method="POST" action="{{ route('admin.settings.update') }}" class="mt-6 lego-card p-6 space-y-4">
        @csrf
        @method('PUT')
        <input name="phone_support" class="lego-input" placeholder="{{ __('messages.support_phone') }}" value="{{ old('phone_support', $setting->phone_support) }}">
        <input name="telegram_support_url" class="lego-input" placeholder="{{ __('messages.support_telegram') }}" value="{{ old('telegram_support_url', $setting->telegram_support_url) }}">
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="show_support_widget" value="1" @checked(old('show_support_widget', $setting->show_support_widget))>
            {{ __('messages.support_widget') }}
        </label>
        <button class="lego-btn lego-btn-primary">{{ __('messages.save') }}</button>
    </form>
</x-admin-layout>
