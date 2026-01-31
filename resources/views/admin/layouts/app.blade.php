<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Admin - Brick Shop</title>
        @vite(['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased admin-theme">
        <div class="admin-shell">
            @include('admin.partials.sidebar')

            <div class="admin-content">
                @include('admin.partials.topbar', ['breadcrumb' => $breadcrumb ?? 'Admin'])

                <main class="admin-main">
                    @if (session('toast'))
                        <div class="lego-toast" data-toast>{{ session('toast') }}</div>
                    @endif
                    {{ $slot }}
                </main>
            </div>
        </div>

        <div class="admin-confirm" data-confirm-modal>
            <div class="admin-confirm-card">
                <div class="text-lg font-semibold">Підтвердження дії</div>
                <p class="mt-2 text-sm text-[color:var(--text-muted)]" data-confirm-text>Ви впевнені?</p>
                <div class="admin-confirm-actions">
                    <button type="button" class="lego-btn lego-btn-secondary text-xs" data-confirm-cancel>Скасувати</button>
                    <button type="button" class="lego-btn lego-btn-primary text-xs" data-confirm-ok>Так, видалити</button>
                </div>
            </div>
        </div>
    </body>
</html>
