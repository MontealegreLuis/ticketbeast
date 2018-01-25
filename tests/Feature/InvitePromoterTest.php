<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace Tests\Feature;

use App\IdentifierGenerator;
use App\Invitation;
use App\Mail\InvitationEmail;
use Egulias\EmailValidator\Exception\InvalidEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mail;
use Mockery;
use Tests\TestCase;

class InvitePromoterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function invite_a_promoter()
    {
        Mail::fake();
        $generator = Mockery::mock(IdentifierGenerator::class);
        $generator
            ->shouldReceive('generateConfirmationNumber')
            ->andReturn('TESTCODE1234')
        ;
        $this->app->bind(IdentifierGenerator::class, function () use ($generator) {
            return $generator;
        });

        $this->artisan('promoter:invite', ['email' => 'john@example.com']);

        $this->assertEquals(1, Invitation::count());
        $invitation = Invitation::first();
        $this->assertEquals('john@example.com', $invitation->email);
        $this->assertEquals('TESTCODE1234', $invitation->code);

        Mail::assertSent(InvitationEmail::class, function ($mail) use ($invitation) {
            return $mail->hasTo('john@example.com') && $mail->invitation->is($invitation);
        });
    }

}
