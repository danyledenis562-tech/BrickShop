<?php

namespace App\Services\Cart;

use App\Models\CartReminder;
use Illuminate\Http\Request;

final class CartReminderService
{
    public function syncFromSessionCart(Request $request, CartService $cartService): void
    {
        $user = $request->user();
        if (! $user) {
            return;
        }

        $lines = $cartService->getLines($request);
        if ($lines === []) {
            CartReminder::query()->where('user_id', $user->id)->delete();

            return;
        }

        $payload = array_map(fn (array $line) => [
            'name' => $line['name'],
            'quantity' => $line['quantity'],
        ], $lines);

        CartReminder::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'cart_json' => json_encode($payload, JSON_THROW_ON_ERROR),
                'reminder_sent_at' => null,
            ]
        );
    }

    public function clearForUserId(int $userId): void
    {
        CartReminder::query()->where('user_id', $userId)->delete();
    }
}
