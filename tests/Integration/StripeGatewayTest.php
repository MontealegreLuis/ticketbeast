<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Tests\Integration;

use App\Billing\PaymentFailed;
use App\Billing\PaymentGateway;
use App\Billing\StripePaymentGateway;
use Stripe\Charge;
use Tests\ContractTests\PaymentGatewayTest;
use function config;

class StripeGatewayTest extends PaymentGatewayTest
{
    /** @test */
    function it_fails_to_charge_using_an_invalid_token()
    {
        $paymentGateway = $this->newPaymentGateway();

        $this->expectException(PaymentFailed::class);
        $paymentGateway->charge(2500, 'invalid-token');

        $this->assertCount(0, $this->newCharges());
    }

    protected function setUp()
    {
        parent::setUp();
        $this->lastCharge = $this->lastCharge();
    }

    private function lastCharge()
    {
        return array_first(Charge::all(
            ['limit' => 1],
            ['api_key' => config("services.stripe.secret")]
        )['data']);
    }

    public function newCharges()
    {
        return Charge::all(
            ['ending_before' => $this->lastCharge ? $this->lastCharge->id :  null],
            ['api_key' => config("services.stripe.secret")]
        )['data'];
    }

    function newPaymentGateway(): PaymentGateway
    {
        return new StripePaymentGateway(config("services.stripe.secret"));
    }

    private $lastCharge;
}
