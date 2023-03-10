<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CartController extends BaseController
{
    /**
     * @var
     */
    private $cart = [];

    /**
     * Load saved cart.
     *
     * @return mixed
     */
    public function loadCart()
    {
        if ($id = $this->request->cookie('cart')) {
            $this->cart = $this->model->find($id);
        } else {
            $this->cart = $this->model->orderByDesc('id')->first();
        }

        $this->cart['user_id'] = $this->cart['user_id'] ?? $this->request->user()->id;

        return json_decode(json_encode($this->cart), true);
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
            if (!isset($tmpCart['items'][$item['id']])) {
                $tmpCart['items'][$item['id']]['id'] = $item['id'];
                $tmpCart['items'][$item['id']]['quantity'] = $item['quantity'];
            } else {
                $tmpCart['items'][$item['id']]['quantity'] += $item['quantity'];
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

        foreach ($this->request->get('items') as $item) {
            if (isset($tmpCart['items'][$item['id']])) {
                $tmpCart['items'][$item['id']]['quantity'] -= $item['quantity'];
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

        foreach ($this->request->get('itemIds') as $itemId) {
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
        $model = isset($this->cart->id) ? $this->cart : $this->model;
        $this->calculateCartPrice($tmpCart);
        $this->save($model, $tmpCart);

        return $this->response($tmpCart);
    }

    /**
     * Calculate cart price.
     *
     * @param $tmpCart
     * @return void
     */
    public function calculateCartPrice(&$tmpCart)
    {
        $tmpCart['total'] = $tmpCart['total'] ?? 0;

        foreach ($tmpCart['items'] as $item) {
            $itemPrice = Product::find($item['id'])->cost * $item['quantity'];
            $tmpCart['total'] +=
                Carbon::now()->timezone('Europe/Moscow')->isWeekend() ?
                    $itemPrice * 0.8 :
                    (
                        $item['quantity'] > 2 ?
                            $itemPrice * 0.85 :
                            (
                                $item['quantity'] > 1 ?
                                    $itemPrice * 0.9 :
                                    $itemPrice
                            )
                    );
        }
    }
}
