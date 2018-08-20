<?php

Route::get('/concerts/{concert}', 'ConcertController@show');
Route::post('/concerts/{concert}/orders', 'ConcertOrderController@store');