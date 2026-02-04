@php
    $setting = \App\Models\Setting::query()->first();
@endphp

@if ($setting?->show_support_widget ?? true)
    <div class="support-widget">
        <button type="button" data-support-toggle class="support-fab" aria-label="{{ __('messages.support') }}">
            <span class="support-fab-icon">💬</span>
        </button>
        <div data-support-popup class="support-panel hidden">
            <div class="support-panel-header">
                <div>
                    <p class="support-title">{{ __('messages.support_title') }}</p>
                    <p class="support-subtitle">{{ __('messages.support_subtitle') }}</p>
                </div>
                <span class="support-pill">{{ __('messages.support_badge') }}</span>
            </div>
            <div class="support-actions">
                <a href="{{ $setting?->telegram_support_url ?? 'https://t.me/' }}" target="_blank" rel="noopener" class="support-action">
                    <span>{{ __('messages.support_telegram') }}</span>
                    <span class="support-action-icon">↗</span>
                </a>
                <a href="mailto:{{ $setting?->email_support ?? 'support@brickshop.ua' }}" class="support-action">
                    <span>{{ __('messages.support_email') }}</span>
                    <span class="support-action-icon">✉</span>
                </a>
                <a href="{{ route('faq') }}" class="support-action">
                    <span>{{ __('messages.support_faq') }}</span>
                    <span class="support-action-icon">?</span>
                </a>
            </div>
        </div>
    </div>
@endif
