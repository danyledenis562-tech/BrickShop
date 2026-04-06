<x-mail::message>
# {{ __('messages.mail_order_heading', ['id' => $order->id]) }}

{{ __('messages.mail_order_intro') }}

@foreach ($order->items as $item)
{{ $item->product->name }} × {{ $item->quantity }} — {{ number_format((float) $item->total, 2) }} {{ __('messages.currency_uah') }}

@endforeach

@if ($order->discount_amount > 0)
{{ __('messages.discount') }}: −{{ number_format((float) $order->discount_amount, 2) }} {{ __('messages.currency_uah') }}

@endif
**{{ __('messages.total') }}:** {{ number_format((float) $order->total, 2) }} {{ __('messages.currency_uah') }}

<x-mail::button :url="route('catalog')">
{{ __('messages.back_to_catalog') }}
</x-mail::button>

{{ __('messages.mail_order_footer') }}
</x-mail::message>
