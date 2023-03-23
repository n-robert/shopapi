<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

/**
 * @extends Factory<Product>
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
        return [
            'title' => fake()->words(asText: true),
            'cost' => fake()->randomFloat(2, 10, 30),
            'quantity' => fake()->randomNumber(nbDigits: 2),
            'description' => fake()->sentence(),
        ];
    }
}
