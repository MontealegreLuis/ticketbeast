<?php

namespace Tests\Unit;

use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;

class ConcertTest extends TestCase
{
    /** @test */
    function it_formats_its_date()
    {
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('December 13, 2016 8:00pm'),
        ]);

        $this->assertEquals('December 13, 2016', $concert->formattedDate);
    }

    /** @test */
    function it_formats_its_start_time()
    {
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('December 13, 2016 17:00:00'),
        ]);

        $this->assertEquals('5:00pm', $concert->formattedStartTime);
    }

    /** @test */
    function it_converts_its_ticket_price_to_dollars()
    {
        $concert = factory(Concert::class)->make([
            'ticket_price' => 2500
        ]);

        $this->assertEquals('25.00', $concert->ticketPriceInDollars);
    }
}
