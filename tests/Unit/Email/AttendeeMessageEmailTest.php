<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace App\Mail;

use App\AttendeeMessage;
use Tests\TestCase;

class AttendeeMessageEmailTest extends TestCase
{
    /** @test */
    function it_has_the_correct_subject_and_message()
    {
        $message = new AttendeeMessage([
            'subject' => 'My Subject',
            'message' => 'My Message',
        ]);

        $email = new AttendeeMessageEmail($message);

        $this->assertEquals('My Subject', $email->build()->subject);
        $this->assertEquals('My Message', trim($this->render($email)));
    }

    function render(AttendeeMessageEmail $email): string
    {
        $email->build();
        return view($email->textView, $email->buildViewData())->render();
    }
}