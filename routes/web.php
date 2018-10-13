<?php

Route::get('/moqups', function () {
    return view('auth.login');
});

Route::get('/concerts/{concert}', 'ConcertController@show');
Route::post('/concerts/{concert}/orders', 'ConcertOrderController@store');
Route::get('/orders/{confirmationNumber}', 'OrderController@show');

Route::get('/login', 'Auth\LoginController@show');
Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout');

Route::get('/backstage/concerts/new', 'ConcertController@create');
