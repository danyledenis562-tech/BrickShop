@php
    $setting = \App\Models\Setting::query()->first();
@endphp

<footer class="mt-16 border-t border-[color:var(--border)] bg-[color:var(--card)]">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 md:grid-cols-4">
        <div>
            <div class="text-2xl font-extrabold">Brick Shop</div>
            <p class="mt-3 text-sm text-[color:var(--muted)]">{{ __('messages.footer_tagline') }}</p>
            <div class="mt-4 flex gap-3 text-sm font-semibold">
                <a href="https://facebook.com" target="_blank" rel="noopener" class="hover:text-[color:var(--lego-red)]">Facebook</a>
                <a href="https://instagram.com" target="_blank" rel="noopener" class="hover:text-[color:var(--lego-red)]">Instagram</a>
                <a href="https://t.me" target="_blank" rel="noopener" class="hover:text-[color:var(--lego-red)]">Telegram</a>
            </div>
        </div>

        <div>
            <div class="text-sm font-bold uppercase">{{ __('messages.footer_company') }}</div>
            <ul class="mt-3 space-y-2 text-sm text-[color:var(--muted)]">
                <li><a href="{{ route('about') }}" class="hover:text-[color:var(--lego-red)]">{{ __('messages.about') }}</a></li>
                <li><a href="{{ route('contacts') }}" class="hover:text-[color:var(--lego-red)]">{{ __('messages.contacts') }}</a></li>
                <li><a href="{{ route('faq') }}" class="hover:text-[color:var(--lego-red)]">{{ __('messages.faq') }}</a></li>
                <li><a href="{{ route('policy') }}" class="hover:text-[color:var(--lego-red)]">{{ __('messages.policy') }}</a></li>
            </ul>
        </div>

        <div>
            <div class="text-sm font-bold uppercase">{{ __('messages.footer_delivery') }}</div>
            <ul class="mt-3 space-y-2 text-sm text-[color:var(--muted)]">
                <li><a href="{{ route('shipping') }}" class="hover:text-[color:var(--lego-red)]">{{ __('messages.shipping') }}</a></li>
                <li><a href="{{ route('payment') }}" class="hover:text-[color:var(--lego-red)]">{{ __('messages.payment') }}</a></li>
                <li><a href="{{ route('returns') }}" class="hover:text-[color:var(--lego-red)]">{{ __('messages.returns') }}</a></li>
                <li><a href="{{ route('catalog') }}" class="hover:text-[color:var(--lego-red)]">{{ __('messages.catalog') }}</a></li>
            </ul>
        </div>

        <div>
            <div class="text-sm font-bold uppercase">{{ __('messages.footer_support') }}</div>
            <div class="mt-3 space-y-2 text-sm text-[color:var(--muted)]">
                <div>{{ __('messages.support_phone') }}:</div>
                <div class="font-semibold text-[color:var(--lego-red)]">{{ $setting?->phone_support ?? '+380 00 000 00 00' }}</div>
                <div>{{ __('messages.support_telegram') }}:</div>
                <a href="{{ $setting?->telegram_support_url ?? 'https://t.me/' }}" target="_blank" rel="noopener" class="text-sm font-semibold text-[color:var(--lego-red)]">Telegram</a>
            </div>
        </div>
    </div>
    <div class="border-t border-[color:var(--border)] py-4 text-center text-xs text-[color:var(--muted)]">
        © {{ date('Y') }} Brick Shop
    </div>
</footer>
