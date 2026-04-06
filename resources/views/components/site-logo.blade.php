@props(['variant' => 'header'])

@php
    $name = config('app.name', 'Brick Shop');
    $logoText = config('shop.logo_text', 'Brick Shop');
    $logoUrl = config('shop.logo_url');
    $logoPath = config('shop.logo_path');
    $localExists = is_string($logoPath) && $logoPath !== '' && file_exists(public_path($logoPath));
    $src = $logoUrl ?: ($localExists ? asset($logoPath) : null);
    $markLetter = mb_strtoupper(mb_substr($logoText, 0, 1));
@endphp

@if ($variant === 'auth')
    <a href="{{ route('welcome') }}" class="lego-auth-logo lego-auth-logo-row" aria-label="{{ $name }}">
        @if ($src)
            <img src="{{ $src }}" alt="" class="lego-auth-logo-img h-9 w-[52px] shrink-0 object-contain" width="52" height="36" role="presentation">
            @unless (config('shop.logo_hide_wordmark'))
                <span class="lego-auth-logo-text">{{ $logoText }}</span>
            @endunless
        @else
            {{ $logoText }}
        @endif
    </a>
@else
    <a href="{{ route('welcome') }}" class="lego-logo" aria-label="{{ $name }}">
        @if ($src)
            <img src="{{ $src }}" alt="" class="lego-logo-img lego-logo-img--mark" role="presentation" width="52" height="36">
            @unless (config('shop.logo_hide_wordmark'))
                <span class="lego-logo-wordmark">{{ $logoText }}</span>
            @endunless
        @else
            <span class="lego-logo-mark">{{ $markLetter }}</span>
            <span>{{ $logoText }}</span>
        @endif
    </a>
@endif
