<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_index_loads_empty(): void
    {
        $this->get(route('cart.index'))->assertStatus(200);
    }

    public function test_add_product_to_cart_redirects_back_with_toast(): void
    {
        $product = Product::factory()->create(['slug' => 'test-set']);

        $this->post(route('cart.add', $product))
            ->assertRedirect()
            ->assertSessionHas('toast')
            ->assertSessionHas('cart');
    }

    public function test_add_product_to_cart_redirects_to_checkout_when_requested(): void
    {
        $product = Product::factory()->create(['slug' => 'test-set']);

        $this->post(route('cart.add', $product), ['redirect' => 'checkout'])
            ->assertRedirect(route('checkout.index'));
    }

    public function test_update_cart_quantity(): void
    {
        $product = Product::factory()->create(['slug' => 'test-set']);
        $this->post(route('cart.add', $product));
        $this->patch(route('cart.update', $product), ['quantity' => 3])
            ->assertRedirect()
            ->assertSessionHas('toast');

        $cart = session('cart');
        $this->assertSame(3, $cart[(string) $product->id]['quantity']);
    }

    public function test_remove_from_cart(): void
    {
        $product = Product::factory()->create(['slug' => 'test-set']);
        $this->post(route('cart.add', $product));
        $this->delete(route('cart.remove', $product))
            ->assertRedirect()
            ->assertSessionHas('toast');

        $cart = session('cart');
        $this->assertArrayNotHasKey((string) $product->id, $cart ?? []);
    }
}
