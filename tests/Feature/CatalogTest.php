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

    public function test_catalog_search_matches_partial_case_insensitive(): void
    {
        Product::factory()->create([
            'name' => 'Amazing Technic Crane',
            'slug' => 'amazing-technic-crane',
            'is_active' => true,
        ]);

        $this->get(route('catalog', ['search' => 'technic']))
            ->assertOk()
            ->assertSeeText('Amazing Technic Crane');

        $this->get(route('catalog', ['search' => 'CRANE']))
            ->assertOk()
            ->assertSeeText('Amazing Technic Crane');
    }

    public function test_product_show_page_loads(): void
    {
        $product = Product::factory()->create(['slug' => 'test-product']);

        $this->get(route('product.show', $product))
            ->assertStatus(200)
            ->assertSeeText($product->name);
    }

    public function test_product_show_renders_gallery_thumbnails_when_multiple_images(): void
    {
        $product = Product::factory()
            ->withGalleryImages(4)
            ->create(['slug' => 'gallery-product', 'stock' => 5]);

        $this->get(route('product.show', $product))
            ->assertStatus(200)
            ->assertSee('data-gallery-thumb')
            ->assertSee('data-gallery-main');
    }
}
