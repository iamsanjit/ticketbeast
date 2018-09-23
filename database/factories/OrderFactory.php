<?php

use Faker\Generator as Faker;

$factory->define(App\Order::class, function (Faker $faker) {
    return [
        'email' => 'jane@example.com',
        'amount' => 6789
    ];
});
