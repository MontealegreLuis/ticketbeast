<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Tests\Integration;

use App\Billing\PaymentGateway;
use App\Billing\StripePaymentGateway;
use Stripe\Charge;
use Stripe\Transfer;
use Tests\ContractTests\PaymentGatewayTest;
use function config;

class StripeGatewayTest extends PaymentGatewayTest
{
    function newPaymentGateway(): PaymentGateway
    {
        return new StripePaymentGateway(config("services.stripe.secret"));
    }

    /** @test */
    function it_transfers_90_percent_of_the_charges_to_the_promoter_account()
    {
        $gateway = new StripePaymentGateway(config("services.stripe.secret"));

        $gateway->charge(50000, $gateway->getValidTestToken(), env('STRIPE_TEST_PROMOTER_ID'));

        $lastCharge = array_first(Charge::all(
            ['limit' => 1],
            ['api_key' => config('services.stripe.secret')]
        )['data']);

        $this->assertEquals(50000, $lastCharge['amount']);
        $this->assertEquals(env('STRIPE_TEST_PROMOTER_ID'), $lastCharge['destination']);

        $transfer = Transfer::retrieve($lastCharge['transfer'], ['api_key' => config('services.stripe.secret')]);

        $this->assertEquals(45000, $transfer['amount']);
    }
}
