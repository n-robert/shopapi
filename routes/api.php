<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['namespace' => 'App\Http\Controllers\Api'], function () {
    Route::get('/login', 'ShopApiController@onUnauthorized')->name('login');
    Route::post('/register', 'AuthController@register');
    Route::post('/login', 'AuthController@login');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/user', 'AuthController@user');
        Route::post('/logout', 'AuthController@logout');
        Route::post('/cart-items', 'CartController@addToCart');
        Route::put('/cart-items', 'CartController@removeFromCart');
        Route::delete('/cart-items', 'CartController@deleteFromCart');

        $models = ['product', 'status', 'cart', 'order', 'payment', 'delivery'];

        foreach ($models as $model) {
            $table = Str::plural($model);
            $controller = ucfirst($model) . 'Controller';

            if ($model != 'cart') {
                Route::get('/' . $table, $controller . '@index');
                Route::post('/' . $table, $controller . '@store');
            }

            Route::get('/' . $table . '/{id}', $controller . '@show');
            Route::delete('/' . $table . '/{id}', $controller . '@destroy');

            Route::model($model, 'App\\Models\\' . ucfirst($model));
            Route::put('/' . $table . '/{' . $model . '}', $controller . '@update');
        }
    });
});
