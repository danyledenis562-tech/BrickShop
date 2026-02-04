<x-app-layout>
    <div class="mx-auto max-w-5xl px-4 py-10">
        <div class="info-hero">
            <h1 class="text-4xl font-extrabold">{{ __('messages.returns') }}</h1>
            <p class="text-[color:var(--muted)]">{{ __('messages.returns_text_1') }}</p>
        </div>

        <div class="mt-8 info-grid">
            <div class="lego-card info-card">
                <div class="info-icon">🧾</div>
                <div class="info-section-title">{{ __('messages.returns_terms') }}</div>
                <p class="text-sm text-[color:var(--muted)]">{{ __('messages.returns_text_2') }}</p>
            </div>
            <div class="lego-card info-card">
                <div class="info-icon">📦</div>
                <div class="info-section-title">{{ __('messages.returns_condition') }}</div>
                <p class="text-sm text-[color:var(--muted)]">{{ __('messages.returns_text_3') }}</p>
            </div>
            <div class="lego-card info-card">
                <div class="info-icon">🔁</div>
                <div class="info-section-title">{{ __('messages.returns_exchange') }}</div>
                <p class="text-sm text-[color:var(--muted)]">{{ __('messages.returns_exchange_desc') }}</p>
            </div>
        </div>

        <section class="mt-10 lego-card p-6">
            <h2 class="text-xl font-bold">{{ __('messages.faq') }}</h2>
            <div class="mt-4 info-faq">
                <div class="info-card">
                    <div class="info-section-title">{{ __('messages.returns_faq_q1') }}</div>
                    <p class="text-sm text-[color:var(--muted)]">{{ __('messages.returns_faq_a1') }}</p>
                </div>
                <div class="info-card">
                    <div class="info-section-title">{{ __('messages.returns_faq_q2') }}</div>
                    <p class="text-sm text-[color:var(--muted)]">{{ __('messages.returns_faq_a2') }}</p>
                </div>
            </div>
        </section>

        <section class="mt-10 lego-card p-6">
            <h2 class="text-xl font-bold">{{ __('messages.trust_title') }}</h2>
            <div class="mt-4 trust-grid">
                <div class="info-card">
                    <div class="info-section-title">{{ __('messages.trust_item_4_title') }}</div>
                    <p class="text-sm text-[color:var(--muted)]">{{ __('messages.trust_item_4_desc') }}</p>
                </div>
                <div class="info-card">
                    <div class="info-section-title">{{ __('messages.trust_item_5_title') }}</div>
                    <p class="text-sm text-[color:var(--muted)]">{{ __('messages.trust_item_5_desc') }}</p>
                </div>
                <div class="info-card">
                    <div class="info-section-title">{{ __('messages.trust_item_6_title') }}</div>
                    <p class="text-sm text-[color:var(--muted)]">{{ __('messages.trust_item_6_desc') }}</p>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
