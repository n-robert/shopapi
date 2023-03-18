<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class CartController extends ShopApiController
{
    /**
     * Calculate cart price.
     *
     * @param $tmpCart
     * @return void
     */
    public static function calculateCartPrice(&$tmpCart): void
    {
        $tmpCart['total'] = 0;

        array_walk($tmpCart['items'], function (&$item) use (&$tmpCart) {
            $itemPrice = Product::find($item['id'])->cost * $item['quantity'];
            $tmpCart['total'] += Carbon::now()->timezone('Europe/Moscow')->isWeekend() ? $itemPrice * 0.8
                : ($item['quantity'] > 2 ? $itemPrice * 0.85
                    : ($item['quantity'] > 1 ? $itemPrice * 0.9
                        : $itemPrice));
        });
    }

    /**
     * @var
     */
    private $cart = [];

    /**
     * Load saved cart.
     *
     * @return array
     */
    public function loadCart(): array
    {
        $this->cart = $this->model->orderByDesc('id')->firstOrNew();

        return json_decode(json_encode(value: $this->cart, flags: JSON_NUMERIC_CHECK), associative: true);
    }

    /**
     * Add new items/increase items quantity.
     *
     * @return JsonResponse
     */
    public function addToCart(): JsonResponse
    {
        $tmpCart = $this->loadCart();

        foreach ($this->request->get('items') as $item) {
            $itemId = $item['id'];

            if (!isset($tmpCart['items'][$itemId])) {
                $tmpCart['items'][$itemId]['id'] = $item['id'];
                $tmpCart['items'][$itemId]['quantity'] = $item['quantity'];
            } else {
                $tmpCart['items'][$itemId]['quantity'] += $item['quantity'];
            }
        }

        return $this->saveCart($tmpCart);
    }

    /**
     * Reduce items quantity.
     *
     * @return JsonResponse
     */
    public function removeFromCart(): JsonResponse
    {
        $tmpCart = $this->loadCart();

        foreach ($this->request->get(key: 'items') as $item) {
            $itemId = $item['id'];

            if (isset($tmpCart['items'][$itemId])) {
                $tmpCart['items'][$itemId]['quantity'] -= $item['quantity'];
            }
        }

        return $this->saveCart($tmpCart);
    }

    /**
     * Delete item from cart.
     *
     * @return JsonResponse
     */
    public function deleteFromCart(): JsonResponse
    {
        $tmpCart = $this->loadCart();

        foreach ($this->request->get(key: 'itemIds') as $itemId) {
            if (isset($tmpCart['items'][$itemId])) {
                unset($tmpCart['items'][$itemId]);
            }
        }

        return $this->saveCart($tmpCart);
    }

    /**
     * Save cart to database.
     *
     * @param $tmpCart
     * @return JsonResponse
     */
    public function saveCart($tmpCart): JsonResponse
    {
        static::calculateCartPrice($tmpCart);
        $tmpCart['id'] = $tmpCart['user_id'] = $this->request->user()->id;
        $model = isset($this->cart->id) ? $this->cart : $this->model;
        $this->save($model, $tmpCart);

        return $this->response(payload: $tmpCart);
    }
}
