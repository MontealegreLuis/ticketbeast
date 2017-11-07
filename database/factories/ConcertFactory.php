<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

use App\Concert;
use App\User;
use Carbon\Carbon;

$factory->define(Concert::class, function () {
    return [
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'title' => 'Example Band',
        'subtitle' => 'with The Fake Openers',
        'additional_information' => 'Some sample addtional information',
        'date' => Carbon::parse('+2 weeks'),
        'venue' => 'The Example Theatre',
        'venue_address' => '123 Example lane',
        'city' => 'Fakeville',
        'state' => 'ON',
        'zip' => '90210',
        'ticket_price' => 2000,
        'ticket_quantity' => 5,
    ];
});

$factory->state(Concert::class, 'published', function () {
    return [
        'published_at' => Carbon::parse('-1 week'),
    ];
});

$factory->state(Concert::class, 'unpublished', function () {
    return [
        'published_at' => null,
    ];
});
