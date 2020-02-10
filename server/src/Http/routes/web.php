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
    'Gernzy\Server\Http\controllers\ResetPasswordController@index'
)
    ->name('password.reset.token');


Route::get('shop', function () {
    return view('Gernzy\Server::home');
});

Route::get('cart', function () {
    return view('Gernzy\Server::cart');
});

Route::get('checkout', function () {
    return view('Gernzy\Server::checkout');
});
