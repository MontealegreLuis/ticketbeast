<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Tests\Unit\Billing;

use App\Billing\PaymentGateway;
use Tests\ContractTests\PaymentGatewayTest;
use Tests\Feature\FakePaymentGateway;

class FakePaymentGatewayTest extends PaymentGatewayTest
{
    /** @test */
    function it_can_execute_a_hook_before_charging()
    {
        $paymentGateway = new FakePaymentGateway();
        $timesCallbackRan = 0;

        $paymentGateway->beforeCharge(function ($paymentGateway) use (&$timesCallbackRan) {
            $timesCallbackRan++;
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken(), 'test_acct_1234');
            $this->assertEquals(2500, $paymentGateway->totalCharges());
        });

        $paymentGateway->charge(
            2500,
            $paymentGateway->getValidTestToken(),
            'test_acct_1234'
        );

        $this->assertEquals(1, $timesCallbackRan);
        $this->assertEquals(5000, $paymentGateway->totalCharges());
    }

    /** @test */
    function it_can_get_total_charges_for_a_specific_account()
    {
        $gateway = new FakePaymentGateway();

        $gateway->charge(1000, $gateway->getValidTestToken(), 'test_acct_0000');
        $gateway->charge(2500, $gateway->getValidTestToken(), 'test_acct_1234');
        $gateway->charge(4000, $gateway->getValidTestToken(), 'test_acct_1234');

        $this->assertEquals(6500, $gateway->totalChargesFor('test_acct_1234'));
    }


    function newPaymentGateway(): PaymentGateway
    {
        return new FakePaymentGateway();
    }
}
