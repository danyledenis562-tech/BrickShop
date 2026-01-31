@props(['breadcrumb' => 'Admin'])

<header class="admin-topbar">
    <div class="admin-topbar-inner">
        <div class="admin-breadcrumbs">{{ $breadcrumb }}</div>
        <div class="admin-top-actions">
            <a href="{{ route('welcome') }}" class="admin-pill" title="На сайт">
                <span>↗</span>
                На сайт
            </a>
            <details class="relative">
                <summary class="admin-avatar cursor-pointer">A</summary>
                <div class="absolute right-0 mt-3 w-40 rounded-2xl border border-[color:var(--border-soft)] bg-[color:var(--bg-card)] p-2 shadow-lg">
                    <a href="{{ route('profile.index') }}" class="block rounded-xl px-3 py-2 text-sm hover:bg-[color:var(--bg-hover)]">Профіль</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left rounded-xl px-3 py-2 text-sm hover:bg-[color:var(--bg-hover)]">Вийти</button>
                    </form>
                </div>
            </details>
        </div>
    </div>
</header>
