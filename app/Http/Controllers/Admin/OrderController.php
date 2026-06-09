<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStatusRequest;
use App\Mail\OrderTrackingMail;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class OrderController extends Controller
{
    private function canSendMailNow(): bool
    {
        $default = (string) config('mail.default', 'smtp');
        if ($default !== 'smtp') {
            return true;
        }

        return filled(config('mail.mailers.smtp.username'))
            && filled(config('mail.mailers.smtp.password'))
            && filled(config('mail.mailers.smtp.host'));
    }

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
        $validated = $request->validated();

        $newTtn = array_key_exists('tracking_number', $validated)
            ? trim((string) $validated['tracking_number'])
            : trim((string) ($order->tracking_number ?? ''));
        $newTtn = $newTtn === '' ? null : $newTtn;

        $oldTtnRaw = $order->tracking_number;
        $oldTtn = ($oldTtnRaw !== null && $oldTtnRaw !== '')
            ? trim((string) $oldTtnRaw)
            : null;

        $order->status = $validated['status'];
        $order->tracking_number = $newTtn;
        $order->tracking_url = null;
        $order->save();

        if ($newTtn !== null && $newTtn !== $oldTtn && $this->canSendMailNow()) {
            $orderId = $order->id;
            dispatch(function () use ($orderId): void {
                $fresh = Order::query()->find($orderId);
                if (! $fresh || ! $fresh->tracking_number) {
                    return;
                }
                $email = $fresh->customerEmail();
                if (! $email) {
                    return;
                }
                try {
                    Mail::to($email)->send(new OrderTrackingMail($fresh));
                } catch (Throwable $e) {
                    Log::warning('Order tracking email failed', [
                        'order_id' => $fresh->id,
                        'email' => $email,
                        'message' => $e->getMessage(),
                    ]);
                }
            })->afterResponse();
        }

        return back()->with('toast', __('messages.order_updated'));
    }
}
