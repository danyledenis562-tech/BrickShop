<x-mail::message>
# {{ __('messages.mail_tracking_heading', ['id' => $order->id]) }}

{{ __('messages.mail_tracking_intro', ['name' => $order->full_name]) }}

<x-mail::panel>
**{{ __('messages.mail_tracking_ttn_label') }}**  
<span style="font-size:1.1em;letter-spacing:0.02em;">{{ $order->tracking_number }}</span>
</x-mail::panel>

{{ __('messages.mail_tracking_outro') }}

<x-mail::button :url="config('app.url')">
{{ __('messages.mail_tracking_shop_button') }}
</x-mail::button>

{{ __('messages.mail_order_footer') }}
</x-mail::message>
