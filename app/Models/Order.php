<?php

namespace App\Models;

class Order extends Base
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'status_id',
        'payment_id',
        'delivery_id',
        'items',
        'total',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'items' => 'array',
    ];

    /**
     * Get details of order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
