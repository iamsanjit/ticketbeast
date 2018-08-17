<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(App\Concert::class, function (Faker $faker) {
    return [
        'title' => 'The Red Chord',
        'subtitle' => 'with Animosity and Ethargy',
        'date' => Carbon::parse('+2 weeks'),
        'ticket_price' => 2000,
        'venue' => 'The Mosh Pit',
        'venue_address' => '123 Example Lane',
        'city' => 'Laraville',
        'state' => 'ON',
        'zip' => '17916',
        'additional_information' => 'For tickets call 555-555-5555',
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