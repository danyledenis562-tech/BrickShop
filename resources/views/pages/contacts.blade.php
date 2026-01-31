@php
    $setting = \App\Models\Setting::query()->first();
@endphp

<x-app-layout>
    <div class="mx-auto max-w-5xl px-4 py-10">
        <h1 class="text-4xl font-extrabold">{{ __('messages.contacts') }}</h1>
        <div class="mt-6 grid gap-4 md:grid-cols-2">
            <div class="lego-card p-5 text-sm">
                <div class="font-semibold">{{ __('messages.contacts_support') }}</div>
                <div class="mt-2 text-[color:var(--muted)]">{{ __('messages.contacts_hours') }}</div>
                <div class="mt-2 font-semibold text-[color:var(--lego-red)]">{{ __('messages.support_phone') }}: {{ $setting?->phone_support ?? '+380 00 000 00 00' }}</div>
                <div class="mt-2">
                    <a href="{{ $setting?->telegram_support_url ?? 'https://t.me/' }}" target="_blank" rel="noopener" class="text-sm font-semibold text-[color:var(--lego-red)]">Telegram</a>
                </div>
            </div>
            <div class="lego-card p-5 text-sm">
                <div class="font-semibold">{{ __('messages.contacts_office') }}</div>
                <div class="mt-2 text-[color:var(--muted)]">{{ __('messages.contacts_address') }}</div>
                <div class="mt-2 text-[color:var(--muted)]">{{ __('messages.contacts_email') }}: hello@brickshop.test</div>
            </div>
        </div>
    </div>
</x-app-layout>
