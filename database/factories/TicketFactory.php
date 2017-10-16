<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
use App\Concert;
use App\Ticket;
use Illuminate\Support\Carbon;

$factory->define(Ticket::class, function () {
    return [
        'concert_id' => function() {
            return factory(Concert::class)->create();
        }
    ];
});

$factory->state(Ticket::class, 'reserved', function() {
    return ['reserved_at' => Carbon::now()];
});
