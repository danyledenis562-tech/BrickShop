@section('title', __('messages.checkout').' — LiqPay')
<x-app-layout>
    <div class="mx-auto max-w-lg px-4 py-16 text-center">
        <p class="text-sm text-[color:var(--muted)]">{{ __('messages.liqpay_redirect_hint') }}</p>
        <form id="liqpay-form" method="POST" action="{{ $checkout['url'] }}" class="mt-6">
            <input type="hidden" name="data" value="{{ $checkout['data'] }}">
            <input type="hidden" name="signature" value="{{ $checkout['signature'] }}">
            <noscript>
                <button type="submit" class="lego-btn lego-btn-primary">{{ __('messages.liqpay_pay_button') }}</button>
            </noscript>
        </form>
        <script>
            document.getElementById('liqpay-form').submit();
        </script>
    </div>
</x-app-layout>
