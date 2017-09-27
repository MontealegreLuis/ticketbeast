<?php

/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace Tests\Integration;

use App\Concert;
use App\NotEnoughTickets;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConcertTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_finds_published_concerts()
    {
        $publishedConcertA = factory(Concert::class)->create([
            'published_at' =>  Carbon::parse('-1 week'),
        ]);
        $publishedConcertB = factory(Concert::class)->create([
            'published_at' =>  Carbon::parse('-1 week'),
        ]);
        $unpublishedConcert = factory(Concert::class)->create([
            'published_at' =>  null,
        ]);

        $publishedConcerts = Concert::published()->get();

        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcert));
    }

    /** @test */
    function it_orders_tickets()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(3);

        $order = $concert->orderTickets('jane@example.com', 3);

        $this->assertEquals('jane@example.com', $order->email);
        $this->assertEquals(3, $order->ticketsQuantity());
    }

    /** @test */
    function it_adds_available_tickets()
    {
        $concert = factory(Concert::class)->create();

        $concert->addTickets(50);

        $this->assertEquals(50, $concert->ticketsRemaining());
    }

    /** @test */
    function it_reserves_tickets()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(3);

        $reservation = $concert->reserveTickets(2, 'john@example.com');

        $this->assertCount(2, $reservation->tickets());
        $this->assertEquals('john@example.com', $reservation->email());
        $this->assertEquals(1, $concert->ticketsRemaining());
    }

    /** @test */
    function its_remaining_tickets_are_not_related_to_any_order()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(50);
        $concert->orderTickets('jane@example.com', 30);

        $this->assertEquals(20, $concert->ticketsRemaining());
    }
    
    /** @test */
    function it_fails_to_order_more_tickets_than_remaining()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(10);

        $this->expectException(NotEnoughTickets::class);
        $concert->orderTickets('jane@example.com', 30);

        $this->assertFalse($concert->hasOrderFor('jane@example.com'));
        $this->assertEquals(10, $concert->ticketsRemaining());
    }

    /** @test */
    function it_cannot_order_tickets_that_are_already_ordered()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(10);

        $concert->orderTickets('jane@example.com', 8);
        $this->expectException(NotEnoughTickets::class);
        $concert->orderTickets('john@example.com', 3);

        $this->assertFalse($concert->hasOrderFor('john@example.com'));
        $this->assertEquals(2, $concert->ticketsRemaining());
    }
}
