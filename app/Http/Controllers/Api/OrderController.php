<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Delivery;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $cartId = $request->get('cart_id');
            $cart = Cart::query()->findOrFail($cartId);

            if (empty($cart->items)) {
                return $this->response(payload: 'Cart is empty. No order is created.');
            }

            $statusId = $request->get('status_id', 3);
            $paymentId = $request->get('payment_id', 1);
            $deliveryId = $request->get('delivery_id', 1);

            $data = [
                'items'       => $cart->items,
                'user_id'     => $cartId,
                'status_id'   => $statusId,
                'payment_id'  => $paymentId,
                'delivery_id' => $deliveryId,
                'total'       => $cart->total,
            ];
            static::calculateOrderTotal($data);

            $result = parent::save(request: $request, data: $data);

            if ($result->getData()->success) {
                Cart::query()->update([
                    'items' => [],
                    'total' => 0,
                ]);
            }
        } catch (\Exception $exception) {
            $result = $this->response(
                payload: [
                    'message' => 'Checkout failed.',
                    'errors'  => $exception->getMessage(),
                ],
                status: 500
            );
        }

        return $result;
    }

    /**
     * Update existing record.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $model = $this->model->find((int)$id);
        $attributes = [
            ...$model->attributesToArray(),
            ...$request->only($model->getFillable()),
        ];
        static::calculateOrderTotal($attributes);

        return parent::save($request, $model, $attributes);
    }
}
