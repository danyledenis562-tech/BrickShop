<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStatusRequest;
use App\Mail\OrderTrackingMail;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse as SymfonyStreamedResponse;
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

        $filename = 'orders-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($orders) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($out, [
                'ID',
                __('messages.date'),
                __('messages.email'),
                __('messages.guest_email'),
                __('messages.full_name'),
                __('messages.phone'),
                __('messages.city'),
                __('messages.address'),
                __('messages.dont_call_confirm'),
                __('messages.status'),
                __('messages.total'),
                __('messages.discount'),
                __('messages.tracking_number'),
                'items_count',
            ]);
            foreach ($orders as $order) {
                fputcsv($out, [
                    $order->id,
                    $order->created_at->format('Y-m-d H:i'),
                    $order->user?->email ?? '',
                    $order->guest_email ?? '',
                    $order->full_name,
                    $order->phone,
                    $order->city ?? '',
                    $order->address ?? '',
                    $order->dont_call ? '1' : '0',
                    $order->status->value,
                    $order->total,
                    $order->discount_amount ?? 0,
                    $order->tracking_number ?? '',
                    $order->items->count(),
                ]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
