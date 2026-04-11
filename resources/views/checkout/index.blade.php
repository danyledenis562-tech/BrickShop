<x-app-layout>
    @push('scripts')
        @vite('resources/js/checkout.js')
    @endpush

    <div class="mx-auto max-w-7xl px-4 py-8">
        <div class="sr-only" aria-live="polite" aria-atomic="true" data-checkout-live></div>

        <div class="checkout-header mb-4">
            <h1 class="text-3xl font-extrabold">{{ __('messages.checkout') }}</h1>
            <p class="text-sm text-[color:var(--muted)]">{{ __('messages.checkout_hint') }}</p>
        </div>

        <div class="checkout-grid checkout-grid-comfy mt-4 lg:items-start">
            <form
                method="POST"
                action="{{ route('checkout.store') }}"
                class="space-y-4"
                data-checkout-wizard
                data-subtotal="{{ $subtotal }}"
                data-discount="{{ $discount }}"
                data-delivery-prices='@json(config("shop.delivery_prices", []))'
                data-nova-cities-url="{{ route('shipping.nova.cities') }}"
                data-nova-branches-url="{{ route('shipping.nova.branches') }}"
                data-nova-streets-url="{{ route('shipping.nova.streets') }}"
                data-msg-card-number-invalid="{{ __('messages.card_number_invalid') }}"
                data-msg-card-expiry-invalid="{{ __('messages.card_expiry_invalid') }}"
                data-msg-card-cvv-invalid="{{ __('messages.card_cvv_invalid') }}"
                data-msg-nova-branch-required="{{ __('messages.nova_branch_required') }}"
                data-msg-courier-city-required="{{ __('messages.courier_city_required') }}"
                data-msg-courier-street-required="{{ __('messages.courier_street_required') }}"
                data-msg-courier-house-required="{{ __('messages.courier_house_required') }}"
                data-msg-ukrposhta-city-required="{{ __('messages.ukrposhta_city_required') }}"
                data-msg-ukrposhta-branch-required="{{ __('messages.ukrposhta_branch_required') }}"
            >
                @csrf
                <input type="hidden" name="bonus_to_spend" value="{{ $bonusToSpend ?? 0 }}">
                <input type="hidden" name="promo_code" value="{{ request('promo_code') }}">
                <input type="hidden" name="city" value="{{ old('city', auth()->user()?->city) }}" data-delivery-city-hidden>
                <input type="hidden" name="address" value="{{ old('address', auth()->user()?->address) }}" data-delivery-address-hidden>

                <div class="checkout-progress" data-checkout-progress>
                    <button type="button" class="checkout-progress-step is-active" data-step-nav="1">
                        <span>1</span>
                        <strong>{{ __('messages.delivery') }}</strong>
                    </button>
                    <button type="button" class="checkout-progress-step" data-step-nav="2">
                        <span>2</span>
                        <strong>{{ __('messages.payment') }}</strong>
                    </button>
                    <button type="button" class="checkout-progress-step" data-step-nav="3">
                        <span>3</span>
                        <strong>{{ __('messages.confirm') }}</strong>
                    </button>
                </div>

                <section class="lego-card p-6 space-y-5 checkout-stage is-active" data-checkout-step="1">
                    <div class="space-y-2">
                        <h2 class="checkout-section-title">{{ __('messages.delivery') }}</h2>
                        <p class="text-sm text-[color:var(--muted)]">{{ __('messages.checkout_step_delivery_desc') }}</p>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-1">
                            <div class="text-xs font-semibold text-[color:var(--muted)]">{{ __('messages.full_name') }}</div>
                            <input name="full_name" class="lego-input" placeholder="{{ __('messages.full_name') }}" value="{{ old('full_name', auth()->user()?->name) }}" required>
                        </div>

                        <div class="space-y-1">
                            <div class="text-xs font-semibold text-[color:var(--muted)]">{{ __('messages.phone') }}</div>
                            <input name="phone" class="lego-input" placeholder="{{ __('messages.phone') }}" value="{{ old('phone', auth()->user()?->phone) }}" required>
                        </div>

                        @guest
                            <div class="space-y-1 md:col-span-2">
                                <div class="text-xs font-semibold text-[color:var(--muted)]">{{ __('messages.email') }}</div>
                                <input name="guest_email" type="email" class="lego-input" placeholder="{{ __('messages.email') }}" value="{{ old('guest_email') }}" required autocomplete="email">
                            </div>
                        @endguest

                        <div class="space-y-1 md:col-span-2">
                            <div class="text-xs font-semibold text-[color:var(--muted)]">{{ __('messages.shipping_methods') }}</div>
                            <div class="grid gap-3 md:grid-cols-3" role="radiogroup" aria-label="{{ __('messages.shipping_methods') }}">
                                <label class="lego-radio-card" data-delivery-radio-card data-delivery-label="{{ __('messages.delivery_nova') }}">
                                    <input
                                        type="radio"
                                        name="delivery_type"
                                        value="nova"
                                        class="sr-only"
                                        data-delivery-radio
                                        required
                                        {{ old('delivery_type', $deliveryType ?? 'nova') === 'nova' ? 'checked' : '' }}
                                    >
                                    <div class="text-sm font-extrabold">{{ __('messages.delivery_nova') }}</div>
                                    <div class="text-xs text-[color:var(--muted)]">{{ config('shop.delivery_prices.nova', 100) }} грн</div>
                                </label>

                                <label class="lego-radio-card" data-delivery-radio-card data-delivery-label="{{ __('messages.delivery_courier_nova') }}">
                                    <input
                                        type="radio"
                                        name="delivery_type"
                                        value="courier"
                                        class="sr-only"
                                        data-delivery-radio
                                        required
                                        {{ old('delivery_type', $deliveryType ?? 'nova') === 'courier' ? 'checked' : '' }}
                                    >
                                    <div class="text-sm font-extrabold">{{ __('messages.delivery_courier_nova') }}</div>
                                    <div class="text-xs text-[color:var(--muted)]">{{ config('shop.delivery_prices.courier', 250) }} грн</div>
                                </label>

                                <label class="lego-radio-card" data-delivery-radio-card data-delivery-label="{{ __('messages.delivery_ukrposhta') }}">
                                    <input
                                        type="radio"
                                        name="delivery_type"
                                        value="ukrposhta"
                                        class="sr-only"
                                        data-delivery-radio
                                        required
                                        {{ old('delivery_type', $deliveryType ?? 'nova') === 'ukrposhta' ? 'checked' : '' }}
                                    >
                                    <div class="text-sm font-extrabold">{{ __('messages.delivery_ukrposhta') }}</div>
                                    <div class="text-xs text-[color:var(--muted)]">{{ config('shop.delivery_prices.ukrposhta', 50) }} грн</div>
                                </label>
                            </div>
                        </div>

                        <div class="md:col-span-2 grid gap-4" data-delivery-block="nova">
                            <div x-data="cityDropdown()" class="space-y-1 relative">
                                <div class="text-xs font-semibold text-[color:var(--muted)]">
                                    {{ __('messages.delivery_nova_city') }}
                                </div>

                                <label class="checkout-field">
                                    <span class="checkout-icon">🏙️</span>
                                    <input
                                        type="text"
                                        x-model="search"
                                        @focus="open = true; filter()"
                                        @input="filter()"
                                        placeholder="{{ __('messages.select_city') }}"
                                        class="lego-input"
                                        autocomplete="off"
                                    >
                                </label>

                                <div
                                    x-show="open"
                                    class="absolute z-10 mt-1 w-full rounded-xl border border-[color:var(--border)] bg-[color:var(--card)] shadow-2xl max-h-72 overflow-y-auto"
                                >
                                    <template x-if="loading">
                                        <div class="p-4 text-sm text-[color:var(--muted)]">
                                            {{ __('messages.loading_cities') }}
                                        </div>
                                    </template>

                                    <template x-if="!loading && filtered.length === 0">
                                        <div class="p-4 text-sm text-[color:var(--muted)]">
                                            {{ __('messages.nothing_found') }}
                                        </div>
                                    </template>

                                    <template x-for="item in filtered" :key="item.id">
                                        <button
                                            type="button"
                                            @click="select(item)"
                                            class="flex w-full flex-col items-start gap-1 px-4 py-3 text-left hover:bg-yellow-50/60"
                                        >
                                            <div class="text-sm font-semibold text-[color:var(--text)]" x-text="item.name"></div>
                                        </button>
                                    </template>
                                </div>

                                <input type="hidden" name="nova_city" data-delivery-city-field="nova" x-model="selectedName">
                                <input type="hidden" name="nova_city_ref" data-nova-city-ref x-model="selectedRef">
                            </div>

                            <div x-data="branchDropdown()" class="space-y-1 relative">
                                <div class="text-xs font-semibold text-[color:var(--muted)]">
                                    {{ __('messages.delivery_nova_branch') }}
                                </div>

                                <label class="checkout-field">
                                    <span class="checkout-icon">📦</span>
                                    <input
                                        type="text"
                                        x-model="search"
                                        @focus="openDropdown()"
                                        @input="filter()"
                                        placeholder="{{ __('messages.select_branch') }}"
                                        class="lego-input"
                                    >
                                </label>

                                <div
                                    x-show="open"
                                    class="absolute z-10 mt-1 w-full rounded-xl border border-[color:var(--border)] bg-[color:var(--card)] shadow-2xl max-h-72 overflow-y-auto"
                                >
                                    <template x-if="loading">
                                        <div class="p-4 text-sm text-[color:var(--muted)]">
                                            {{ __('messages.loading_branches') }}
                                        </div>
                                    </template>

                                    <template x-if="!loading && filtered.length === 0">
                                        <div class="p-4 text-sm text-[color:var(--muted)]">
                                            {{ __('messages.nothing_found') }}
                                        </div>
                                    </template>

                                    <template x-for="item in filtered" :key="item.id">
                                        <button
                                            type="button"
                                            @click="select(item)"
                                            class="flex w-full flex-col items-start gap-1 px-4 py-3 text-left hover:bg-yellow-50/60"
                                        >
                                            <div class="text-sm font-semibold text-[color:var(--text)]" x-text="item.name"></div>
                                            <div class="text-xs text-[color:var(--muted)]" x-text="item.address"></div>
                                        </button>
                                    </template>
                                </div>

                                <input type="hidden" name="nova_branch" :value="search">
                                <input type="hidden" name="branch_id" :value="selected?.id">
                                <p class="checkout-error" data-field-error="nova_branch"></p>
                            </div>
                        </div>

                        <div class="md:col-span-2 grid gap-4 hidden" data-delivery-block="courier">
                            <div class="space-y-1">
                                <div class="text-xs font-semibold text-[color:var(--muted)]">{{ __('messages.delivery_courier_city_nova') }}</div>
                                <div x-data="courierCityDropdown()" class="space-y-1 relative">
                                    <label class="checkout-field">
                                        <span class="checkout-icon">🏙️</span>
                                        <input
                                            type="text"
                                            x-model="search"
                                            @focus="open = true; filter()"
                                            @input="filter()"
                                            placeholder="{{ __('messages.select_city') }}"
                                            class="lego-input"
                                            autocomplete="off"
                                        >
                                    </label>

                                    <div
                                        x-show="open"
                                        class="absolute z-10 mt-1 w-full rounded-xl border border-[color:var(--border)] bg-[color:var(--card)] shadow-2xl max-h-72 overflow-y-auto"
                                    >
                                        <template x-if="loading">
                                            <div class="p-4 text-sm text-[color:var(--muted)]">
                                                {{ __('messages.loading_cities') }}
                                            </div>
                                        </template>

                                        <template x-if="!loading && filtered.length === 0">
                                            <div class="p-4 text-sm text-[color:var(--muted)]">
                                                {{ __('messages.nothing_found') }}
                                            </div>
                                        </template>

                                        <template x-for="item in filtered" :key="item.id">
                                            <button
                                                type="button"
                                                @click="select(item)"
                                                class="flex w-full flex-col items-start gap-1 px-4 py-3 text-left hover:bg-yellow-50/60"
                                            >
                                                <div class="text-sm font-semibold text-[color:var(--text)]" x-text="item.name"></div>
                                            </button>
                                        </template>
                                    </div>

                                    <input type="hidden" name="courier_city" data-delivery-city-field="courier" x-model="selectedName">
                                    <input type="hidden" name="courier_city_ref" x-model="selectedRef">
                                    <p class="checkout-error" data-field-error="courier_city"></p>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <div class="text-xs font-semibold text-[color:var(--muted)]">{{ __('messages.delivery_courier_street') }}</div>
                                <div x-data="streetDropdown()" class="space-y-1 relative">
                                    <label class="checkout-field">
                                        <span class="checkout-icon">📍</span>
                                        <input
                                            type="text"
                                            x-model="search"
                                            @focus="openDropdown()"
                                            @input="filter()"
                                            placeholder="{{ __('messages.delivery_courier_street') }}"
                                            class="lego-input"
                                            autocomplete="off"
                                        >
                                    </label>

                                    <div
                                        x-show="open"
                                        class="absolute z-10 mt-1 w-full rounded-xl border border-[color:var(--border)] bg-[color:var(--card)] shadow-2xl max-h-72 overflow-y-auto"
                                    >
                                        <template x-if="loading">
                                            <div class="p-4 text-sm text-[color:var(--muted)]">
                                                {{ __('messages.loading_streets') }}
                                            </div>
                                        </template>

                                        <template x-if="!loading && filtered.length === 0">
                                            <div class="p-4 text-sm text-[color:var(--muted)]">
                                                {{ __('messages.nothing_found') }}
                                            </div>
                                        </template>

                                        <template x-for="item in filtered" :key="item.id">
                                            <button
                                                type="button"
                                                @click="select(item)"
                                                class="flex w-full flex-col items-start gap-1 px-4 py-3 text-left hover:bg-yellow-50/60"
                                            >
                                                <div class="text-sm font-semibold text-[color:var(--text)]" x-text="item.name"></div>
                                            </button>
                                        </template>
                                    </div>

                                    <input type="hidden" name="courier_street" x-model="selectedName">
                                    <p class="checkout-error" data-field-error="courier_street"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <div class="text-xs font-semibold text-[color:var(--muted)]">{{ __('messages.delivery_courier_house') }}</div>
                                    <input name="courier_house" class="lego-input" placeholder="{{ __('messages.delivery_courier_house') }}" value="{{ old('courier_house') }}">
                                    <p class="checkout-error" data-field-error="courier_house"></p>
                                </div>
                                <div class="space-y-1">
                                    <div class="text-xs font-semibold text-[color:var(--muted)]">{{ __('messages.delivery_courier_apartment') }}</div>
                                    <input name="courier_apartment" class="lego-input" placeholder="{{ __('messages.delivery_courier_apartment') }}" value="{{ old('courier_apartment') }}">
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2 grid gap-4 hidden" data-delivery-block="ukrposhta">
                            <div x-data="cityDropdown({ cityName: 'ukrposhta_city', cityRef: 'ukrposhta_city_ref' })" class="space-y-1 relative">
                                <div class="text-xs font-semibold text-[color:var(--muted)]">{{ __('messages.delivery_ukrposhta_city') }}</div>

                                <label class="checkout-field">
                                    <span class="checkout-icon">🏙️</span>
                                    <input
                                        type="text"
                                        x-model="search"
                                        @focus="open = true; filter()"
                                        @input="filter()"
                                        placeholder="{{ __('messages.select_city') }}"
                                        class="lego-input"
                                        autocomplete="off"
                                    >
                                </label>

                                <div
                                    x-show="open"
                                    class="absolute z-10 mt-1 w-full rounded-xl border border-[color:var(--border)] bg-[color:var(--card)] shadow-2xl max-h-72 overflow-y-auto"
                                >
                                    <template x-if="loading">
                                        <div class="p-4 text-sm text-[color:var(--muted)]">
                                            {{ __('messages.loading_cities') }}
                                        </div>
                                    </template>

                                    <template x-if="!loading && filtered.length === 0">
                                        <div class="p-4 text-sm text-[color:var(--muted)]">
                                            {{ __('messages.nothing_found') }}
                                        </div>
                                    </template>

                                    <template x-for="item in filtered" :key="item.id">
                                        <button
                                            type="button"
                                            @click="select(item)"
                                            class="flex w-full flex-col items-start gap-1 px-4 py-3 text-left hover:bg-yellow-50/60"
                                        >
                                            <div class="text-sm font-semibold text-[color:var(--text)]" x-text="item.name"></div>
                                        </button>
                                    </template>
                                </div>

                                <input type="hidden" name="ukrposhta_city" data-delivery-city-field="ukrposhta" x-model="selectedName">
                                <input type="hidden" name="ukrposhta_city_ref" x-model="selectedRef">
                                <p class="checkout-error" data-field-error="ukrposhta_city"></p>
                            </div>

                            <div x-data="branchDropdown({ cityRefField: 'ukrposhta_city_ref', branchNameField: 'ukrposhta_branch' })" class="space-y-1 relative">
                                <div class="text-xs font-semibold text-[color:var(--muted)]">{{ __('messages.delivery_ukrposhta_branch') }}</div>

                                <label class="checkout-field">
                                    <span class="checkout-icon">📦</span>
                                    <input
                                        type="text"
                                        x-model="search"
                                        @focus="openDropdown()"
                                        @input="filter()"
                                        placeholder="{{ __('messages.select_branch') }}"
                                        class="lego-input"
                                        autocomplete="off"
                                    >
                                </label>

                                <div
                                    x-show="open"
                                    class="absolute z-10 mt-1 w-full rounded-xl border border-[color:var(--border)] bg-[color:var(--card)] shadow-2xl max-h-72 overflow-y-auto"
                                >
                                    <template x-if="loading">
                                        <div class="p-4 text-sm text-[color:var(--muted)]">
                                            {{ __('messages.loading_branches') }}
                                        </div>
                                    </template>

                                    <template x-if="!loading && filtered.length === 0">
                                        <div class="p-4 text-sm text-[color:var(--muted)]">
                                            {{ __('messages.nothing_found') }}
                                        </div>
                                    </template>

                                    <template x-for="item in filtered" :key="item.id">
                                        <button
                                            type="button"
                                            @click="select(item)"
                                            class="flex w-full flex-col items-start gap-1 px-4 py-3 text-left hover:bg-yellow-50/60"
                                        >
                                            <div class="text-sm font-semibold text-[color:var(--text)]" x-text="item.name"></div>
                                            <div class="text-xs text-[color:var(--muted)]" x-text="item.address"></div>
                                        </button>
                                    </template>
                                </div>

                                <input type="hidden" name="ukrposhta_branch" :value="search">
                                <p class="checkout-error" data-field-error="ukrposhta_branch"></p>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="lego-btn lego-btn-primary w-full justify-center text-base" data-step-next>
                        {{ __('messages.go') }}
                    </button>
                </section>

                <section class="lego-card p-6 space-y-5 checkout-stage" data-checkout-step="2">
                    <div class="space-y-1">
                        <h2 class="checkout-section-title">{{ __('messages.payment') }}</h2>
                        <p class="text-sm text-[color:var(--muted)]">{{ __('messages.payment_methods') }}</p>
                    </div>

                    <div class="space-y-2">
                        <div class="text-xs font-semibold text-[color:var(--muted)]">{{ __('messages.payment') }}</div>
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3" role="radiogroup" aria-label="{{ __('messages.payment_methods') }}">
                            <label class="lego-radio-card" data-payment-radio-card data-payment-label="{{ __('messages.payment_card') }}">
                                <input
                                    type="radio"
                                    name="payment_type"
                                    value="card"
                                    class="sr-only"
                                    data-payment-radio
                                    required
                                    {{ old('payment_type', 'card') === 'card' ? 'checked' : '' }}
                                >
                                <div class="text-sm font-extrabold">{{ __('messages.payment_card') }}</div>
                                <div class="text-xs text-[color:var(--muted)]">{{ __('messages.payment_card_hint') }}</div>
                            </label>

                            <label class="lego-radio-card" data-payment-radio-card data-payment-label="{{ __('messages.payment_cash') }}">
                                <input
                                    type="radio"
                                    name="payment_type"
                                    value="cash"
                                    class="sr-only"
                                    data-payment-radio
                                    required
                                    {{ old('payment_type', 'card') === 'cash' ? 'checked' : '' }}
                                >
                                <div class="text-sm font-extrabold">{{ __('messages.payment_cash') }}</div>
                                <div class="text-xs text-[color:var(--muted)]">{{ __('messages.payment_cash_hint') }}</div>
                            </label>

                            @if (filled(config('shop.liqpay.public_key')) && filled(config('shop.liqpay.private_key')))
                                <label class="lego-radio-card" data-payment-radio-card data-payment-label="{{ __('messages.payment_liqpay') }}">
                                    <input
                                        type="radio"
                                        name="payment_type"
                                        value="liqpay"
                                        class="sr-only"
                                        data-payment-radio
                                        required
                                        {{ old('payment_type') === 'liqpay' ? 'checked' : '' }}
                                    >
                                    <div class="text-sm font-extrabold">{{ __('messages.payment_liqpay') }}</div>
                                    <div class="text-xs text-[color:var(--muted)]">{{ __('messages.payment_liqpay_hint') }}</div>
                                </label>
                            @endif
                        </div>
                    </div>

                    <div class="checkout-section {{ old('payment_type', 'card') === 'card' ? '' : 'hidden' }}" data-payment-card>
                        <div class="space-y-1">
                            <h3 class="checkout-section-title">{{ __('messages.payment_card') }}</h3>
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="space-y-1 md:col-span-2">
                                    <div class="text-xs font-semibold text-[color:var(--muted)]">{{ __('messages.card_number') }}</div>
                                    <input name="card_number" class="lego-input" placeholder="0000 0000 0000 0000" inputmode="numeric" maxlength="19" value="{{ old('card_number') }}">
                                    <p class="checkout-error" data-field-error="card_number"></p>
                                </div>
                                <div class="space-y-1">
                                    <div class="text-xs font-semibold text-[color:var(--muted)]">{{ __('messages.card_expiry') }}</div>
                                    <input name="card_expiry" class="lego-input" placeholder="MM/YY" inputmode="numeric" maxlength="5" value="{{ old('card_expiry') }}">
                                    <p class="checkout-error" data-field-error="card_expiry"></p>
                                </div>
                                <div class="space-y-1">
                                    <div class="text-xs font-semibold text-[color:var(--muted)]">{{ __('messages.card_cvv') }}</div>
                                    <input name="card_cvv" class="lego-input" placeholder="123" inputmode="numeric" maxlength="4" value="{{ old('card_cvv') }}">
                                    <p class="checkout-error" data-field-error="card_cvv"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-3 md:grid-cols-2">
                        <button type="button" class="lego-btn lego-btn-secondary justify-center" data-step-prev>{{ __('messages.back') }}</button>
                        <button type="button" class="lego-btn lego-btn-primary justify-center" data-step-next>{{ __('messages.go') }}</button>
                    </div>
                </section>

                <section class="lego-card p-6 space-y-5 checkout-stage" data-checkout-step="3">
                    <h2 class="checkout-section-title">{{ __('messages.confirm') }}</h2>
                    <div class="checkout-confirm-list">
                        <div class="checkout-confirm-item">
                            <span>{{ __('messages.delivery') }}</span>
                            <strong data-checkout-preview-delivery>—</strong>
                        </div>
                        <div class="checkout-confirm-item">
                            <span>{{ __('messages.payment') }}</span>
                            <strong data-checkout-preview-payment>—</strong>
                        </div>
                        <div class="checkout-confirm-item">
                            <span>{{ __('messages.delivery_price') }}</span>
                            <strong data-checkout-preview-delivery-price>{{ number_format($shippingAmount ?? 0, 2) }} грн</strong>
                        </div>
                    </div>

                    <div class="checkout-section">
                        <h3 class="checkout-section-title">{{ __('messages.note') }}</h3>
                        <label class="checkout-field">
                            <span class="checkout-icon">📝</span>
                            <textarea name="note" class="lego-input" placeholder="{{ __('messages.note') }}" rows="3">{{ old('note') }}</textarea>
                        </label>
                        <div class="mt-2 flex items-center gap-2 text-sm">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="dont_call" value="1" class="sr-only lego-checkbox-input">
                                <span class="lego-checkbox">
                                    <span class="lego-checkbox-inner"></span>
                                </span>
                                <span>{{ __('messages.dont_call_confirm') }}</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid gap-3 md:grid-cols-2">
                        <button type="button" class="lego-btn lego-btn-secondary justify-center" data-step-prev>{{ __('messages.back') }}</button>
                        <button class="lego-btn lego-btn-primary justify-center text-base">{{ __('messages.confirm_order') }}</button>
                    </div>
                </section>
            </form>

            <aside class="space-y-4">
                <div class="lego-card p-6 checkout-summary">
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
                <div class="checkout-item">
                    <span>{{ __('messages.subtotal') }}</span>
                    <span>{{ number_format($subtotal, 2) }} грн</span>
                </div>
                <div class="checkout-item">
                    <span>{{ __('messages.delivery_price') }}</span>
                    <span data-checkout-shipping-price>{{ number_format($shippingAmount ?? 0, 2) }} грн</span>
                </div>
                @if($discount > 0)
                    <div class="checkout-item text-green-600">
                        <span>{{ __('messages.discount') }}</span>
                        <span>−{{ number_format($discount, 2) }} грн</span>
                    </div>
                @endif
                @if(($bonusToSpend ?? 0) > 0)
                    <div class="checkout-item text-purple-600">
                        <span>{{ __('messages.bonus_spent') }}</span>
                        <span>−{{ number_format($bonusToSpend, 0) }}</span>
                    </div>
                @endif
                <div class="checkout-total">
                    <span>{{ __('messages.total') }}</span>
                    <span data-checkout-total>{{ number_format($total, 2) }}</span> грн
                </div>
                @auth
                    @if(($previewBonusEarn ?? 0) > 0)
                        <p class="mt-2 text-xs text-[color:var(--muted)]">
                            {{ __('messages.bonus_will_earn_footer', ['amount' => $previewBonusEarn]) }}
                        </p>
                    @endif
                @endauth
                </div>

                <div class="lego-card p-6 space-y-4">
                    <h2 class="text-lg font-bold">{{ __('messages.promo_code') }}</h2>
                    @if($appliedPromo)
                        <p class="text-sm text-[color:var(--muted)]">
                            {{ __('messages.promo_applied') }}:
                            <strong>{{ $appliedPromo->code }}</strong>
                        </p>
                        <form method="GET" action="{{ route('checkout.index') }}" class="mt-2">
                            <input type="hidden" name="delivery_type" value="{{ $deliveryType ?? 'nova' }}">
                            <button type="submit" class="text-xs text-red-500 underline">
                                {{ __('messages.cancel') }}
                            </button>
                        </form>
                    @else
                        <form method="GET" action="{{ route('checkout.index') }}" class="flex gap-2">
                            <input type="hidden" name="delivery_type" value="{{ $deliveryType ?? 'nova' }}">
                            <input type="text" name="promo_code" class="lego-input flex-1" placeholder="{{ __('messages.promo_code') }}" value="{{ old('promo_code', request('promo_code')) }}">
                            <button type="submit" class="lego-btn lego-btn-secondary">
                                {{ __('messages.promo_apply') }}
                            </button>
                        </form>
                        @if(request('promo_code') && !$appliedPromo)
                            <p class="mt-1 text-sm text-red-600">{{ __('messages.promo_invalid') }}</p>
                        @endif
                    @endif

                    @auth
                        <div class="rounded-2xl border border-[color:var(--border)] bg-[color:var(--card)] p-3 text-xs">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-1 font-semibold">
                                        <span>⭐</span>
                                        <span>{{ __('messages.bonus_title') }}</span>
                                    </div>
                                    <div class="mt-1 text-[color:var(--muted)]">
                                        {{ __('messages.bonus_balance') }}:
                                        <strong>{{ $bonusBalance }}</strong>
                                    </div>
                                    <div class="mt-1 text-[color:var(--muted)]">
                                        {{ __('messages.bonus_hint', ['rate' => config('shop.bonus_earn_rate', 25)]) }}
                                    </div>
                                </div>
                            </div>

                            @if(($bonusBalance ?? 0) > 0)
                                <form method="GET" action="{{ route('checkout.index') }}" class="mt-3 flex flex-col gap-2">
                                    <input type="hidden" name="delivery_type" value="{{ $deliveryType ?? 'nova' }}">
                                    @if(request('promo_code'))
                                        <input type="hidden" name="promo_code" value="{{ request('promo_code') }}">
                                    @endif
                                    <div class="flex items-center gap-2">
                                        <input
                                            type="number"
                                            name="bonus_to_spend"
                                            min="0"
                                            max="{{ $maxBonusUsable ?? $bonusBalance }}"
                                            value="{{ $bonusToSpend ?? 0 }}"
                                            class="lego-input h-9 w-28 text-xs"
                                        >
                                        <span class="text-[color:var(--muted)]">
                                            {{ __('messages.bonus_can_spend', ['max' => $maxBonusUsable ?? 0]) }}
                                        </span>
                                    </div>
                                    <button type="submit" class="lego-btn lego-btn-secondary h-8 w-max px-3 text-xs">
                                        {{ __('messages.bonus_apply_button') }}
                                    </button>
                                </form>
                            @endif

                            @if(($previewBonusEarn ?? 0) > 0)
                                <div class="mt-3 rounded-xl bg-purple-600/10 px-3 py-2 text-[11px] text-purple-700">
                                    {{ __('messages.bonus_will_earn', ['amount' => $previewBonusEarn]) }}
                                </div>
                            @endif
                        </div>
                    @endauth
                </div>
            </aside>
        </div>
    </div>

</x-app-layout>
