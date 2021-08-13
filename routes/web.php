<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/set-webhook', 'App\Http\Controllers\TelegramController@setWebHook');
Route::post('/' . env('TELEGRAM_WEBHOOK_URL'), 'App\Http\Controllers\TelegramController@handleUpdate');