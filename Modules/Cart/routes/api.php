<?php

use Illuminate\Support\Facades\Route;
use Modules\Cart\App\Http\Controllers\Api\CartController;

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

Route::prefix('v1')->name('user-api.')->group(function () {




    // cart routes
    Route::controller(CartController::class)->name('carts.')->prefix('carts')->group(function () {
        Route::get('', 'viewCart')->name('viewCart');
        Route::post('/add', 'addToCart')->name('addToCart');
        Route::put('/update/{itemId}', 'updateItemQuantity')->name('update');
        Route::delete('/remove/{itemId}', 'removeFromCart')->name('remove');
    });
    // cart routes


});
