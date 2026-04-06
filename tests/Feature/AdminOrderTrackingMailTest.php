<?php

namespace Tests\Feature;

use App\Mail\OrderTrackingMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminOrderTrackingMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_saving_new_tracking_number_sends_email_to_customer(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['email' => 'buyer@example.com']);
        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'tracking_number' => null,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.orders.show', $order))
            ->put(route('admin.orders.update', $order), [
                'status' => 'shipped',
                'tracking_number' => '59001234567890',
            ])
            ->assertRedirect();

        Mail::assertSent(OrderTrackingMail::class, function (OrderTrackingMail $mail) use ($customer): bool {
            return $mail->hasTo($customer->email);
        });
    }

    public function test_admin_updating_same_tracking_number_does_not_send_duplicate_email(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'tracking_number' => '59001234567890',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.orders.update', $order), [
                'status' => 'shipped',
                'tracking_number' => '59001234567890',
            ])
            ->assertRedirect();

        Mail::assertNothingSent();
    }

    public function test_tracking_email_goes_to_guest_email_when_no_user(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::factory()->create([
            'user_id' => null,
            'guest_email' => 'guest-track@example.com',
            'tracking_number' => null,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.orders.update', $order), [
                'status' => 'processing',
                'tracking_number' => '5900999888777',
            ])
            ->assertRedirect();

        Mail::assertSent(OrderTrackingMail::class, fn (OrderTrackingMail $mail): bool => $mail->hasTo('guest-track@example.com'));
    }
}
