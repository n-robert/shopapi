<?php

namespace App\Models;

class Product extends ShopApi
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'cost',
        'quantity',
        'description',
    ];

    /**
     * Save the model to the database.
     *
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        if (!isset($this->attributes['slug'])) {
            $nextId = static::query()->max('id') + 1;
            $this->setAttribute('slug', 'product-' . $nextId);
        }

        return parent::save($options);
    }
}
