<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $statusCounts = Order::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $sales7 = Order::query()->where('created_at', '>=', now()->subDays(7))->sum('total');
        $sales30 = Order::query()->where('created_at', '>=', now()->subDays(30))->sum('total');

        $dailySalesRaw = Order::query()
            ->selectRaw('DATE(created_at) as day, SUM(total) as total')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $dailyLabels = collect(range(6, 0))
            ->map(fn ($offset) => now()->subDays($offset)->format('d.m'))
            ->values();

        $dailyData = collect(range(6, 0))
            ->map(function ($offset) use ($dailySalesRaw) {
                $key = now()->subDays($offset)->format('Y-m-d');
                return (float) ($dailySalesRaw[$key] ?? 0);
            })
            ->values();

        $monthlySalesRaw = Order::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total) as total")
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $monthlyPeriods = collect(range(11, 0))
            ->map(fn ($offset) => now()->subMonths($offset)->startOfMonth());

        $monthlyLabels = $monthlyPeriods->map(fn (Carbon $date) => $date->format('M Y'))->values();
        $monthlyData = $monthlyPeriods
            ->map(fn (Carbon $date) => (float) ($monthlySalesRaw[$date->format('Y-m')] ?? 0))
            ->values();

        $topProducts = OrderItem::query()
            ->select('product_id', DB::raw('sum(quantity) as qty'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('qty')
            ->take(5)
            ->get();

        $recentOrders = Order::query()->with('user')->latest()->take(10)->get();

        return view('admin.dashboard', compact(
            'statusCounts',
            'sales7',
            'sales30',
            'topProducts',
            'recentOrders',
            'dailyLabels',
            'dailyData',
            'monthlyLabels',
            'monthlyData'
        ));
    }
}
