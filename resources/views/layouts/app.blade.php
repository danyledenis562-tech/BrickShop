<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Brick Shop') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="prefetch" href="{{ route('welcome') }}" as="document">
        <link rel="prefetch" href="{{ route('catalog') }}" as="document">
        <link rel="prefetch" href="{{ route('shipping') }}" as="document">
        <link rel="prefetch" href="{{ route('returns') }}" as="document">
        <link rel="prefetch" href="{{ route('faq') }}" as="document">
        @auth
            <link rel="prefetch" href="{{ route('checkout.index') }}" as="document">
            <link rel="prefetch" href="{{ route('profile.index') }}" as="document">
        @endauth

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        @include('partials.header')

        @if (session('toast'))
            <div class="lego-toast" data-toast>{{ session('toast') }}</div>
        @endif

        <main class="min-h-screen theme-fade lego-page">
            @unless (request()->routeIs('welcome'))
                <div class="lego-back-bar">
                    <button type="button" class="lego-btn lego-btn-secondary lego-back-btn" data-back data-back-fallback="{{ route('welcome') }}">← Назад</button>
                </div>
            @endunless
            {{ $slot }}
        </main>

        @include('partials.footer')

        @include('partials.support-widget')
    </body>
</html>
