<x-app-layout>
    <div class="mx-auto max-w-5xl px-4 py-10">
        <div class="info-hero">
            <h1 class="text-4xl font-extrabold">{{ __('messages.shipping') }}</h1>
            <p class="text-[color:var(--muted)]">{{ __('messages.shipping_text_1') }}</p>
        </div>

        <div class="mt-8 info-grid">
            <div class="lego-card info-card">
                <div class="info-icon">🚚</div>
                <div class="info-section-title">{{ __('messages.shipping_methods') }}</div>
                <p class="text-sm text-[color:var(--muted)]">{{ __('messages.shipping_text_2') }}</p>
            </div>
            <div class="lego-card info-card">
                <div class="info-icon">⏱️</div>
                <div class="info-section-title">{{ __('messages.shipping_timeline') }}</div>
                <p class="text-sm text-[color:var(--muted)]">{{ __('messages.shipping_text_3') }}</p>
            </div>
            <div class="lego-card info-card">
                <div class="info-icon">📍</div>
                <div class="info-section-title">{{ __('messages.shipping_tracking') }}</div>
                <p class="text-sm text-[color:var(--muted)]">{{ __('messages.shipping_tracking_desc') }}</p>
            </div>
        </div>

        <section class="mt-10 lego-card p-6">
            <h2 class="text-xl font-bold">{{ __('messages.faq') }}</h2>
            <div class="mt-4 info-faq">
                <div class="info-card">
                    <div class="info-section-title">{{ __('messages.shipping_faq_q1') }}</div>
                    <p class="text-sm text-[color:var(--muted)]">{{ __('messages.shipping_faq_a1') }}</p>
                </div>
                <div class="info-card">
                    <div class="info-section-title">{{ __('messages.shipping_faq_q2') }}</div>
                    <p class="text-sm text-[color:var(--muted)]">{{ __('messages.shipping_faq_a2') }}</p>
                </div>
            </div>
        </section>

        <section class="mt-10 lego-card p-6">
            <h2 class="text-xl font-bold">{{ __('messages.trust_title') }}</h2>
            <div class="mt-4 trust-grid">
                <div class="info-card">
                    <div class="info-section-title">{{ __('messages.trust_item_1_title') }}</div>
                    <p class="text-sm text-[color:var(--muted)]">{{ __('messages.trust_item_1_desc') }}</p>
                </div>
                <div class="info-card">
                    <div class="info-section-title">{{ __('messages.trust_item_2_title') }}</div>
                    <p class="text-sm text-[color:var(--muted)]">{{ __('messages.trust_item_2_desc') }}</p>
                </div>
                <div class="info-card">
                    <div class="info-section-title">{{ __('messages.trust_item_3_title') }}</div>
                    <p class="text-sm text-[color:var(--muted)]">{{ __('messages.trust_item_3_desc') }}</p>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
