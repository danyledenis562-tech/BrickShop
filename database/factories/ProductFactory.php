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
}
