<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
use App\Order;

$factory->define(Order::class, function () {
    return [
        'amount' => 5250,
        'email' => 'somebody@example.com'
    ];
});
