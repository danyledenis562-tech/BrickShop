<x-app-layout>
    <div class="mx-auto max-w-5xl px-4 py-10">
        <h1 class="text-4xl font-extrabold">{{ __('messages.about') }}</h1>
        <p class="mt-4 text-[color:var(--muted)]">{{ __('messages.about_text_1') }}</p>
        <p class="mt-3 text-[color:var(--muted)]">{{ __('messages.about_text_2') }}</p>
        <div class="mt-6 grid gap-4 md:grid-cols-3">
            <div class="lego-card p-5">
                <div class="text-sm font-semibold">{{ __('messages.about_point_1') }}</div>
            </div>
            <div class="lego-card p-5">
                <div class="text-sm font-semibold">{{ __('messages.about_point_2') }}</div>
            </div>
            <div class="lego-card p-5">
                <div class="text-sm font-semibold">{{ __('messages.about_point_3') }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
