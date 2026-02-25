<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStatusRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse as SymfonyStreamedResponse;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()
            ->with('user')
            ->when(request('status'), fn ($q) => $q->where('status', request('status')))
            ->when(request('user'), fn ($q) => $q->whereHas('user', fn ($u) => $u->where('email', 'like', '%'.request('user').'%')))
            ->when(request('date'), fn ($q) => $q->whereDate('created_at', request('date')))
            ->latest()
            ->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load('user', 'items.product');

        return view('admin.orders.show', compact('order'));
    }

    public function update(OrderStatusRequest $request, Order $order): RedirectResponse
    {
        $order->update(['status' => $request->validated()['status']]);

        return back()->with('toast', __('messages.order_updated'));
    }

    public function export(Request $request): SymfonyStreamedResponse
    {
        $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $orders = Order::query()
            ->with('user', 'items.product')
            ->when($dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->orderBy('created_at')
            ->get();

        $filename = 'orders-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($orders) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
            fputcsv($out, [
                'ID',
                __('messages.date'),
                __('messages.email'),
                __('messages.full_name'),
                __('messages.phone'),
                __('messages.status'),
                __('messages.total'),
                __('messages.discount'),
                'items_count',
            ]);
            foreach ($orders as $order) {
                fputcsv($out, [
                    $order->id,
                    $order->created_at->format('Y-m-d H:i'),
                    $order->user?->email ?? '',
                    $order->full_name,
                    $order->phone,
                    $order->status,
                    $order->total,
                    $order->discount_amount ?? 0,
                    $order->items->count(),
                ]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
