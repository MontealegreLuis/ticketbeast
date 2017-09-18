<?php

namespace Tests\Feature;

use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewConcertsListingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function user_can_view_concert_listing()
    {
        $concert = Concert::create([
            'title' => 'The red chord',
            'subtitle' => 'with Animosity and the Lethargy',
            'date' => Carbon::parse('December 13, 2016 8:00pm'),
            'ticket_price' => 3250,
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17196',
            'additional_information' => 'For tickets call (555) 222-2222.',
        ]);

        $response = $this->get('/concerts/' . $concert->id);

        $response->assertStatus(200);
        $response->assertSee('The red chord');
        $response->assertSee('with Animosity and the Lethargy');
        $response->assertSee('December 13, 2016');
        $response->assertSee('8:00pm');
        $response->assertSee('32.50');
        $response->assertSee('The Mosh Pit');
        $response->assertSee('123 Example lane');
        $response->assertSee('Laraville, ON 17196');
        $response->assertSee('For tickets call (555) 222-2222.');
    }
}
