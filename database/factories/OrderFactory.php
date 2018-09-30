<?php

use Faker\Generator as Faker;

$factory->define(App\Order::class, function (Faker $faker) {
    return [
        'email' => 'jane@example.com',
        'amount' => 6789,
        'confirmation_number' => 'ORDERCONFIRMATION1234',
        'card_last_four' => '1234',
    ];
});
