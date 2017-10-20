<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Tests\Integration;

use App\Concert;
use App\ConfirmationNumberGenerator;
use App\Reservation;
use App\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\Feature\FakePaymentGateway;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_be_completed()
    {
        $concert = factory(Concert::class)->create(['ticket_price' =>  1200]);
        $tickets = factory(Ticket::class, 3)->create(['concert_id' => $concert->id]);
        $reservation = new Reservation($tickets, 'john@example.com');
        $paymentGateway = new FakePaymentGateway();
        $confirmationNumberGenerator = Mockery::mock(ConfirmationNumberGenerator::class, [
            'generate' => 'ORDERCONFIRMATION1234',
        ]);

        $order = $reservation->complete(
            $paymentGateway,
            $paymentGateway->getValidTestToken(),
            $confirmationNumberGenerator
        );

        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(3, $order->ticketsQuantity());
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals(3600, $paymentGateway->totalCharges());
    }
}
