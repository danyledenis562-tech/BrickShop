<?php

namespace App\Console\Commands;

use App\Mail\AbandonedCartMail;
use App\Models\CartReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAbandonedCartReminders extends Command
{
    protected $signature = 'shop:send-abandoned-cart-reminders';

    protected $description = 'Email authenticated users who left items in the cart for 24+ hours';

    public function handle(): int
    {
        $threshold = now()->subHours(24);

        CartReminder::query()
            ->with('user')
            ->whereNull('reminder_sent_at')
            ->where('updated_at', '<=', $threshold)
            ->chunkById(50, function ($reminders): void {
                foreach ($reminders as $reminder) {
                    $user = $reminder->user;
                    if (! $user || ! $user->email) {
                        continue;
                    }

                    try {
                        $lines = json_decode($reminder->cart_json, true, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException) {
                        $reminder->delete();

                        continue;
                    }

                    if (! is_array($lines) || $lines === []) {
                        $reminder->delete();

                        continue;
                    }

                    Mail::to($user->email)->send(new AbandonedCartMail($user, $lines));
                    $reminder->forceFill(['reminder_sent_at' => now()])->save();
                }
            });

        return self::SUCCESS;
    }
}
