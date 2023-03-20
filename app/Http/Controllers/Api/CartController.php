<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $tmpCart = $this->loadCart();
        $items = $request->get(key: 'items');

        foreach ($items as $item) {
            $itemId = $item['id'];

            if (!isset($tmpCart['items'][$itemId])) {
                $tmpCart['items'][$itemId]['id'] = $item['id'];
                $tmpCart['items'][$itemId]['quantity'] = $item['quantity'];
            } else {
                $tmpCart['items'][$itemId]['quantity'] += $item['quantity'];
            }
        }

        return $this->save(request: $request, data: $tmpCart);
    }

    /**
     * Reduce items quantity.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $tmpCart = $this->loadCart();
        $items = $request->get(key: 'items');

        foreach ($items as $item) {
            $itemId = $item['id'];

            if (isset($tmpCart['items'][$itemId])) {
                $tmpCart['items'][$itemId]['quantity'] = $item['quantity'];
            }
        }

        return $this->save(request: $request, data: $tmpCart);
    }

    /**
     * Delete item from cart.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $tmpCart = $this->loadCart();
        $tmpCart['items'] = [];
        $tmpCart['total'] = 0;

        try {
            $this->cart->fill($tmpCart)->save();
            $message = $this->baseName . ' #' . $id . ' deleted successfully.';
            $code = 200;
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            $code = 500;
        }

        return $this->response(payload: $message, status: $code);
    }

    /**
     * Save cart to database.
     *
     * @param Request $request
     * @param Model|null $model
     * @param null $data
     * @return JsonResponse
     */
    public function save(Request $request, Model $model = null, $data = null): JsonResponse
    {
        static::calculateCartPrice($data);
        $data['id'] = $data['user_id'] = $request->user()->id;
        $model = isset($this->cart->id) ? $this->cart : $this->model;

        return parent::save($request, $model, $data);
    }
}
