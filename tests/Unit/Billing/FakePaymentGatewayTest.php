<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Tests\Unit\Billing;

use App\Billing\PaymentFailed;
use App\Billing\PaymentGateway;
use Tests\Feature\FakePaymentGateway;
use Tests\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    /** @test */
    function can_fetch_charges_created_during_a_callback()
    {
        $paymentGateway = $this->newPaymentGateway();
        $paymentGateway->charge(2000, $paymentGateway->getValidTestToken());
        $paymentGateway->charge(3000, $paymentGateway->getValidTestToken());

        $newCharges = $paymentGateway->newChargesDuring(function (PaymentGateway $paymentGateway) {
                $paymentGateway->charge(4000, $paymentGateway->getValidTestToken());
                $paymentGateway->charge(5000, $paymentGateway->getValidTestToken());
            });

        $this->assertCount(2, $newCharges);
        $this->assertEquals([4000, 5000], $newCharges->all());
    }

    /** @test */
    function it_charges_successfully_using_a_valid_token()
    {
        $paymentGateway = $this->newPaymentGateway();

        $newCharges = $paymentGateway->newChargesDuring(function (PaymentGateway $paymentGateway) {
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
        });

        $this->assertCount(1, $newCharges);
        $this->assertEquals(2500, $newCharges->sum());
    }

    /** @test */
    function it_fails_to_charge_using_an_invalid_token()
    {
        $paymentGateway = $this->newPaymentGateway();

        $this->expectException(PaymentFailed::class);

        $paymentGateway->charge(2500, 'invalid-token');
    }
    
    /** @test */
    function it_can_execute_a_hook_before_charging()
    {
        $paymentGateway = $this->newPaymentGateway();
        $timesCallbackRan = 0;

        $paymentGateway->beforeCharge(function ($paymentGateway) use (&$timesCallbackRan) {
            $timesCallbackRan++;
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
            $this->assertEquals(2500, $paymentGateway->totalCharges());
        });

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        $this->assertEquals(1, $timesCallbackRan);
        $this->assertEquals(5000, $paymentGateway->totalCharges());
    }

    private function newPaymentGateway(): PaymentGateway
    {
        return new FakePaymentGateway();
    }
}