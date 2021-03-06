<?php

use Carbon\Carbon;
use Faker\Generator as Faker;
use App\User;

$factory->define(App\Concert::class, function (Faker $faker) {
    return [
        'user_id' => function() {
            return factory(User::class)->create();
        },
        'title' => 'The Red Chord',
        'subtitle' => 'with Animosity and Ethargy',
        'additional_information' => 'For tickets call 555-555-5555',
        'date' => Carbon::parse('+2 weeks'),
        'venue' => 'The Mosh Pit',
        'venue_address' => '123 Example Lane',
        'city' => 'Laraville',
        'state' => 'ON',
        'zip' => '17916',
        'ticket_price' => 2000,
        'ticket_quantity' => 10,
    ];
});

$factory->state(App\Concert::class, 'published', function(Faker $faker) {
    return [
        'published_at' => Carbon::parse('+2 weeks')
    ];
});

$factory->state(App\Concert::class, 'unpublished', function (Faker $faker) {
    return [
        'published_at' => null
    ];
});