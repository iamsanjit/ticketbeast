<?php

use Faker\Generator as Faker;
use App\Concert;
use Carbon\Carbon;

$factory->define(App\Ticket::class, function (Faker $faker) {
    return [
        'concert_id' => function () {
            return factory(Concert::class)->create();
        }
    ];
});

$factory->state(App\Ticket::class, 'reserved', function (Faker $faker) {
    return [
        'reserved_at' => Carbon::now()
    ];
});