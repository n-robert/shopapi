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
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'App\Http\Controllers\Api'], function () {
    Route::get('/login', 'BaseController@onUnauthorized')->name('login');
    Route::post('/register', 'AuthController@register');
    Route::post('/login', 'AuthController@login');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/user', 'AuthController@user');
        Route::post('/logout', 'AuthController@logout');
        Route::post('/cart-items/add', 'CartController@addToCart');
        Route::put('/cart-items/remove', 'CartController@removeFromCart');
        Route::delete('/cart-items/delete', 'CartController@deleteFromCart');

        $models = ['product', 'cart', 'order'];

        foreach ($models as $model) {
            $table = Str::plural($model);
            $controller = ucfirst($model) . 'Controller';

            Route::get('/' . $table, $controller . '@index');
            Route::get('/' . $table . '/{id}', $controller . '@show');
            Route::post('/' . $table . '/store', $controller . '@store');
            Route::delete('/' . $table . '/{id}', $controller . '@destroy');

            Route::model($model, 'App\\Models\\' . ucfirst($model));
            Route::put('/' . $table . '/update/{' . $model . '}', $controller . '@update');
        }
    });
});