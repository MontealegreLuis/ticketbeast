<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Tests\Unit\Billing;

use App\Billing\PaymentFailed;
use App\Billing\PaymentGateway;
use Tests\ContractTests\PaymentGatewayTest;
use Tests\Feature\FakePaymentGateway;

class FakePaymentGatewayTest extends PaymentGatewayTest
{
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

    function newPaymentGateway(): PaymentGateway
    {
        return new FakePaymentGateway();
    }
}