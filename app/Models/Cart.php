<?php

namespace App\Models;

class Cart extends ShopApi
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'items',
        'type',
        'status_id',
        'user_id',
        'total',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'items' => 'array',
        'total' => 'float',
    ];
}
