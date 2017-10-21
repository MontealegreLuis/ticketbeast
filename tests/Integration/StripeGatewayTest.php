<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Tests\Integration;

use App\Billing\PaymentGateway;
use App\Billing\StripePaymentGateway;
use Tests\ContractTests\PaymentGatewayTest;
use function config;

class StripeGatewayTest extends PaymentGatewayTest
{
    function newPaymentGateway(): PaymentGateway
    {
        return new StripePaymentGateway(config("services.stripe.secret"));
    }
}
