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
    Route::get('/concerts', 'ConcertController@index')->name('backstage.concerts.index');
    Route::get('/concerts/new', 'ConcertController@create')->name('backstage.concerts.create');
    Route::post('/concerts', 'ConcertController@store')->name('backstage.concerts.store');
    Route::get('/concerts/{id}/edit', 'ConcertController@edit')->name('backstage.concerts.edit');
    Route::patch('/concerts/{id}', 'ConcertController@patch')->name('backstage.concerts.patch');
});
