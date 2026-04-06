<?php

namespace Tests\Feature;

use App\Models\Product;
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
        $product = Product::factory()->create(['slug' => 'test-set', 'stock' => 10]);

        $this->post(route('cart.add', $product))
            ->assertRedirect()
            ->assertSessionHas('toast')
            ->assertSessionHas('cart');
    }

    public function test_add_product_to_cart_redirects_to_checkout_when_requested(): void
    {
        $product = Product::factory()->create(['slug' => 'test-set', 'stock' => 10]);

        $this->post(route('cart.add', $product), ['redirect' => 'checkout'])
            ->assertRedirect(route('checkout.index'));
    }

    public function test_update_cart_quantity(): void
    {
        $product = Product::factory()->create(['slug' => 'test-set', 'stock' => 10]);
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

    public function test_cannot_add_out_of_stock_product_to_cart(): void
    {
        $product = Product::factory()->create(['slug' => 'oos-set', 'stock' => 0]);

        $this->post(route('cart.add', $product))
            ->assertRedirect()
            ->assertSessionHas('toast', __('messages.cannot_add_out_of_stock'));

        $this->assertEmpty(session('cart', []));
    }
}
