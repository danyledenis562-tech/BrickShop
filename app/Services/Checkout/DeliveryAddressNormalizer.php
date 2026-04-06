<?php

namespace App\Services\Checkout;

final class DeliveryAddressNormalizer
{
    public function normalize(array $data): array
    {
        $deliveryType = (string) ($data['delivery_type'] ?? 'nova');

        if ($deliveryType === 'nova') {
            $city = trim((string) ($data['nova_city'] ?? $data['city'] ?? ''));
            $branch = trim((string) ($data['nova_branch'] ?? ''));

            return [
                'city' => $city,
                'address' => __('messages.delivery_nova_branch_format', ['branch' => $branch]),
            ];
        }

        if ($deliveryType === 'ukrposhta') {
            $city = trim((string) ($data['ukrposhta_city'] ?? $data['city'] ?? ''));
            $branch = trim((string) ($data['ukrposhta_branch'] ?? ''));

            return [
                'city' => $city,
                'address' => __('messages.delivery_ukrposhta_branch_format', ['branch' => $branch]),
            ];
        }

        $city = trim((string) ($data['courier_city'] ?? $data['city'] ?? ''));
        $street = trim((string) ($data['courier_street'] ?? ''));
        $house = trim((string) ($data['courier_house'] ?? ''));
        $apartment = trim((string) ($data['courier_apartment'] ?? ''));

        return [
            'city' => $city,
            'address' => __('messages.delivery_courier_address_format', [
                'street' => $street,
                'house' => $house,
                'apartment' => $apartment,
            ]),
        ];
    }
}
