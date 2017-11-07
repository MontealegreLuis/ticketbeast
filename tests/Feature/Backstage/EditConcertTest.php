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

        $promoter = factory(User::class)->create();
        $concert = factory(Concert::class)->create(['user_id' => $promoter->id]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($promoter)->get("/backstage/concerts/{$concert->id}/edit");

        $response->assertStatus(200);
        $this->assertTrue($response->original->getData()['concert']->is($concert));
    }

    /** @test */
    function promoters_cannot_view_the_edit_form_for_their_own_published_concerts()
    {
        $promoter = factory(User::class)->create();
        $concert = factory(Concert::class)->states('published')->create([
            'user_id' => $promoter->id
        ]);
        $this->assertTrue($concert->isPublished());

        $response = $this->actingAs($promoter)->get("/backstage/concerts/{$concert->id}/edit");

        $response->assertStatus(403);
    }

    /** @test */
    function promoters_cannot_view_the_edit_form_for_other_concerts()
    {
        $promoter = factory(User::class)->create();
        $anotherPromoter = factory(User::class)->create();
        $concert = factory(Concert::class)->create(['user_id' => $anotherPromoter->id]);

        $response = $this->actingAs($promoter)->get("/backstage/concerts/{$concert->id}/edit");

        $response->assertStatus(404);
    }

    /** @test */
    function promoters_cannot_view_the_edit_form_for_a_concert_that_does_not_exist()
    {
        $promoter = factory(User::class)->create();

        $response = $this->actingAs($promoter)->get("/backstage/concerts/999/edit");

        $response->assertStatus(404);
    }

    /** @test */
    function guests_are_asked_to_login_when_attempting_to_view_the_edit_form_for_any_concert()
    {
        $guest = factory(User::class)->create();
        $concert = factory(Concert::class)->create(['user_id' => $guest->id]);

        $response = $this->get("/backstage/concerts/{$concert->id}/edit");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    function guests_must_login_when_trying_to_view_the_edit_form_for_a_concert_that_does_not_exist()
    {
        $response = $this->get("/backstage/concerts/999/edit");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    function promoters_can_edit_their_unpublished_concerts()
    {
        $this->withoutExceptionHandling();

        $promoter = factory(User::class)->create();
        $concert = factory(Concert::class)->create($this->oldInformation([
            'user_id' => $promoter->id,
        ]));

        $response = $this
            ->actingAs($promoter)
            ->patch("/backstage/concerts/$concert->id", $this->newInformation([
                'user_id' => $promoter->id,
            ]))
        ;

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
        $this->assertEquals(10, $updatedConcert->ticket_quantity);
    }

    /** @test */
    function promoters_cannot_edit_other_promoters_unpublished_concerts()
    {
        $promoter = factory(User::class)->create();
        $otherPromoter = factory(User::class)->create();

        $concert = factory(Concert::class)->create($this->oldInformation([
            'user_id' => $otherPromoter->id,
        ]));

        $response = $this
            ->actingAs($promoter)
            ->patch("/backstage/concerts/$concert->id", $this->newInformation([
                'user_id' => $promoter->id,
            ]))
        ;

        $response->assertStatus(404);
        $this->assertArraySubset(
            $this->oldInformation(['user_id' => $otherPromoter->id,]),
            $concert->refresh()->getAttributes()
        );
    }

    /** @test */
    function promoters_cannot_edit_published_concerts()
    {
        $promoter = factory(User::class)->create();
        $concert = factory(Concert::class)->states('published')->create($this->oldInformation([
            'user_id' => $promoter->id,
        ]));

        $response = $this
            ->actingAs($promoter)
            ->patch("/backstage/concerts/$concert->id", $this->newInformation([
                'user_id' => $promoter->id,
            ]))
        ;

        $response->assertStatus(403);
        $this->assertArraySubset(
            $this->oldInformation(['user_id' => $promoter->id,]),
            $concert->refresh()->getAttributes()
        );
    }

    /** @test */
    function guests_cannot_edit_concerts()
    {
        $promoter = factory(User::class)->create();
        $concert = factory(Concert::class)->create($this->oldInformation([
            'user_id' => $promoter->id,
        ]));

        $response = $this->patch("/backstage/concerts/$concert->id",$this->newInformation([
            'user_id' => $promoter->id,
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertArraySubset(
            $this->oldInformation(['user_id' => $promoter->id,]),
            $concert->refresh()->getAttributes()
        );
    }

    /** @test */
    function concerts_updated_information_is_validated()
    {
        $promoter = factory(User::class)->create();
        $concert = factory(Concert::class)->create($this->oldInformation([
            'user_id' => $promoter->id,
        ]));
        session()->setPreviousUrl(url("/backstage/concerts/{$concert->id}/edit"));

        $response = $this
            ->actingAs($promoter)
            ->patch("/backstage/concerts/$concert->id", $this->newInformation([
                'title' => '',
                'user_id' => $promoter->id,
            ]))
        ;

        $response->assertStatus(302);
        $response->assertRedirect("/backstage/concerts/{$concert->id}/edit");
        $response->assertSessionHasErrors('title');
        $this->assertArraySubset(
            $this->oldInformation(['user_id' => $promoter->id]),
            $concert->refresh()->getAttributes()
        );
    }

    private function oldInformation(array $overrides = []): array
    {
        return array_merge([
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
            'ticket_quantity' => 5,
        ], $overrides);
    }

    private function newInformation(array $overrides = []): array
    {
        return array_merge([
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
        ], $overrides);
    }
}
