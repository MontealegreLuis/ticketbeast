<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Tests\Unit;

use App\Billing\Charge;
use App\Concert;
use App\NotEnoughTickets;
use App\Order;
use App\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_be_released()
    {
        $ticket = factory(Ticket::class)->states('reserved')->create();
        $this->assertNotNull($ticket->reserved_at);

        $ticket->release();

        $this->assertNull($ticket->fresh()->reserved_at);
    }

    /** @test */
    function it_can_be_reserved()
    {
        $ticket = factory(Ticket::class)->create();
        $this->assertNull($ticket->reserved_at);

        $ticket->reserve();

        $this->assertNotNull($ticket->reserved_at);
    }

    /** @test */
    function it_can_be_claimed_for_an_order()
    {
        $order = factory(Order::class)->create();
        /** @var Ticket $ticket */
        $ticket = factory(Ticket::class)->create(['code' => null]);

        $ticket->claimFor($order);

        $this->assertEquals($order->id, $ticket->order_id);
    }


    /** @test */
    function it_cannot_be_reserved_if_it_is_already_purchased()
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create();
        $concert->addTickets(3);
        $tickets = $concert->findTickets(2);
        $charge = new Charge(['amount' => 6000, 'card_last_four' => '1234']);
        Order::forPurchase(
            $tickets,
            'jane@example.com',
            $charge,
            'order-number-123'
        );

        $this->expectException(NotEnoughTickets::class);
        $concert->reserveTickets(2, 'john@example.com');
        $this->assertEquals(1, $concert->ticketsRemaining());
    }

    /** @test */
    function it_cannot_be_reserved_if_it_is_already_reserved()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(3);
        $concert->reserveTickets(2, 'john@example.com');

        $this->expectException(NotEnoughTickets::class);
        $concert->reserveTickets(2, 'jane@example.com');
        $this->assertEquals(1, $concert->ticketsRemaining());
    }
}
