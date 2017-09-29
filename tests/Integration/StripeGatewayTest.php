<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace Tests\Integration;

use App\Billing\PaymentFailed;
use App\Billing\StripePaymentGateway;
use function config;
use Stripe\Charge;
use Tests\TestCase;
use Stripe\Token;

class StripeGatewayTest extends TestCase
{
    /** @test */
    function it_charges_successfully_using_a_valid_token()
    {
        $paymentGateway = new StripePaymentGateway(config("services.stripe.secret"));

        $paymentGateway->charge(2500, $this->validToken());

        $this->assertCount(1, $this->newCharges());
        $this->assertEquals(2500, $this->lastCharge()->amount);
    }

    /** @test */
    function it_fails_to_charge_using_an_invalid_token()
    {
        $paymentGateway = new StripePaymentGateway(config("services.stripe.secret"));

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

    private function validToken(): string
    {
        return Token::create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 12,
                'exp_year' => date('Y') + 1,
                'cvc' => '123'
            ],
        ], ['api_key' => config("services.stripe.secret")])->id;
    }

    public function newCharges()
    {
        return Charge::all(
            ['ending_before' => $this->lastCharge ? $this->lastCharge->id :  null],
            ['api_key' => config("services.stripe.secret")]
        )['data'];
    }

    private $lastCharge;
}
