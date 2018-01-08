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
use Illuminate\Http\Testing\File;
use Storage;
use Tests\TestCase;

class AddConcertTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function promoters_can_view_the_form_to_add_concerts()
    {
        $promoter = factory(User::class)->create();

        $response = $this->actingAs($promoter)->get('/backstage/concerts/new');

        $response->assertStatus(200);
    }

    /** @test */
    function guests_cannot_view_the_form_to_add_concerts()
    {
        $response = $this->get('/backstage/concerts/new');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    function promoter_adds_a_concert()
    {
        $this->withoutExceptionHandling();
        Storage::fake('s3');
        $file = File::image('concert-poster.png', 850, 1100);

        $promoter = factory(User::class)->create();

        $response = $this->actingAs($promoter)->post('/backstage/concerts', [
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
            'poster_image' => $file,
        ]);

        /** @var Concert $concert */
        $concert = Concert::first();

        $response->assertStatus(302);
        $response->assertRedirect('/backstage/concerts');

        $this->assertFalse($concert->isPublished());
        $this->assertTrue($concert->user->is($promoter));
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
        $this->assertEquals(75, $concert->ticket_quantity);
        $this->assertEquals(0, $concert->ticketsRemaining());
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

    /** @test */
    function poster_image_is_uploaded_if_included()
    {
        $this->withoutExceptionHandling();

        $promoter = factory(User::class)->create();

        Storage::fake('s3');
        $file = File::image('concert-poster.png', 850, 1100);

        $response = $this->actingAs($promoter)->post('/backstage/concerts', [
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
            'poster_image' => $file,
        ]);

        $this->assertNotNull(Concert::first()->poster_image_path);
        Storage::disk('s3')->assertExists(Concert::first()->poster_image_path);
        $this->assertFileEquals(
            $file->getPathname(),
            Storage::disk('s3')->path(Concert::first()->poster_image_path)
        );
    }

    /** @test */
    function poster_image_must_be_an_image()
    {
        Storage::fake('s3');
        session()->setPreviousUrl(url('/backstage/concerts/new'));
        $file = File::create('not-a-poster.pdf');

        $promoter = factory(User::class)->create();

        $response = $this->actingAs($promoter)->post('/backstage/concerts', [
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
            'poster_image' => $file,
        ]);

        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('poster_image');
        $this->assertEquals(0, Concert::count());
    }

    /** @test */
    function poster_image_must_be_at_least_400px_wide()
    {
        Storage::fake('s3');
        session()->setPreviousUrl(url('/backstage/concerts/new'));
        $file = File::image('poster.png', 399, 516);

        $promoter = factory(User::class)->create();

        $response = $this->actingAs($promoter)->post('/backstage/concerts', [
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
            'poster_image' => $file,
        ]);

        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('poster_image');
        $this->assertEquals(0, Concert::count());
    }

    /** @test */
    function poster_image_must_letter_aspect_ratio()
    {
        Storage::fake('s3');
        session()->setPreviousUrl(url('/backstage/concerts/new'));
        $file = File::image('poster.png', 851, 1100);

        $promoter = factory(User::class)->create();

        $response = $this->actingAs($promoter)->post('/backstage/concerts', [
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
            'poster_image' => $file,
        ]);

        $response->assertRedirect('/backstage/concerts/new');
        $response->assertSessionHasErrors('poster_image');
        $this->assertEquals(0, Concert::count());
    }
}
