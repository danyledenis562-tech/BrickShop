@php
    $setting = \App\Models\Setting::query()->first();
@endphp

@if ($setting?->show_support_widget ?? true)
    <div class="support-widget">
        <button type="button" data-support-toggle class="support-fab lego-brick" aria-label="Support">
            <span class="support-fab-icon">💬</span>
        </button>
        <div data-support-popup class="support-panel hidden">
            <div class="support-panel-header">
                <div>
                    <p class="support-title">{{ __('messages.support_title') }}</p>
                    <p class="support-subtitle">{{ __('messages.support_subtitle') }}</p>
                </div>
                <span class="support-pill">LEGO Help</span>
            </div>
            <div class="support-actions">
                <a href="{{ $setting?->telegram_support_url ?? 'https://t.me/' }}" target="_blank" rel="noopener" class="support-action">
                    <span>Telegram</span>
                    <span class="support-action-icon">↗</span>
                </a>
                <a href="mailto:{{ $setting?->email_support ?? 'support@brickshop.ua' }}" class="support-action">
                    <span>Email</span>
                    <span class="support-action-icon">✉</span>
                </a>
                <a href="{{ route('faq') }}" class="support-action">
                    <span>FAQ</span>
                    <span class="support-action-icon">?</span>
                </a>
            </div>
        </div>
    </div>
@endif
