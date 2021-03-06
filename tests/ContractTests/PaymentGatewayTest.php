<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Tests\ContractTests;

use App\Billing\PaymentFailed;
use App\Billing\PaymentGateway;
use Tests\TestCase;

abstract class PaymentGatewayTest extends TestCase
{
    /** @test */
    function it_charges_successfully_using_a_valid_token()
    {
        $paymentGateway = $this->newPaymentGateway();

        $newCharges = $paymentGateway->newChargesDuring(function (PaymentGateway $paymentGateway) {
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken(), env('STRIPE_TEST_PROMOTER_ID'));
        });

        $this->assertCount(1, $newCharges);
        $this->assertEquals(2500, $newCharges->map->amount()->sum());
    }

    /** @test */
    function it_get_details_about_a_successful_charge()
    {
        $paymentGateway = $this->newPaymentGateway();

        $charge = $paymentGateway->charge(
            2500,
            $paymentGateway->getValidTestToken(),
            env('STRIPE_TEST_PROMOTER_ID')
        );

        $this->assertEquals('4242', $charge->cardLastFour());
        $this->assertEquals(2500, $charge->amount());
        $this->assertEquals(env('STRIPE_TEST_PROMOTER_ID'), $charge->destination());
    }

    /** @test */
    function it_fetches_charges_created_during_a_callback()
    {
        $paymentGateway = $this->newPaymentGateway();
        $paymentGateway->charge(
            2000,
            $paymentGateway->getValidTestToken(),
            env('STRIPE_TEST_PROMOTER_ID')
        );
        $paymentGateway->charge(
            3000,
            $paymentGateway->getValidTestToken(),
            env('STRIPE_TEST_PROMOTER_ID')
        );

        $newCharges = $paymentGateway->newChargesDuring(function (PaymentGateway $paymentGateway) {
            $paymentGateway->charge(
                4000,
                $paymentGateway->getValidTestToken(),
                env('STRIPE_TEST_PROMOTER_ID')
            );
            $paymentGateway->charge(
                5000,
                $paymentGateway->getValidTestToken(),
                env('STRIPE_TEST_PROMOTER_ID')
            );
        });

        $this->assertCount(2, $newCharges);
        $this->assertEquals([5000, 4000], $newCharges->map->amount()->all());
    }

    /** @test */
    function it_fails_to_charge_using_an_invalid_token()
    {
        $paymentGateway = $this->newPaymentGateway();

        $newCharges = $paymentGateway->newChargesDuring(function (PaymentGateway $paymentGateway) {
            try {
                $paymentGateway->charge(2500, 'invalid-token', env('STRIPE_TEST_PROMOTER_ID'));
            } catch (PaymentFailed $ignore) {
                return;
            }
            $this->fail('Charging with an invalid token did not throw PaymentFailed');
        });

        $this->assertCount(0, $newCharges);
    }

    abstract function newPaymentGateway(): PaymentGateway;
}
