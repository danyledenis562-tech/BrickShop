<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductImage>
 */
class ProductImageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $seed = fake()->unique()->numerify('#####');

        return [
            'product_id' => Product::factory(),
            'path' => "https://picsum.photos/seed/lego-{$seed}/800/800",
            'is_main' => false,
        ];
    }

    public function main(): static
    {
        return $this->state(fn (array $attributes) => ['is_main' => true]);
    }
}
