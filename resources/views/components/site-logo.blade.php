@props(['variant' => 'header'])

@php
    $name = config('app.name', 'Brick Shop');
    $logoUrl = config('shop.logo_url');
    $logoPath = config('shop.logo_path');
    $localExists = is_string($logoPath) && $logoPath !== '' && file_exists(public_path($logoPath));
    $src = $logoUrl ?: ($localExists ? asset($logoPath) : null);
@endphp

@if ($variant === 'auth')
    <a href="{{ route('welcome') }}" class="lego-auth-logo" aria-label="{{ $name }}">
        @if ($src)
            <img src="{{ $src }}" alt="{{ $name }}" class="lego-auth-logo-img mx-auto block h-12 w-auto max-w-[220px] object-contain">
        @else
            {{ $name }}
        @endif
    </a>
@else
    <a href="{{ route('welcome') }}" class="lego-logo" aria-label="{{ $name }}">
        @if ($src)
            <img src="{{ $src }}" alt="{{ $name }}" class="lego-logo-img">
        @else
            <span class="lego-logo-mark">{{ mb_substr($name, 0, 1) }}</span>
            <span>{{ $name }}</span>
        @endif
    </a>
@endif
