@php
    $navGroups = [
        [
            'title' => 'Shop',
            'items' => [
                ['label' => __('messages.admin_dashboard'), 'route' => 'admin.dashboard', 'icon' => 'dashboard'],
                ['label' => __('messages.products'), 'route' => 'admin.products.index', 'icon' => 'products'],
                ['label' => __('messages.categories'), 'route' => 'admin.categories.index', 'icon' => 'categories'],
                ['label' => __('messages.orders'), 'route' => 'admin.orders.index', 'icon' => 'orders'],
            ],
        ],
        [
            'title' => 'Content',
            'items' => [
                ['label' => __('messages.banners'), 'route' => 'admin.banners.index', 'icon' => 'banners'],
                ['label' => __('messages.reviews'), 'route' => 'admin.reviews.index', 'icon' => 'reviews'],
            ],
        ],
        [
            'title' => 'System',
            'items' => [
                ['label' => __('messages.users'), 'route' => 'admin.users.index', 'icon' => 'users'],
                ['label' => __('messages.settings'), 'route' => 'admin.settings.edit', 'icon' => 'settings'],
            ],
        ],
    ];

    $icons = [
        'dashboard' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 13h8V3H3v10zm10 8h8V11h-8v10zM3 21h8v-6H3v6zm10-8h8V3h-8v10z"/></svg>',
        'products' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8l-9-5-9 5v8l9 5 9-5z"/><path d="M3.3 7.5L12 12l8.7-4.5"/></svg>',
        'categories' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z"/></svg>',
        'orders' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h12"/></svg>',
        'banners' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/><path d="M4 8h16"/></svg>',
        'reviews' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
        'users' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21c0-3.314-3.134-6-7-6s-7 2.686-7 6"/><circle cx="12" cy="8" r="4"/></svg>',
        'settings' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9c0 .66.38 1.26 1 1.51.34.14.73.2 1.12.2H21a2 2 0 1 1 0 4h-.09c-.39 0-.78.06-1.12.2z"/></svg>',
    ];
@endphp

<aside class="admin-sidebar">
    <div class="admin-logo">
        <span class="admin-logo-badge">B</span>
        Brick Admin
    </div>

    <nav class="admin-nav">
        @foreach ($navGroups as $group)
            <div>
                <div class="admin-nav-group-title">{{ $group['title'] }}</div>
                <div class="space-y-1">
                    @foreach ($group['items'] as $item)
                        @php
                            $isActive = request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route']));
                        @endphp
                        <a href="{{ route($item['route']) }}" class="admin-nav-item {{ $isActive ? 'is-active' : '' }}">
                            <span class="admin-nav-icon">{!! $icons[$item['icon']] !!}</span>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>
</aside>
