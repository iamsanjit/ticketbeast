<?php

use Faker\Generator as Faker;
use App\Concert;

$factory->define(App\Ticket::class, function (Faker $faker) {
    return [
        'concert_id' => function () {
            return factory(Concert::class)->create();
        }
    ];
});
