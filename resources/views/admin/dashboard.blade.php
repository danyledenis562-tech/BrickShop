<x-admin-layout>
    <x-slot name="breadcrumb">Admin / Dashboard</x-slot>

    @php
        $statusLabels = collect(['new', 'paid', 'processing', 'shipped', 'canceled'])
            ->merge($statusCounts->keys())
            ->unique()
            ->values();
        $statusData = $statusLabels->map(fn ($status) => (int) ($statusCounts[$status] ?? 0))->values();
    @endphp

    <div class="admin-card admin-glass p-6">
        <div class="admin-card-header">
            <div>
                <div class="text-sm text-[color:var(--text-muted)]">Огляд магазину</div>
                <h1 class="text-2xl font-extrabold">{{ __('messages.admin_dashboard') }}</h1>
            </div>
            <div class="admin-pill">Live</div>
        </div>
        <div class="grid gap-4 md:grid-cols-4">
            <div class="admin-card admin-metric">
                <div class="admin-metric-label">{{ __('messages.sales_7') }}</div>
                <div class="admin-metric-value">{{ number_format($sales7, 2) }} грн</div>
                <div class="admin-metric-sub">Останні 7 днів</div>
            </div>
            <div class="admin-card admin-metric">
                <div class="admin-metric-label">{{ __('messages.sales_30') }}</div>
                <div class="admin-metric-value">{{ number_format($sales30, 2) }} грн</div>
                <div class="admin-metric-sub">Останні 30 днів</div>
            </div>
            <div class="admin-card admin-metric">
                <div class="admin-metric-label">Замовлень</div>
                <div class="admin-metric-value">{{ $statusCounts->sum() }}</div>
                <div class="admin-metric-sub">Усі статуси</div>
            </div>
            <div class="admin-card admin-metric">
                <div class="admin-metric-label">Активні</div>
                <div class="admin-metric-value">{{ ($statusCounts['new'] ?? 0) + ($statusCounts['paid'] ?? 0) + ($statusCounts['processing'] ?? 0) }}</div>
                <div class="admin-metric-sub">В обробці</div>
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="admin-card admin-glass p-6 lg:col-span-2">
            <div class="admin-card-header">
                <div>
                    <div class="text-sm text-[color:var(--text-muted)]">Продажі по днях</div>
                    <h2 class="text-lg font-semibold">Останні 7 днів</h2>
                </div>
                <span class="admin-pill">Chart</span>
            </div>
            <div class="admin-chart">
                <canvas id="salesDailyChart" height="140"></canvas>
            </div>
        </div>
        <div class="admin-card admin-glass p-6">
            <div class="admin-card-header">
                <div>
                    <div class="text-sm text-[color:var(--text-muted)]">Замовлення</div>
                    <h2 class="text-lg font-semibold">По статусах</h2>
                </div>
                <span class="admin-pill">Status</span>
            </div>
            <div class="admin-chart">
                <canvas id="statusChart" height="220"></canvas>
            </div>
        </div>
    </div>

    <div class="mt-6 admin-card admin-glass p-6">
        <div class="admin-card-header">
            <div>
                <div class="text-sm text-[color:var(--text-muted)]">Продажі по місяцях</div>
                <h2 class="text-lg font-semibold">Останні 12 місяців</h2>
            </div>
            <span class="admin-pill">Revenue</span>
        </div>
        <div class="admin-chart">
            <canvas id="salesMonthlyChart" height="120"></canvas>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="admin-card admin-glass p-6">
            <div class="admin-card-header">
                <h2 class="text-lg font-semibold">{{ __('messages.top_products') }}</h2>
                <span class="admin-pill">Top</span>
            </div>
            <div class="admin-list">
                @foreach ($topProducts as $item)
                    <div class="admin-list-item">
                        <span class="text-sm">{{ $item->product?->name }}</span>
                        <span class="text-sm font-semibold">{{ $item->qty }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="admin-card admin-glass p-6 lg:col-span-2">
            <div class="admin-card-header">
                <h2 class="text-lg font-semibold">{{ __('messages.recent_orders') }}</h2>
                <span class="admin-pill">Останні</span>
            </div>
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.users') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentOrders as $order)
                            @php
                                $badgeMap = [
                                    'new' => 'badge-new',
                                    'paid' => 'badge-paid',
                                    'processing' => 'badge-processing',
                                    'shipped' => 'badge-shipped',
                                    'canceled' => 'badge-canceled',
                                ];
                                $badgeClass = $badgeMap[$order->status] ?? 'badge-processing';
                            @endphp
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->user->email }}</td>
                                <td><span class="admin-badge {{ $badgeClass }}">{{ $order->status }}</span></td>
                                <td>{{ number_format($order->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const dailyLabels = @json($dailyLabels);
        const dailyData = @json($dailyData);
        const monthlyLabels = @json($monthlyLabels);
        const monthlyData = @json($monthlyData);
        const statusLabels = @json($statusLabels);
        const statusData = @json($statusData);

        const gridColor = 'rgba(148, 163, 184, 0.2)';
        const labelColor = '#e2e8f0';

        new Chart(document.getElementById('salesDailyChart'), {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Продажі',
                    data: dailyData,
                    borderColor: '#ffcf00',
                    backgroundColor: 'rgba(255, 207, 0, 0.2)',
                    tension: 0.35,
                    fill: true,
                    pointBackgroundColor: '#e3000b',
                    pointBorderColor: '#ffffff',
                }]
            },
            options: {
                plugins: { legend: { labels: { color: labelColor } } },
                scales: {
                    x: { grid: { color: gridColor }, ticks: { color: labelColor } },
                    y: { grid: { color: gridColor }, ticks: { color: labelColor } }
                }
            }
        });

        new Chart(document.getElementById('salesMonthlyChart'), {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Продажі',
                    data: monthlyData,
                    backgroundColor: 'rgba(0, 85, 191, 0.35)',
                    borderColor: '#0055bf',
                    borderWidth: 1,
                    borderRadius: 10,
                }]
            },
            options: {
                plugins: { legend: { labels: { color: labelColor } } },
                scales: {
                    x: { grid: { color: gridColor }, ticks: { color: labelColor } },
                    y: { grid: { color: gridColor }, ticks: { color: labelColor } }
                }
            }
        });

        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: ['#ffcf00', '#22c55e', '#3b82f6', '#8b5cf6', '#e3000b'],
                    borderColor: 'rgba(15, 23, 42, 0.9)',
                    borderWidth: 2,
                }]
            },
            options: {
                plugins: {
                    legend: { position: 'bottom', labels: { color: labelColor } }
                }
            }
        });
    </script>
</x-admin-layout>
