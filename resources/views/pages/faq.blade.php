<x-app-layout>
    <div class="mx-auto max-w-5xl px-4 py-10">
        <h1 class="text-4xl font-extrabold">{{ __('messages.faq') }}</h1>
        <div class="mt-6 space-y-4">
            <div class="lego-card p-5">
                <div class="font-semibold">{{ __('messages.faq_q1') }}</div>
                <p class="mt-2 text-sm text-[color:var(--muted)]">{{ __('messages.faq_a1') }}</p>
            </div>
            <div class="lego-card p-5">
                <div class="font-semibold">{{ __('messages.faq_q2') }}</div>
                <p class="mt-2 text-sm text-[color:var(--muted)]">{{ __('messages.faq_a2') }}</p>
            </div>
            <div class="lego-card p-5">
                <div class="font-semibold">{{ __('messages.faq_q3') }}</div>
                <p class="mt-2 text-sm text-[color:var(--muted)]">{{ __('messages.faq_a3') }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
