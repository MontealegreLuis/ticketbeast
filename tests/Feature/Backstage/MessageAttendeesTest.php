<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Tests\Feature\Backstage;

use App\AttendeeMessage;
use App\Jobs\SendAttendeeMessage;
use App\User;
use ConcertFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Queue;
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

    /** @test */
    function a_promoter_can_send_a_new_message()
    {
        $this->withoutExceptionHandling();

        Queue::fake();
        $promoter = factory(User::class)->create();
        $concert = ConcertFactory::createPublished(['user_id' => $promoter->id]);

        $response = $this->actingAs($promoter)->post("/backstage/concerts/{$concert->id}/messages", [
            'subject' => 'My Subject',
            'message' => 'My Message',
        ]);

        $response->assertRedirect("/backstage/concerts/{$concert->id}/messages/new");
        $response->assertSessionHas('flash');

        $message = AttendeeMessage::first();
        $this->assertEquals($concert->id, $message->concert_id);
        $this->assertEquals('My Subject', $message->subject);
        $this->assertEquals('My Message', $message->message);

        Queue::assertPushed(SendAttendeeMessage::class, function ($job) use ($message) {
            return $job->attendeeMessage->is($message);
        });
    }

    /** @test */
    function promoters_cannot_send_a_new_message_for_other_concerts()
    {
        Queue::fake();
        $promoter = factory(User::class)->create();
        $otherPromoter = factory(User::class)->create();
        $concert = ConcertFactory::createPublished(['user_id' => $otherPromoter->id,]);

        $response = $this
            ->actingAs($promoter)
            ->post("/backstage/concerts/{$concert->id}/messages", [
                'subject' => 'My subject',
                'message' => 'My message',
            ])
        ;

        $response->assertStatus(404);
        $this->assertEquals(0, AttendeeMessage::count());
        Queue::assertNotPushed(SendAttendeeMessage::class);
    }

    /** @test */
    function guests_cannot_send_a_new_message_for_any_concerts()
    {
        Queue::fake();
        $concert = ConcertFactory::createPublished();

        $response = $this->post("/backstage/concerts/{$concert->id}/messages", [
            'subject' => 'My subject',
            'message' => 'My message',
        ]);

        $response->assertRedirect('/login');
        $this->assertEquals(0, AttendeeMessage::count());
        Queue::assertNotPushed(SendAttendeeMessage::class);
    }
}
