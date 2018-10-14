<?php

Route::get('/moqups', function () {
    return view('auth.login');
});

Route::get('/concerts/{concert}', 'ConcertController@show')->name('concerts.show');
Route::post('/concerts/{concert}/orders', 'ConcertOrderController@store');
Route::get('/orders/{confirmationNumber}', 'OrderController@show');

Route::get('/login', 'Auth\LoginController@show')->name('auth.login');
Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout')->name('auth.logout');

Route::group(['middleware' => 'auth', 'prefix' => 'backstage', 'namespace' => 'Backstage'], function () {
    Route::get('/concerts', 'ConcertController@index');
    Route::get('/concerts/new', 'ConcertController@create');
    Route::post('/concerts', 'ConcertController@store');
    Route::get('/concerts/{id}/edit', 'ConcertController@edit');
});
