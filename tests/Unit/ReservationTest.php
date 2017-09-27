<?php

/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace Tests\Integration;

use App\Reservation;
use App\Ticket;
use Mockery;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    /** @test */
    function it_calculates_the_total_cost()
    {
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);
        $reservation = new Reservation($tickets);

        $this->assertEquals(3600, $reservation->totalCost());
    }

    /** @test */
    function it_retrieves_its_tickets()
    {
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);
        $reservation = new Reservation($tickets);

        $this->assertEquals($tickets, $reservation->tickets());
    }

    /** @test */
    function it_releases_its_tickets_when_it_is_cancelled()
    {
        $tickets = collect([
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class)
        ]);
        $reservation = new Reservation($tickets);

        $reservation->cancel();

        $tickets->each(function ($ticket) {
            $ticket->shouldHaveReceived('release');
        });
    }
}
