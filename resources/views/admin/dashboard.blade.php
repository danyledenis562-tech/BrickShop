<x-admin-layout>
    <x-slot name="breadcrumb">{{ __('messages.admin') }} / {{ __('messages.admin_dashboard') }}</x-slot>

    @php
        $statusLabels = collect(['new', 'paid', 'processing', 'shipped', 'canceled'])
            ->merge($statusCounts->keys())
            ->unique()
            ->values();
        $statusData = $statusLabels->map(fn ($status) => (int) ($statusCounts[$status] ?? 0))->values();
        $statusChartLabels = $statusLabels->map(
            fn ($status) => __('messages.order_status_' . $status)
        )->values();
    @endphp

    <div class="admin-card admin-glass p-6">
        <div class="admin-card-header">
            <div>
                <div class="text-sm text-[color:var(--text-muted)]">{{ __('messages.store_overview') }}</div>
                <h1 class="text-2xl font-extrabold">{{ __('messages.admin_dashboard') }}</h1>
            </div>
            <div class="admin-pill">{{ __('messages.live') }}</div>
        </div>
        <div class="grid gap-4 md:grid-cols-4">
            <div class="admin-card admin-metric">
                <div class="admin-metric-label">{{ __('messages.sales_7') }}</div>
                <div class="admin-metric-value">{{ number_format($sales7, 2) }} грн</div>
                <div class="admin-metric-sub">{{ __('messages.last_7_days') }}</div>
            </div>
            <div class="admin-card admin-metric">
                <div class="admin-metric-label">{{ __('messages.sales_30') }}</div>
                <div class="admin-metric-value">{{ number_format($sales30, 2) }} грн</div>
                <div class="admin-metric-sub">{{ __('messages.last_30_days') }}</div>
            </div>
            <div class="admin-card admin-metric">
                <div class="admin-metric-label">{{ __('messages.orders_count') }}</div>
                <div class="admin-metric-value">{{ $statusCounts->sum() }}</div>
                <div class="admin-metric-sub">{{ __('messages.all_statuses') }}</div>
            </div>
            <div class="admin-card admin-metric">
                <div class="admin-metric-label">{{ __('messages.active_orders') }}</div>
                <div class="admin-metric-value">{{ ($statusCounts['new'] ?? 0) + ($statusCounts['paid'] ?? 0) + ($statusCounts['processing'] ?? 0) }}</div>
                <div class="admin-metric-sub">{{ __('messages.in_processing') }}</div>
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="admin-card admin-glass p-6 lg:col-span-2">
            <div class="admin-card-header">
                <div>
                    <div class="text-sm text-[color:var(--text-muted)]">{{ __('messages.sales_by_days') }}</div>
                    <h2 class="text-lg font-semibold">{{ __('messages.last_7_days') }}</h2>
                </div>
                <span class="admin-pill">{{ __('messages.chart') }}</span>
            </div>
            <div class="admin-chart">
                <canvas id="salesDailyChart" height="140"></canvas>
            </div>
        </div>
        <div class="admin-card admin-glass p-6">
            <div class="admin-card-header">
                <div>
                    <div class="text-sm text-[color:var(--text-muted)]">{{ __('messages.orders') }}</div>
                    <h2 class="text-lg font-semibold">{{ __('messages.by_statuses') }}</h2>
                </div>
                <span class="admin-pill">{{ __('messages.status') }}</span>
            </div>
            <div class="admin-chart">
                <canvas id="statusChart" height="220"></canvas>
            </div>
        </div>
    </div>

    <div class="mt-6 admin-card admin-glass p-6">
        <div class="admin-card-header">
            <div>
                <div class="text-sm text-[color:var(--text-muted)]">{{ __('messages.sales_by_months') }}</div>
                <h2 class="text-lg font-semibold">{{ __('messages.last_12_months') }}</h2>
            </div>
            <span class="admin-pill">{{ __('messages.revenue') }}</span>
        </div>
        <div class="admin-chart">
            <canvas id="salesMonthlyChart" height="120"></canvas>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="admin-card admin-glass p-6">
            <div class="admin-card-header">
                <h2 class="text-lg font-semibold">{{ __('messages.top_products') }}</h2>
                <span class="admin-pill">{{ __('messages.top') }}</span>
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
                <span class="admin-pill">{{ __('messages.recent') }}</span>
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
                                $statusRaw = $order->status;
                                if ($statusRaw instanceof \BackedEnum) {
                                    $statusKey = (string) $statusRaw->value;
                                } elseif (is_string($statusRaw)) {
                                    $statusKey = $statusRaw;
                                } else {
                                    $statusKey = '';
                                }
                                $badgeMap = [
                                    'new' => 'badge-new',
                                    'paid' => 'badge-paid',
                                    'processing' => 'badge-processing',
                                    'shipped' => 'badge-shipped',
                                    'canceled' => 'badge-canceled',
                                ];
                                $badgeClass = $badgeMap[$statusKey] ?? 'badge-processing';
                            @endphp
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->user->email }}</td>
                                <td><span class="admin-badge {{ $badgeClass }}">{{ $statusKey !== '' ? __('messages.order_status_'.$statusKey) : '—' }}</span></td>
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
        const statusChartLabels = @json($statusChartLabels);
        const statusData = @json($statusData);

        const gridColor = 'rgba(148, 163, 184, 0.2)';
        const labelColor = '#e2e8f0';

        new Chart(document.getElementById('salesDailyChart'), {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: @json(__('messages.sales')),
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
                    label: @json(__('messages.sales')),
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
                labels: statusChartLabels,
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
