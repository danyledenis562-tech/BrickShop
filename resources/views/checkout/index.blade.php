<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-10">
        <div class="checkout-header">
            <h1 class="text-3xl font-extrabold">{{ __('messages.checkout') }}</h1>
            <p class="text-sm text-[color:var(--muted)]">Заповніть дані — замовлення буде готове за хвилину.</p>
        </div>

        <div class="checkout-grid mt-6">
            <div class="space-y-6">
                <div class="checkout-steps lego-card p-5">
                    <div class="checkout-step is-active">
                        <span class="checkout-step-index">1</span>
                        <div>
                            <div class="checkout-step-title">Контактні дані</div>
                            <div class="checkout-step-subtitle">Імʼя, телефон, адреса</div>
                        </div>
                    </div>
                    <div class="checkout-step">
                        <span class="checkout-step-index">2</span>
                        <div>
                            <div class="checkout-step-title">Доставка та оплата</div>
                            <div class="checkout-step-subtitle">Виберіть спосіб</div>
                        </div>
                    </div>
                    <div class="checkout-step">
                        <span class="checkout-step-index">3</span>
                        <div>
                            <div class="checkout-step-title">Підтвердження</div>
                            <div class="checkout-step-subtitle">Підсумок замовлення</div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('checkout.store') }}" class="lego-card p-6 space-y-5">
                    @csrf
                    <div class="checkout-section">
                        <h2 class="checkout-section-title">Дані покупця</h2>
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="checkout-field">
                                <span class="checkout-icon">👤</span>
                                <input name="full_name" class="lego-input" placeholder="{{ __('messages.full_name') }}" value="{{ old('full_name', auth()->user()->name) }}">
                            </label>
                            <label class="checkout-field">
                                <span class="checkout-icon">📞</span>
                                <input name="phone" class="lego-input" placeholder="{{ __('messages.phone') }}" value="{{ old('phone', auth()->user()->phone) }}">
                            </label>
                            <label class="checkout-field md:col-span-2">
                                <span class="checkout-icon">🏙️</span>
                                <input name="city" class="lego-input" placeholder="{{ __('messages.city') }}" value="{{ old('city', auth()->user()->city) }}">
                            </label>
                            <label class="checkout-field md:col-span-2">
                                <span class="checkout-icon">📍</span>
                                <input name="address" class="lego-input" placeholder="{{ __('messages.address') }}" value="{{ old('address', auth()->user()->address) }}">
                            </label>
                        </div>
                    </div>

                    <div class="checkout-section">
                        <h2 class="checkout-section-title">Доставка та оплата</h2>
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="checkout-field">
                                <span class="checkout-icon">🚚</span>
                                <select name="delivery_type" class="lego-input">
                                    <option value="nova">{{ __('messages.delivery_nova') }}</option>
                                    <option value="courier">{{ __('messages.delivery_courier') }}</option>
                                </select>
                            </label>
                            <label class="checkout-field">
                                <span class="checkout-icon">💳</span>
                                <select name="payment_type" class="lego-input">
                                    <option value="card">{{ __('messages.payment_card') }}</option>
                                    <option value="cash">{{ __('messages.payment_cash') }}</option>
                                </select>
                            </label>
                        </div>
                    </div>

                    <div class="checkout-section">
                        <h2 class="checkout-section-title">{{ __('messages.note') }}</h2>
                        <label class="checkout-field">
                            <span class="checkout-icon">📝</span>
                            <textarea name="note" class="lego-input" placeholder="{{ __('messages.note') }}" rows="3">{{ old('note') }}</textarea>
                        </label>
                    </div>

                    <button class="lego-btn lego-btn-primary w-full justify-center">{{ __('messages.place_order') }}</button>
                </form>
            </div>

            <aside class="lego-card p-6 checkout-summary">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold">{{ __('messages.order_summary') }}</h2>
                    <span class="lego-badge">{{ count($cart) }}</span>
                </div>
                <div class="mt-4 space-y-3">
                    @foreach ($cart as $item)
                        <div class="checkout-item">
                            <div>
                                <div class="text-sm font-semibold">{{ $item['name'] }}</div>
                                <div class="text-xs text-[color:var(--muted)]">× {{ $item['quantity'] }}</div>
                            </div>
                            <div class="text-sm font-bold">{{ number_format($item['price'] * $item['quantity'], 2) }} грн</div>
                        </div>
                    @endforeach
                </div>
                <div class="checkout-total">
                    <span>{{ __('messages.total') }}</span>
                    <span>{{ number_format($total, 2) }} грн</span>
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
