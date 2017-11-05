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

class EditConcertTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function promoters_can_view_the_edit_form_for_their_own_unpublished_concerts()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create(['user_id' => $user->id]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)->get("/backstage/concerts/{$concert->id}/edit");

        $response->assertStatus(200);
        $this->assertTrue($response->original->getData()['concert']->is($concert));
    }

    /** @test */
    function promoters_cannot_view_the_edit_form_for_their_own_published_concerts()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->states('published')->create([
            'user_id' => $user->id
        ]);
        $this->assertTrue($concert->isPublished());

        $response = $this->actingAs($user)->get("/backstage/concerts/{$concert->id}/edit");

        $response->assertStatus(403);
    }

    /** @test */
    function promoters_cannot_view_the_edit_form_for_other_concerts()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $concert = factory(Concert::class)->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get("/backstage/concerts/{$concert->id}/edit");

        $response->assertStatus(404);
    }

    /** @test */
    function promoters_cannot_view_the_edit_form_for_a_concert_that_does_not_exist()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get("/backstage/concerts/999/edit");

        $response->assertStatus(404);
    }

    /** @test */
    function guests_are_asked_to_login_when_attempting_to_view_the_edit_form_for_any_concert()
    {
        $otherUser = factory(User::class)->create();
        $concert = factory(Concert::class)->create(['user_id' => $otherUser->id]);

        $response = $this->get("/backstage/concerts/{$concert->id}/edit");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    function guests_need_to_login_when_trying_to_view_the_edit_form_for_a_concert_that_does_not_exist()
    {
        $response = $this->get("/backstage/concerts/999/edit");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    function promoters_can_edit_their_unpublished_concerts()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'title' => 'Old title',
            'subtitle' => 'Old subtitle',
            'additional_information' => 'Old additional information',
            'date' => Carbon::parse('2017-01-01 5:00pm'),
            'venue' => 'Old venue',
            'venue_address' => 'Old address',
            'city' => 'Old city',
            'state' => 'Old state',
            'zip' => '00000',
            'ticket_price' => 2000,
        ]);

        $response = $this->actingAs($user)->patch("/backstage/concerts/$concert->id", [
            'user_id' => $user->id,
            'title' => 'New title',
            'subtitle' => 'New subtitle',
            'additional_information' => 'New additional information',
            'date' => '2018-12-12',
            'time' => '8:00pm',
            'venue' => 'New venue',
            'venue_address' => 'New address',
            'city' => 'New city',
            'state' => 'New state',
            'zip' => '99999',
            'ticket_price' => '25.50',
            'ticket_quantity' => 10,
        ]);

        $updatedConcert = $concert->refresh();

        $response->assertRedirect('/backstage/concerts');
        $this->assertEquals('New title', $updatedConcert->title);
        $this->assertEquals('New subtitle', $updatedConcert->subtitle);
        $this->assertEquals('New additional information', $updatedConcert->additional_information);
        $this->assertEquals(Carbon::parse('2018-12-12 8:00pm'), $updatedConcert->date);
        $this->assertEquals('New venue', $updatedConcert->venue);
        $this->assertEquals('New address', $updatedConcert->venue_address);
        $this->assertEquals('New city', $updatedConcert->city);
        $this->assertEquals('New state', $updatedConcert->state);
        $this->assertEquals('99999', $updatedConcert->zip);
        $this->assertEquals(2550, $updatedConcert->ticket_price);
    }

    /** @test */
    function promoters_cannot_edit_other_promoters_unpublished_concerts()
    {
        $promoter = factory(User::class)->create();
        $otherPromoter = factory(User::class)->create();

        $concert = factory(Concert::class)->create([
            'user_id' => $otherPromoter->id,
            'title' => 'Old title',
            'subtitle' => 'Old subtitle',
            'additional_information' => 'Old additional information',
            'date' => Carbon::parse('2017-01-01 5:00pm'),
            'venue' => 'Old venue',
            'venue_address' => 'Old address',
            'city' => 'Old city',
            'state' => 'Old state',
            'zip' => '00000',
            'ticket_price' => 2000,
        ]);

        $response = $this->actingAs($promoter)->patch("/backstage/concerts/$concert->id", [
            'user_id' => $promoter->id,
            'title' => 'New title',
            'subtitle' => 'New subtitle',
            'additional_information' => 'New additional information',
            'date' => '2018-12-12',
            'time' => '8:00pm',
            'venue' => 'New venue',
            'venue_address' => 'New address',
            'city' => 'New city',
            'state' => 'New state',
            'zip' => '99999',
            'ticket_price' => '25.50',
            'ticket_quantity' => 10,
        ]);

        $updatedConcert = $concert->refresh();

        $response->assertStatus(404);
        $this->assertEquals('Old title', $updatedConcert->title);
        $this->assertEquals('Old subtitle', $updatedConcert->subtitle);
        $this->assertEquals('Old additional information', $updatedConcert->additional_information);
        $this->assertEquals(Carbon::parse('2017-01-01 5:00pm'), $updatedConcert->date);
        $this->assertEquals('Old venue', $updatedConcert->venue);
        $this->assertEquals('Old address', $updatedConcert->venue_address);
        $this->assertEquals('Old city', $updatedConcert->city);
        $this->assertEquals('Old state', $updatedConcert->state);
        $this->assertEquals('00000', $updatedConcert->zip);
        $this->assertEquals(2000, $updatedConcert->ticket_price);
    }

    /** @test */
    function promoters_cannot_edit_published_concerts()
    {
        $promoter = factory(User::class)->create();
        $concert = factory(Concert::class)->states('published')->create([
            'user_id' => $promoter->id,
            'title' => 'Old title',
            'subtitle' => 'Old subtitle',
            'additional_information' => 'Old additional information',
            'date' => Carbon::parse('2017-01-01 5:00pm'),
            'venue' => 'Old venue',
            'venue_address' => 'Old address',
            'city' => 'Old city',
            'state' => 'Old state',
            'zip' => '00000',
            'ticket_price' => 2000,
        ]);

        $response = $this->actingAs($promoter)->patch("/backstage/concerts/$concert->id", [
            'user_id' => $promoter->id,
            'title' => 'New title',
            'subtitle' => 'New subtitle',
            'additional_information' => 'New additional information',
            'date' => '2018-12-12',
            'time' => '8:00pm',
            'venue' => 'New venue',
            'venue_address' => 'New address',
            'city' => 'New city',
            'state' => 'New state',
            'zip' => '99999',
            'ticket_price' => '25.50',
            'ticket_quantity' => 10,
        ]);

        $updatedConcert = $concert->refresh();

        $response->assertStatus(403);
        $this->assertEquals('Old title', $updatedConcert->title);
        $this->assertEquals('Old subtitle', $updatedConcert->subtitle);
        $this->assertEquals('Old additional information', $updatedConcert->additional_information);
        $this->assertEquals(Carbon::parse('2017-01-01 5:00pm'), $updatedConcert->date);
        $this->assertEquals('Old venue', $updatedConcert->venue);
        $this->assertEquals('Old address', $updatedConcert->venue_address);
        $this->assertEquals('Old city', $updatedConcert->city);
        $this->assertEquals('Old state', $updatedConcert->state);
        $this->assertEquals('00000', $updatedConcert->zip);
        $this->assertEquals(2000, $updatedConcert->ticket_price);
    }

    /** @test */
    function guests_cannot_edit_concerts()
    {
        $promoter = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $promoter->id,
            'title' => 'Old title',
            'subtitle' => 'Old subtitle',
            'additional_information' => 'Old additional information',
            'date' => Carbon::parse('2017-01-01 5:00pm'),
            'venue' => 'Old venue',
            'venue_address' => 'Old address',
            'city' => 'Old city',
            'state' => 'Old state',
            'zip' => '00000',
            'ticket_price' => 2000,
        ]);

        $response = $this->patch("/backstage/concerts/$concert->id", [
            'user_id' => $promoter->id,
            'title' => 'New title',
            'subtitle' => 'New subtitle',
            'additional_information' => 'New additional information',
            'date' => '2018-12-12',
            'time' => '8:00pm',
            'venue' => 'New venue',
            'venue_address' => 'New address',
            'city' => 'New city',
            'state' => 'New state',
            'zip' => '99999',
            'ticket_price' => '25.50',
        ]);

        $updatedConcert = $concert->refresh();

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertEquals('Old title', $updatedConcert->title);
        $this->assertEquals('Old subtitle', $updatedConcert->subtitle);
        $this->assertEquals('Old additional information', $updatedConcert->additional_information);
        $this->assertEquals(Carbon::parse('2017-01-01 5:00pm'), $updatedConcert->date);
        $this->assertEquals('Old venue', $updatedConcert->venue);
        $this->assertEquals('Old address', $updatedConcert->venue_address);
        $this->assertEquals('Old city', $updatedConcert->city);
        $this->assertEquals('Old state', $updatedConcert->state);
        $this->assertEquals('00000', $updatedConcert->zip);
        $this->assertEquals(2000, $updatedConcert->ticket_price);
    }

    /** @test */
    function concerts_updated_information_is_validated()
    {
        $promoter = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $promoter->id,
        ]);
        session()->setPreviousUrl(url("/backstage/concerts/{$concert->id}/edit"));

        $response = $this->actingAs($promoter)->patch("/backstage/concerts/$concert->id", [
            'user_id' => $promoter->id,
            'title' => '',
            'subtitle' => 'New subtitle',
            'additional_information' => 'New additional information',
            'date' => '2018-12-12',
            'time' => '8:00pm',
            'venue' => 'New venue',
            'venue_address' => 'New address',
            'city' => 'New city',
            'state' => 'New state',
            'zip' => '99999',
            'ticket_price' => '25.50',
            'ticket_quantity' => 10,
        ]);

        $concert->refresh();

        $response->assertStatus(302);
        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors('title');
    }
}
