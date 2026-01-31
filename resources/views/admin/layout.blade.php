<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Admin - Brick Shop</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="flex min-h-screen bg-[color:var(--bg)]">
            <aside class="w-72 border-r border-[color:var(--border)] bg-[color:var(--card)] p-6">
                <div class="rounded-2xl bg-[color:var(--lego-yellow)] px-4 py-3 text-2xl font-extrabold text-[color:var(--lego-gray)] shadow">Brick Admin</div>
                <nav class="mt-6 space-y-2 text-sm">
                    <a href="{{ route('admin.dashboard') }}" class="block rounded-xl px-3 py-2 font-semibold hover:bg-[color:var(--lego-yellow)]">{{ __('messages.admin_dashboard') }}</a>
                    <a href="{{ route('admin.products.index') }}" class="block rounded-xl px-3 py-2 hover:bg-[color:var(--lego-yellow)]">{{ __('messages.products') }}</a>
                    <a href="{{ route('admin.categories.index') }}" class="block rounded-xl px-3 py-2 hover:bg-[color:var(--lego-yellow)]">{{ __('messages.categories') }}</a>
                    <a href="{{ route('admin.banners.index') }}" class="block rounded-xl px-3 py-2 hover:bg-[color:var(--lego-yellow)]">{{ __('messages.banners') }}</a>
                    <a href="{{ route('admin.orders.index') }}" class="block rounded-xl px-3 py-2 hover:bg-[color:var(--lego-yellow)]">{{ __('messages.orders') }}</a>
                    <a href="{{ route('admin.reviews.index') }}" class="block rounded-xl px-3 py-2 hover:bg-[color:var(--lego-yellow)]">{{ __('messages.reviews') }}</a>
                    <a href="{{ route('admin.users.index') }}" class="block rounded-xl px-3 py-2 hover:bg-[color:var(--lego-yellow)]">{{ __('messages.users') }}</a>
                    <a href="{{ route('admin.settings.edit') }}" class="block rounded-xl px-3 py-2 hover:bg-[color:var(--lego-yellow)]">{{ __('messages.settings') }}</a>
                    <a href="{{ route('catalog') }}" class="block rounded-xl px-3 py-2 hover:bg-[color:var(--lego-yellow)]">{{ __('messages.catalog') }}</a>
                </nav>
            </aside>

            <div class="flex-1">
                <header class="border-b border-[color:var(--border)] bg-[color:var(--card)] px-6 py-4 shadow">
                    <div class="flex items-center justify-between gap-4">
                        <div class="text-sm text-[color:var(--muted)]">
                            {{ $breadcrumb ?? 'Admin' }}
                        </div>
                        <button type="button" class="lego-btn lego-btn-secondary lego-back-btn" data-back data-back-fallback="{{ route('admin.dashboard') }}">← Назад</button>
                    </div>
                </header>

                <main class="px-6 py-8">
                    @if (session('toast'))
                        <div class="lego-toast" data-toast>{{ session('toast') }}</div>
                    @endif
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
