<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Test\Unit\Jobs;

use App\AttendeeMessage;
use App\Jobs\SendAttendeeMessage;
use App\Mail\AttendeeMessageEmail;
use ConcertFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mail;
use OrderFactory;
use Tests\TestCase;

class SendAttendeeMessageTest extends TestCase 
{
    use RefreshDatabase;

    /** @test */
    function it_sends_a_message_to_all_concert_attendees()
    {
        $this->withoutExceptionHandling();

        Mail::fake();
        $concert = ConcertFactory::createPublished();
        $otherConcert = ConcertFactory::createPublished();
        $message = AttendeeMessage::create([
            'concert_id' => $concert->id,
            'message' => 'My message',
            'subject' => 'My subject',
        ]);
        $orderA = OrderFactory::createForConcert($concert, ['email' => 'alex@example.com']);
        $otherOrder = OrderFactory::createForConcert($otherConcert, ['email' => 'jane@example.com']);
        $orderB = OrderFactory::createForConcert($concert, ['email' => 'sam@example.com']);
        $orderC = OrderFactory::createForConcert($concert, ['email' => 'taylor@example.com']);

        SendAttendeeMessage::dispatch($message);

        Mail::assertQueued(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('alex@example.com')
                && $mail->attendeeMessage->is($message);
        });
        Mail::assertQueued(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('sam@example.com')
                && $mail->attendeeMessage->is($message);
        });
        Mail::assertQueued(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('taylor@example.com')
                && $mail->attendeeMessage->is($message);
        });
        Mail::assertNotQueued(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('jane@example.com');
        });
    }
}
