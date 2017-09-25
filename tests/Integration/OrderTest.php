<?php

/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace Tests\Integration;

use App\Concert;
use App\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    function it_is_created_from_purchase()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 1200]);
        $concert->addTickets(5);
        $this->assertEquals(5, $concert->ticketsRemaining());

        $order = Order::forPurchase($concert->findTickets(3), 'jane@example.com');

        $this->assertEquals('jane@example.com', $order->email);
        $this->assertEquals(3, $order->ticketsQuantity());
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals(2, $concert->ticketsRemaining());
    }

    /** @test */
    function it_releases_tickets_if_it_is_cancelled()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(10);

        $order = $concert->orderTickets('jane@example.com', 5);
        $this->assertEquals(5, $concert->ticketsRemaining());

        $order->cancel();

        $this->assertEquals(10, $concert->ticketsRemaining());
        $this->assertNull(Order::find($order->id));
    }

    /** @test */
    function it_can_be_converted_to_array()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 1200]);
        $concert->addTickets(5);
        $order = $concert->orderTickets('jane@example.com', 5);

        $this->assertEquals([
            'email' => 'jane@example.com',
            'ticket_quantity' => 5,
            'amount' => 6000,
        ], $order->toArray());
    }
}
