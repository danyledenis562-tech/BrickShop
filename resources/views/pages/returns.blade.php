<x-app-layout>
    <div class="mx-auto max-w-5xl px-4 py-10">
        <div class="info-hero">
            <h1 class="text-4xl font-extrabold">{{ __('messages.returns') }}</h1>
            <p class="text-[color:var(--muted)]">{{ __('messages.returns_text_1') }}</p>
        </div>

        <div class="mt-8 info-grid">
            <div class="lego-card info-card">
                <div class="info-icon">🧾</div>
                <div class="info-section-title">Умови повернення</div>
                <p class="text-sm text-[color:var(--muted)]">{{ __('messages.returns_text_2') }}</p>
            </div>
            <div class="lego-card info-card">
                <div class="info-icon">📦</div>
                <div class="info-section-title">Стан товару</div>
                <p class="text-sm text-[color:var(--muted)]">{{ __('messages.returns_text_3') }}</p>
            </div>
            <div class="lego-card info-card">
                <div class="info-icon">🔁</div>
                <div class="info-section-title">Обмін</div>
                <p class="text-sm text-[color:var(--muted)]">Якщо набір не підійшов, обміняємо на інший протягом 14 днів.</p>
            </div>
        </div>

        <section class="mt-10 lego-card p-6">
            <h2 class="text-xl font-bold">FAQ</h2>
            <div class="mt-4 info-faq">
                <div class="info-card">
                    <div class="info-section-title">Як оформити повернення?</div>
                    <p class="text-sm text-[color:var(--muted)]">Зверніться до підтримки та підготуйте номер замовлення.</p>
                </div>
                <div class="info-card">
                    <div class="info-section-title">Коли повернуть кошти?</div>
                    <p class="text-sm text-[color:var(--muted)]">Після перевірки товару — протягом 3-5 робочих днів.</p>
                </div>
            </div>
        </section>

        <section class="mt-10 lego-card p-6">
            <h2 class="text-xl font-bold">Чому нам довіряють</h2>
            <div class="mt-4 trust-grid">
                <div class="info-card">
                    <div class="info-section-title">Прозорі правила</div>
                    <p class="text-sm text-[color:var(--muted)]">Без прихованих умов чи комісій.</p>
                </div>
                <div class="info-card">
                    <div class="info-section-title">Швидка обробка</div>
                    <p class="text-sm text-[color:var(--muted)]">Повернення фіксуємо в день звернення.</p>
                </div>
                <div class="info-card">
                    <div class="info-section-title">Підтримка</div>
                    <p class="text-sm text-[color:var(--muted)]">Допоможемо на кожному етапі.</p>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
