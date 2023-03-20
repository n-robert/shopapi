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

        $models = ['product', 'status', 'cart', 'order', 'payment', 'delivery'];
        array_map(
            function ($model) {
                $table = Str::plural($model);
                $controller = ucfirst($model) . 'Controller';
                Route::apiResource(name: $table, controller: $controller);
            },
            $models
        );
    });
});
