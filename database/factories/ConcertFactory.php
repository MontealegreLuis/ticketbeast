<?php

/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

use App\Concert;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Concert::class, function (Faker $faker) {

    return [
        'title' => 'Example Band',
        'subtitle' => 'with The Fake Openers',
        'date' => Carbon::parse('+2 weeks'),
        'ticket_price' => 2000,
        'venue' => 'The Example Theatre',
        'venue_address' => '123 Example lane',
        'city' => 'Fakeville',
        'state' => 'ON',
        'zip' => '90210',
        'additional_information' => 'Some sample addtional information',
    ];
});

$factory->state(Concert::class, 'published', function ($faker) {
    return [
        'published_at' => Carbon::parse('-1 week'),
    ];
});

$factory->state(Concert::class, 'unpublished', function ($faker) {
    return [
        'published_at' => null,
    ];
});
