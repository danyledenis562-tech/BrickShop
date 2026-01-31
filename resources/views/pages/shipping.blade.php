<x-app-layout>
    <div class="mx-auto max-w-5xl px-4 py-10">
        <div class="info-hero">
            <h1 class="text-4xl font-extrabold">{{ __('messages.shipping') }}</h1>
            <p class="text-[color:var(--muted)]">{{ __('messages.shipping_text_1') }}</p>
        </div>

        <div class="mt-8 info-grid">
            <div class="lego-card info-card">
                <div class="info-icon">🚚</div>
                <div class="info-section-title">Способи доставки</div>
                <p class="text-sm text-[color:var(--muted)]">{{ __('messages.shipping_text_2') }}</p>
            </div>
            <div class="lego-card info-card">
                <div class="info-icon">⏱️</div>
                <div class="info-section-title">Терміни</div>
                <p class="text-sm text-[color:var(--muted)]">{{ __('messages.shipping_text_3') }}</p>
            </div>
            <div class="lego-card info-card">
                <div class="info-icon">📍</div>
                <div class="info-section-title">Відстеження</div>
                <p class="text-sm text-[color:var(--muted)]">Після відправлення ви отримаєте номер ТТН або повідомлення у профілі.</p>
            </div>
        </div>

        <section class="mt-10 lego-card p-6">
            <h2 class="text-xl font-bold">FAQ</h2>
            <div class="mt-4 info-faq">
                <div class="info-card">
                    <div class="info-section-title">Скільки коштує доставка?</div>
                    <p class="text-sm text-[color:var(--muted)]">Вартість залежить від служби доставки та ваги набору.</p>
                </div>
                <div class="info-card">
                    <div class="info-section-title">Чи можна змінити адресу?</div>
                    <p class="text-sm text-[color:var(--muted)]">Так, до моменту відправлення — через підтримку або в профілі.</p>
                </div>
            </div>
        </section>

        <section class="mt-10 lego-card p-6">
            <h2 class="text-xl font-bold">Чому нам довіряють</h2>
            <div class="mt-4 trust-grid">
                <div class="info-card">
                    <div class="info-section-title">Оригінальні LEGO</div>
                    <p class="text-sm text-[color:var(--muted)]">Працюємо з офіційними постачальниками.</p>
                </div>
                <div class="info-card">
                    <div class="info-section-title">Гарантія якості</div>
                    <p class="text-sm text-[color:var(--muted)]">Перевіряємо комплектацію перед відправкою.</p>
                </div>
                <div class="info-card">
                    <div class="info-section-title">Підтримка 7/7</div>
                    <p class="text-sm text-[color:var(--muted)]">Відповідаємо у чаті та на пошті.</p>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
