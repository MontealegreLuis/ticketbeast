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
    /** @var \Illuminate\Support\Collection  */
    private $charges;

    /** @var callable */
    private $beforeChargeCallback;

    public function __construct()
    {
        $this->charges = collect();
    }

    public function getValidTestToken(): string
    {
        return 'valid-token';
    }

    public function charge(int $amountInCents, string $token): void
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

    public function totalCharges(): int
    {
        return $this->charges->sum();
    }

    public function beforeCharge(callable $callback): void
    {
        $this->beforeChargeCallback = $callback;
    }

    public function newChargesDuring(callable $callback)
    {
        $chargesFrom = $this->charges->count();
        $callback($this);
        return $this->charges->slice($chargesFrom)->reverse()->values();
    }
}