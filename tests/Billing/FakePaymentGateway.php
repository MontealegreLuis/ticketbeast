<?php

/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace Tests\Feature;

use App\Billing\PaymentFailed;
use App\Billing\PaymentGateway;

class FakePaymentGateway implements PaymentGateway
{
    private $charges;
    private $beforeChargeCallback;

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
        if (is_callable($this->beforeChargeCallback)) {
            $callback = $this->beforeChargeCallback;
            $this->beforeChargeCallback = null;
            $callback($this);
        }
        if ($token !== $this->getValidTestToken()) {
            throw PaymentFailed::withToken($token);
        }
        $this->charges[] = $amountInCents;
    }

    public function totalCharges()
    {
        return $this->charges->sum();
    }

    public function beforeCharge($callback)
    {
        $this->beforeChargeCallback = $callback;
    }
}