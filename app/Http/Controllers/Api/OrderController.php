<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Delivery;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class OrderController extends ShopApiController
{
    /**
     * Calculate cart price.
     *
     * @param $tmpOrder
     * @return void
     */
    public static function calculateOrderTotal(&$tmpOrder): void
    {
        $paymentId = $tmpOrder['payment_id'];
        $deliveryId = $tmpOrder['delivery_id'];
        $payment = Payment::query()->findOrFail($paymentId);
        $delivery = Delivery::query()->findOrFail($deliveryId);

        CartController::calculateCartPrice($tmpOrder);
        $tmpOrder['total'] += $payment->cost + $delivery->cost;
    }

    /**
     * Store new record.
     * @return JsonResponse
     */
    public function store(): JsonResponse
    {
        $cartId = $this->request->get('cart_id');
        $statusId = $this->request->get('status_id', 3);
        $paymentId = $this->request->get('payment_id', 1);
        $deliveryId = $this->request->get('delivery_id', 1);
        $cart = Cart::query()->findOrFail($cartId);

        $data = [
            'items'       => $cart->items,
            'user_id'     => $cartId,
            'status_id'   => $statusId,
            'payment_id'  => $paymentId,
            'delivery_id' => $deliveryId,
            'total'       => $cart->total,
        ];
        static::calculateOrderTotal($data);

        $result = parent::save(data: $data);

        if ($result->getData()->success) {
            Cart::query()->update([
                'items' => [],
                'total' => 0,
            ]);
        }

        return $result;
    }

    /**
     * Update existing record.
     *
     * @param Model $model
     * @return JsonResponse
     */
    public function update(Model $model): JsonResponse
    {
        $attributes = [
            ...$model->attributesToArray(),
            ...$this->request->only($model->getFillable()),
        ];
        static::calculateOrderTotal($attributes);

        return parent::save($model, $attributes);
    }
}
