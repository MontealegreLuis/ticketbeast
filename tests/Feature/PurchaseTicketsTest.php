<?php

/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace Tests\Feature;

use App\Billing\PaymentGateway;
use App\Concert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTicketsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function customer_can_purchase_tickets_for_a_concert()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 3250]);

        $response = $this->json('POST', "/concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertEquals(201, $response->status());
        $this->assertEquals(9750, $this->paymentGateway->totalCharges());
        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets->count());
    }
    
    /** @test */
    function cannot_purchase_tickets_with_incomplete_data()
    {
        $noDataProvided = [];
        $concert = factory(Concert::class)->create();

        $response = $this->json('POST', "/concerts/{$concert->id}/orders", $noDataProvided);

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
    }

    private $paymentGateway;
}
