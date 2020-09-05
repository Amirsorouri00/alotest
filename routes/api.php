<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::prefix('admin')->group(function () {
        Route::prefix('controll')->group(function () {
            Route::post('create-panel', 'AdminController@create_panel');
            Route::post('add-seller', 'AdminController@add_seller');
        });
    });

    Route::prefix('seller')->group(function () {
        Route::post('add-stuff', 'SellerController@add_stuff');
        Route::post('report', 'SellerController@report');
    });

    Route::prefix('customer')->group(function () {
        Route::post('login', 'CustomerController@login');
        Route::post('list-products', 'CustomerController@list_product');
        Route::post('addto-basket', 'CustomerController@add_to_basket');
        Route::post('rmfrm-basket', 'CustomerController@remove_from_baskeat');
        Route::post('basket-receipt', 'CustomerController@generate_receipt');
        Route::post('buy', 'CustomerController@buy');
    });
});
