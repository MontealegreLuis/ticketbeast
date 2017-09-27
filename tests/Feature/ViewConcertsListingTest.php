<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Tests\Feature;

use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewConcertsListingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function user_can_view_a_published_concert_listing()
    {
        $concert = factory(Concert::class)->states('published')->create([
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

    /** @test */
    function user_cannot_view_unpublished_concert_listing()
    {
        $concert = factory(Concert::class)->states('unpublished')->create();

        $response = $this->get('/concerts/' . $concert->id);

        $response->assertStatus(404);
    }
}
