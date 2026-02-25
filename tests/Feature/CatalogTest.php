<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_page_loads(): void
    {
        $this->get(route('catalog'))->assertStatus(200);
    }

    public function test_catalog_shows_products(): void
    {
        $product = Product::factory()->create(['name' => 'Unique LEGO Set', 'slug' => 'unique-lego-set', 'is_active' => true]);

        $this->get(route('catalog'))
            ->assertStatus(200)
            ->assertSeeText('Unique LEGO Set');
    }

    public function test_product_show_page_loads(): void
    {
        $product = Product::factory()->create(['slug' => 'test-product']);

        $this->get(route('product.show', $product))
            ->assertStatus(200)
            ->assertSeeText($product->name);
    }
}
