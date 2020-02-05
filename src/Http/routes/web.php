<?php

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

// Password Reset Route
Route::get(
    'password/reset/{token}',
    'Lab19\Cart\Http\controllers\ResetPasswordController@index'
)
    ->name('password.reset.token');


Route::get('shop', function () {
    return view('lab19\cart::home');
});

Route::get('cart', function () {
    return view('lab19\cart::cart');
});

Route::get('checkout', function () {
    return view('lab19\cart::checkout');
});
