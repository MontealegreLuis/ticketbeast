<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace App\Mail;

use App\Order;
use Tests\TestCase;

class OrderConfirmationEmailTest extends TestCase
{
    /** @test */
    function it_contains_link_to_the_order_confirmation_page()
    {
        $order = factory(Order::class)->make([
            'confirmation_number' => 'confirmation-number-1234',
        ]);
        $email = new OrderConfirmationEmail($order);

        $html = $email->render($email);

        $this->assertContains(url('/orders/confirmation-number-1234'), $html);
    }

    /** @test */
    function it_has_a_subject()
    {
        $order = factory(Order::class)->make();

        $email = new OrderConfirmationEmail($order);

        $this->assertEquals('Your TicketBeast Order', $email->build()->subject);
    }
}
