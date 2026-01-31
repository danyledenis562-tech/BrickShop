<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStatusRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

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
}
