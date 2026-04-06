<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $paymentTypes = ['card', 'cash'];
        if (filled(config('shop.liqpay.public_key')) && filled(config('shop.liqpay.private_key'))) {
            $paymentTypes[] = 'liqpay';
        }

        $guestEmailRules = auth()->check()
            ? ['nullable', 'email', 'max:255']
            : ['required', 'email', 'max:255'];

        return [
            'guest_email' => $guestEmailRules,
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'delivery_type' => ['required', 'in:nova,courier,ukrposhta'],
            'payment_type' => ['required', Rule::in($paymentTypes)],
            'note' => ['nullable', 'string', 'max:500'],
            'promo_code' => ['nullable', 'string', 'max:50'],
            'bonus_to_spend' => ['nullable', 'integer', 'min:0'],
            'card_number' => ['nullable', 'string', 'max:25'],
            'card_expiry' => ['nullable', 'string', 'max:5'],
            'card_cvv' => ['nullable', 'string', 'max:4'],
            'nova_city' => ['nullable', 'string', 'max:100', 'required_if:delivery_type,nova'],
            'nova_city_ref' => ['nullable', 'string', 'max:50'],
            'nova_branch' => ['nullable', 'string', 'max:255', 'required_if:delivery_type,nova'],
            'ukrposhta_city' => ['nullable', 'string', 'max:100', 'required_if:delivery_type,ukrposhta'],
            'ukrposhta_branch' => ['nullable', 'string', 'max:255', 'required_if:delivery_type,ukrposhta'],
            'courier_city' => ['nullable', 'string', 'max:100', 'required_if:delivery_type,courier'],
            'courier_street' => ['nullable', 'string', 'max:120', 'required_if:delivery_type,courier'],
            'courier_house' => ['nullable', 'string', 'max:20', 'required_if:delivery_type,courier'],
            'courier_apartment' => ['nullable', 'string', 'max:20', 'required_if:delivery_type,courier'],
        ];
    }
}
