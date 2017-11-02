<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace Tests\Feature\Backstage;

use App\Concert;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddConcertTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function promoters_can_add_concerts()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/backstage/concerts/new');

        $response->assertStatus(200);
    }

    /** @test */
    function guests_cannot_add_concerts()
    {
        $response = $this->get('/backstage/concerts/new');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    function promoter_adds_a_concert()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/backstage/concerts', [
            'title' => 'No Warning',
            'subtitle' => 'with Cruel Hand and Backtrack',
            'additional_information' => 'You must be 19 years of age to attend this concert',
            'date' => '2017-11-18',
            'time' => '8:00pm',
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Fake St.',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '12345',
            'ticket_price' => '32.50',
            'ticket_quantity' => '75',
        ]);

        /** @var Concert $concert */
        $concert = Concert::first();

        $response->assertStatus(302);
        $response->assertRedirect("/concerts/$concert->id");

        $this->assertTrue($concert->isPublished());
        $this->assertTrue($concert->user->is($user));
        $this->assertEquals('No Warning', $concert->title);
        $this->assertEquals('with Cruel Hand and Backtrack', $concert->subtitle);
        $this->assertEquals(
            'You must be 19 years of age to attend this concert',
            $concert->additional_information
        );
        $this->assertEquals(Carbon::parse('2017-11-18 8:00pm'), $concert->date);
        $this->assertEquals('The Mosh Pit', $concert->venue);
        $this->assertEquals('123 Fake St.', $concert->venue_address);
        $this->assertEquals('Laraville', $concert->city);
        $this->assertEquals('ON', $concert->state);
        $this->assertEquals('12345', $concert->zip);
        $this->assertEquals(3250, $concert->ticket_price);
        $this->assertEquals(75, $concert->ticketsRemaining());
    }

    /** @test */
    function guest_cannot_add_a_concert()
    {
        $response = $this->post('/backstage/concerts', [
            'title' => 'No Warning',
            'subtitle' => 'with Cruel Hand and Backtrack',
            'additional_information' => 'You must be 19 years of age to attend this concert',
            'date' => '2017-11-18',
            'time' => '8:00pm',
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Fake St.',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '12345',
            'ticket_price' => '32.50',
            'ticket_quantity' => '75',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect("/login");
        $this->assertEquals(0, Concert::count());
    }
}
