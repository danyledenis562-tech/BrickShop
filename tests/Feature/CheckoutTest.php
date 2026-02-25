<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_index_redirects_to_cart_when_empty(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('checkout.index'))
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('toast');
    }

    public function test_checkout_index_shows_form_when_cart_has_items(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['slug' => 'test-set', 'price' => 100]);

        $this->actingAs($user)->post(route('cart.add', $product));

        $this->actingAs($user)
            ->get(route('checkout.index'))
            ->assertStatus(200)
            ->assertSeeText($product->name);
    }

    public function test_checkout_store_creates_order_and_clears_cart(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['slug' => 'test-set', 'price' => 50]);

        $this->actingAs($user)->post(route('cart.add', $product));

        $this->actingAs($user)
            ->post(route('checkout.store'), [
                'full_name' => 'Test User',
                'phone' => '+380501234567',
                'city' => 'Kyiv',
                'address' => 'Street 1',
                'delivery_type' => 'nova_poshta',
                'payment_type' => 'card',
                'note' => null,
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('orders', 1);
        $order = Order::first();
        $this->assertSame($user->id, $order->user_id);
        $this->assertSame('new', $order->status);
        $this->assertSame(50.0, (float) $order->total);
        $this->assertDatabaseCount('order_items', 1);
        $this->assertEmpty(session('cart'));
    }

    public function test_checkout_thanks_only_for_own_order(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $other->id]);

        $this->actingAs($user)->get(route('checkout.thanks', $order))->assertStatus(403);
    }
}
