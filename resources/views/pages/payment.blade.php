<x-app-layout>
    <div class="mx-auto max-w-5xl px-4 py-10">
        <div class="info-hero">
            <h1 class="text-4xl font-extrabold">{{ __('messages.payment') }}</h1>
            <p class="text-[color:var(--muted)]">{{ __('messages.payment_details') }}</p>
        </div>

        <div class="mt-8 info-grid">
            <div class="lego-card info-card">
                <div class="info-icon">💳</div>
                <div class="info-section-title">{{ __('messages.payment_card') }}</div>
                <p class="text-sm text-[color:var(--muted)]">Оплата карткою онлайн — швидко та безпечно.</p>
            </div>
            <div class="lego-card info-card">
                <div class="info-icon">💵</div>
                <div class="info-section-title">{{ __('messages.payment_cash') }}</div>
                <p class="text-sm text-[color:var(--muted)]">Готівка або переказ при отриманні замовлення.</p>
            </div>
            <div class="lego-card info-card">
                <div class="info-icon">🧾</div>
                <div class="info-section-title">Фіскальний чек</div>
                <p class="text-sm text-[color:var(--muted)]">Надсилаємо чек на email або в особистому кабінеті.</p>
            </div>
        </div>

        <section class="mt-10 lego-card p-6">
            <h2 class="text-xl font-bold">FAQ</h2>
            <div class="mt-4 info-faq">
                <div class="info-card">
                    <div class="info-section-title">Чи можна оплатити частинами?</div>
                    <p class="text-sm text-[color:var(--muted)]">Наразі доступна повна оплата карткою або при отриманні.</p>
                </div>
                <div class="info-card">
                    <div class="info-section-title">Чи безпечні платежі?</div>
                    <p class="text-sm text-[color:var(--muted)]">Платежі проходять через захищені платіжні шлюзи.</p>
                </div>
            </div>
        </section>

        <section class="mt-10 lego-card p-6">
            <h2 class="text-xl font-bold">Чому нам довіряють</h2>
            <div class="mt-4 trust-grid">
                <div class="info-card">
                    <div class="info-section-title">Прозора оплата</div>
                    <p class="text-sm text-[color:var(--muted)]">Без прихованих комісій.</p>
                </div>
                <div class="info-card">
                    <div class="info-section-title">Офіційні документи</div>
                    <p class="text-sm text-[color:var(--muted)]">Надаємо чек та підтвердження.</p>
                </div>
                <div class="info-card">
                    <div class="info-section-title">Підтримка</div>
                    <p class="text-sm text-[color:var(--muted)]">Завжди допоможемо з оплатою.</p>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
