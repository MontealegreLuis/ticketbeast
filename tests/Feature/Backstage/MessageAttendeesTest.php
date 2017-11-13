<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Tests\Feature\Backstage;

use App\User;
use ConcertFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageAttendeesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function promoters_can_view_the_message_form_for_their_concerts()
    {
        $this->withoutExceptionHandling();

        $promoter = factory(User::class)->create();

        $concert = ConcertFactory::createPublished(['user_id' => $promoter->id]);

        $response = $this
            ->actingAs($promoter)
            ->get("/backstage/concerts/$concert->id/messages/new")
        ;

        $response->assertStatus(200);
        $this->assertEquals($response->original->getName(), 'backstage.concert-messages.new');
        $this->assertTrue($response->original->getData()['concert']->is($concert));
    }

    /** @test */
    function promoters_cannot_view_the_message_form_of_another_promoter()
    {
        $promoter = factory(User::class)->create();
        $anotherPromoter = factory(User::class)->create();


        $concert = ConcertFactory::createPublished(['user_id' => $anotherPromoter->id]);

        $response = $this
            ->actingAs($promoter)
            ->get("/backstage/concerts/$concert->id/messages/new")
        ;

        $response->assertStatus(404);
    }

    /** @test */
    function guests_cannot_view_the_message_form_of_any_concert()
    {
        $concert = ConcertFactory::createPublished();

        $response = $this->get("/backstage/concerts/$concert->id/messages/new");

        $response->assertRedirect('/login');
    }
}
