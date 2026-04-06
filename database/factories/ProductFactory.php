<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => \App\Models\Category::factory(),
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'set_number' => fake()->optional(0.85)->numerify('#####'),
            'price' => fake()->randomFloat(2, 10, 500),
            'stock' => fake()->numberBetween(0, 100),
            'age' => fake()->optional(0.7)->numberBetween(4, 16),
            'difficulty' => fake()->randomElement(['easy', 'medium', 'hard']),
            'pieces' => fake()->optional(0.8)->numberBetween(50, 2000),
            'brand' => 'LEGO',
            'series' => null,
            'country' => null,
            'description' => fake()->paragraph(),
            'is_featured' => false,
            'is_active' => true,
            'popularity' => 0,
        ];
    }

    /**
     * @param  int  $count  Number of gallery images (first is main).
     */
    public function withGalleryImages(int $count = 4): static
    {
        return $this->afterCreating(function (\App\Models\Product $product) use ($count) {
            for ($i = 0; $i < $count; $i++) {
                \App\Models\ProductImage::factory()
                    ->for($product)
                    ->create([
                        'is_main' => $i === 0,
                        'path' => 'https://picsum.photos/seed/p'.$product->id.'-'.$i.'/800/800',
                    ]);
            }
        });
    }
}
