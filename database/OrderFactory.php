<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
use App\Concert;
use App\Order;
use App\Ticket;

class OrderFactory
{
    public static function createForConcert(
        Concert $concert,
        array $overrides = [],
        int $ticketQuantity = 1
    ): Order {
        $order = factory(Order::class)->create($overrides);
        $tickets = factory(Ticket::class, $ticketQuantity)->create(['concert_id' => $concert->id]);
        $order->tickets()->saveMany($tickets);

        return $order;
    }
}
