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
        'email' => 'somebody@example.com',
        'confirmation_number' => 'order-confirmation-1234',
        'card_last_four_digits' => '4242',
    ];
});
