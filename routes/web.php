<?php

Route::get('/moqups', function () {
    return view('moqups');
});

Route::get('/concerts/{concert}', 'ConcertController@show');
Route::post('/concerts/{concert}/orders', 'ConcertOrderController@store');
