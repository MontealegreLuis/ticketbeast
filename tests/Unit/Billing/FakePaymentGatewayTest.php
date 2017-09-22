<?php

/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace Tests\Unit\Billing;

use App\Billing\PaymentFailed;
use Tests\Feature\FakePaymentGateway;
use Tests\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    /** @test */
    function it_charges_successfully_using_a_valid_token()
    {
        $paymentGateway = new FakePaymentGateway();

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        $this->assertEquals(2500, $paymentGateway->totalCharges());
    }

    /** @test */
    function it_fails_to_charge_using_an_invalid_token()
    {
        $paymentGateway = new FakePaymentGateway();

        $this->expectException(PaymentFailed::class);

        $paymentGateway->charge(2500, 'invalid-token');
    }
}