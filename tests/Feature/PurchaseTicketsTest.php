<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Concert;
use App\IdentifierGenerator;
use App\Mail\OrderConfirmationEmail;
use App\User;
use ConcertFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

class PurchaseTicketsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function customer_can_purchase_tickets_to_a_published_concert()
    {
        $this->withoutExceptionHandling();

        $generator = Mockery::mock(IdentifierGenerator::class);
        $generator
            ->shouldReceive('generateConfirmationNumber')
            ->andReturn('ORDERCONFIRMATION1234')
        ;
        $generator
            ->shouldReceive('generateCodeFor')
            ->andReturn('ticket-code-1', 'ticket-code-2', 'ticket-code-3')
        ;
        $this->app->instance(IdentifierGenerator::class, $generator);
        $promoter = factory(User::class)->create(['stripe_account_id' => 'test_acct_1234']);
        $concert = ConcertFactory::createPublished([
            'ticket_price' => 3250,
            'ticket_quantity' => 3,
            'user_id' => $promoter,
        ]);

        $response = $this->orderTicketsFor($concert);

        $this->assertEquals(201, $response->status());
        $response->assertJson([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'email' => 'john@example.com',
            'amount' => 9750,
            'tickets' => [
                ['code' => 'ticket-code-1'],
                ['code' => 'ticket-code-2'],
                ['code' => 'ticket-code-3'],
            ],
        ]);
        $this->assertEquals(9750, $this->paymentGateway->totalCharges());
        $this->assertTrue($concert->hasOrderFor('john@example.com'));
        $order = $concert->orderFor('john@example.com')->first();
        $this->assertEquals(3, $order->ticketsQuantity());
        Mail::assertSent(OrderConfirmationEmail::class, function (OrderConfirmationEmail $mail) use ($order) {
            return $mail->hasTo('john@example.com')
                && $mail->order->id == $order->id;
        });
    }

    /** @test */
    function cannot_purchase_tickets_to_an_unpublished_concert()
    {
        $concert = factory(Concert::class)->states('unpublished')->create();

        $response = $this->orderTicketsFor($concert);

        $this->assertEquals(404, $response->status());
        $this->assertFalse($concert->hasOrderFor('john@example.com'));
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
    }

    /** @test */
    function order_is_not_created_if_payment_fails()
    {
        $concert = ConcertFactory::createPublished(['ticket_quantity' => 3]);

        $response = $this->orderTicketsFor($concert, ['payment_token' => 'invalid-token']);

        $this->assertEquals(422, $response->status());
        $this->assertFalse($concert->hasOrderFor('john@example.com'));
        $this->assertEquals(3, $concert->ticketsRemaining());
    }

    /** @test */
    function cannot_purchase_more_tickets_than_remain()
    {
        $concert = ConcertFactory::createPublished(['ticket_quantity' => 50]);

        $response = $this->orderTicketsFor($concert, ['ticket_quantity' => 51]);

        $this->assertEquals(422, $response->status());
        $this->assertFalse($concert->hasOrderFor('john@example.com'));
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
        $this->assertEquals(50, $concert->ticketsRemaining());
    }

    /** @test */
    function cannot_purchase_tickets_another_customer_is_already_trying_to_purchase()
    {
        $this->withoutExceptionHandling();
        $concert = ConcertFactory::createPublished([
            'ticket_price' => 1200,
            'ticket_quantity' => 3,
        ]);

        $this->paymentGateway->beforeCharge(function ($paymentGateway) use ($concert) {
            $response = $this->orderTicketsFor($concert, [
                'email' => 'jane@example.com',
                'ticket_quantity' => 1,
            ]);
            $this->assertEquals(422, $response->status());
            $this->assertFalse($concert->hasOrderFor('jane@example.com'));
            $this->assertEquals(0, $this->paymentGateway->totalCharges());
        });

        $response = $this->orderTicketsFor($concert);
        $this->assertEquals(201, $response->status());
        $this->assertEquals(3600, $this->paymentGateway->totalCharges());
        $this->assertTrue($concert->hasOrderFor('john@example.com'));
        $this->assertEquals(3, $concert->orderFor('john@example.com')->first()->ticketsQuantity());
    }

    /** @test */
    function cannot_purchase_tickets_with_incomplete_data()
    {
        $noData = [];
        $concert = factory(Concert::class)->create();

        $response = $this->json('POST', "/concerts/{$concert->id}/orders", $noData);

        $this->assertEquals(422, $response->status());
        $this->assertArrayHasKey('email', $response->decodeResponseJson()['errors']);
        $this->assertArrayHasKey('ticket_quantity', $response->decodeResponseJson()['errors']);
        $this->assertArrayHasKey('payment_token', $response->decodeResponseJson()['errors']);
    }

    /* Do not try to use @before, $this->app won't be available */
    function setUp()
    {
        parent::setUp();
        $this->paymentGateway = new FakePaymentGateway();
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
        Mail::fake();
    }

    private function orderTicketsFor(Concert $concert, array $purchase = []): TestResponse
    {
        $defaults = [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ];

        $savedRequest = $this->app['request'];
        $response = $this->json(
            'POST',
            "/concerts/{$concert->id}/orders",
            array_merge($defaults, $purchase)
        );
        $this->app['request'] = $savedRequest;

        return $response;
    }

    /** @var FakePaymentGateway */
    private $paymentGateway;
}
