<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_returns_503_when_secret_not_configured(): void
    {
        config(['shop.test_payment_webhook_secret' => '']);

        $order = Order::factory()->create();

        $this->postJson(route('payments.test.webhook'), [
            'order_id' => $order->id,
        ], [
            'X-Test-Payment-Secret' => 'any',
        ])->assertStatus(503);
    }

    public function test_webhook_returns_403_for_invalid_secret(): void
    {
        config(['shop.test_payment_webhook_secret' => 'correct']);

        $order = Order::factory()->create();

        $this->postJson(route('payments.test.webhook'), [
            'order_id' => $order->id,
        ], [
            'X-Test-Payment-Secret' => 'wrong',
        ])->assertForbidden();
    }

    public function test_webhook_marks_order_paid_when_valid(): void
    {
        config(['shop.test_payment_webhook_secret' => 'test-secret']);

        $order = Order::factory()->create(['status' => OrderStatus::New]);

        $this->postJson(route('payments.test.webhook'), [
            'order_id' => $order->id,
        ], [
            'X-Test-Payment-Secret' => 'test-secret',
        ])->assertOk()->assertJson(['ok' => true]);

        $this->assertSame(OrderStatus::Paid, $order->fresh()->status);
    }

    public function test_webhook_returns_422_when_order_not_new(): void
    {
        config(['shop.test_payment_webhook_secret' => 'test-secret']);

        $order = Order::factory()->create(['status' => OrderStatus::Paid]);

        $this->postJson(route('payments.test.webhook'), [
            'order_id' => $order->id,
        ], [
            'X-Test-Payment-Secret' => 'test-secret',
        ])->assertStatus(422)->assertJson(['ok' => false]);
    }

    public function test_simulate_marks_own_order_paid(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => OrderStatus::New,
        ]);

        $this->actingAs($user)
            ->post(route('checkout.pay-test', $order))
            ->assertRedirect(route('checkout.thanks', $order));

        $this->assertSame(OrderStatus::Paid, $order->fresh()->status);
    }

    public function test_simulate_forbidden_for_other_users_order(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $owner->id,
            'status' => OrderStatus::New,
        ]);

        $this->actingAs($other)
            ->post(route('checkout.pay-test', $order))
            ->assertForbidden();
    }
}
