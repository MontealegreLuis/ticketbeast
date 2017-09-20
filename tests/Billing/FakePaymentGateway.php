<?php

/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace Tests\Feature;

use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;

class FakePaymentGateway implements PaymentGateway
{
    private $charges;

    public function __construct()
    {
        $this->charges = collect();
    }

    public function getValidTestToken()
    {
        return 'valid-token';
    }

    public function charge($amountInCents, $token)
    {
        if ($token !== $this->getValidTestToken()) {
            throw new PaymentFailedException("Invalid token '$token' provided");
        }
        $this->charges[] = $amountInCents;
    }

    public function totalCharges()
    {
        return $this->charges->sum();
    }
}