<?php

namespace Database\Factories;

use App\Http\Controllers\Api\CartController;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Tests\Feature\ProductControllerTest;

/**
 * @extends Factory<Cart>
 */
class CartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        if (!$products = Product::query()->take(ProductControllerTest::$itemCount)->get()->all()) {
            $products = Product::factory(ProductControllerTest::$itemCount)->create()->all();
        }

        $items = [];
        $id = Auth::id();

        array_map(function ($product) use (&$items) {
            $items[$product->id] = [
                'id'       => $product->id,
                'quantity' => fake()->randomFloat(0, 1, $product->quantity),
            ];
        }, $products);

        $tmmCart = [
            'id'      => $id,
            'user_id' => $id,
            'items'   => $items,
        ];
        CartController::calculateCartPrice($tmmCart);

        return $tmmCart;
    }
}
