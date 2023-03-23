<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class AuthUserScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
//        if (Auth::user()->is_admin) {
//            return;
//        }

        if ($model->name != 'product') {
            $builder->where('user_id', Auth::id());
        }

        if ($model->name == 'cart') {
            $builder->where('status_id', 2);
        }
    }
}
