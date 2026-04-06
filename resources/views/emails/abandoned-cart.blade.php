<x-mail::message>
# {{ __('messages.abandoned_cart_heading') }}

{{ __('messages.abandoned_cart_intro') }}

@foreach ($lines as $line)
- {{ $line['name'] }} × {{ $line['quantity'] }}
@endforeach

<x-mail::button :url="route('cart.index')">
{{ __('messages.cart') }}
</x-mail::button>

{{ __('messages.abandoned_cart_footer') }}
</x-mail::message>
