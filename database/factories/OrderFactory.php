<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'status' => 'new',
            'total' => fake()->randomFloat(2, 50, 500),
            'full_name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'city' => fake()->city(),
            'address' => fake()->streetAddress(),
            'delivery_type' => 'nova',
            'payment_type' => 'card',
            'note' => null,
            'dont_call' => false,
        ];
    }
}
