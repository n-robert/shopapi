<?php

namespace App\Models;

use App\Scopes\AuthUserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShopApi extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $names;

    /**
     * BaseModel constructor.
     *
     * @param  array $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->name = strtolower(
            str_replace('Model', '', class_basename(static::class))
        );
        $this->names = Str::plural($this->name);
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new AuthUserScope());
    }
}
