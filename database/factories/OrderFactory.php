<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
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
        $cart = Cart::query()->first() ?: Cart::factory()->create();

        if (!$cart['items']) {
            $tmpCart = Cart::factory()->make()->toArray();
            $cart['items'] = $tmpCart['items'];
            $cart['total'] = $tmpCart['total'];
        }

        return ['cart_id' => $cart['id']];
    }
}
