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
//Route::get('/test', 'App\Http\Controllers\TelegramFootbotController@test');
//Route::get('/' . env('TELEGRAM_FOOTBOT_TOKEN') . '/set-webhook',
//           'App\Http\Controllers\TelegramFootbotController@setWebHook');
//Route::match(['get', 'post'], '/' . env('TELEGRAM_FOOTBOT_TOKEN') . '/webhook',
//             'App\Http\Controllers\TelegramFootbotController@handleUpdate');
//
//Route::get('/' . env('TELEGRAM_ROBERTBOT_TOKEN') . '/set-webhook',
//           'App\Http\Controllers\TelegramController@setWebHook');
//Route::match(['get', 'post'], '/' . env('TELEGRAM_ROBERTBOT_TOKEN') . '/webhook',
//             'App\Http\Controllers\TelegramController@handleUpdate');

Route::get('/test', 'App\Http\Controllers\SouschefController@test');
Route::get('/' . env('TELEGRAM_SOUSCHEFBOT_TOKEN') . '/set-webhook',
    'App\Http\Controllers\SouschefController@setWebHook');
Route::match(['get', 'post'], '/' . env('TELEGRAM_SOUSCHEFBOT_TOKEN') . '/webhook',
    'App\Http\Controllers\SouschefController@handleUpdate');
