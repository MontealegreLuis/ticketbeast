<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace App\Billing;

class FakePaymentGateway implements PaymentGateway
{
    /** @var \Illuminate\Support\Collection */
    private $charges;

    /** @var \Illuminate\Support\Collection */
    private $tokens;

    /** @var callable */
    private $beforeChargeCallback;

    public function __construct()
    {
        $this->charges = collect();
        $this->tokens = collect();
    }

    public function getValidTestToken(string $cardNumber = '0000000000004242'): string
    {
        $token = 'fake-' . str_random(24);
        $this->tokens[$token] = $cardNumber;
        return $token;
    }

    public function charge(int $amountInCents, string $token, string $accountId): Charge
    {
        if (is_callable($this->beforeChargeCallback)) {
            $callback = $this->beforeChargeCallback;
            $this->beforeChargeCallback = null;
            $callback($this);
        }
        if (!$this->tokens->has($token)) {
            throw PaymentFailed::withToken($token);
        }
        return $this->charges[] = new Charge([
            'amount' => $amountInCents,
            'card_last_four' => substr($this->tokens[$token], -4),
            'destination' => $accountId,
        ]);
    }

    public function totalCharges(): int
    {
        return $this->charges->map->amount()->sum();
    }

    public function totalChargesFor(string $accountId): int
    {
        return $this->charges->filter(function ($charge) use ($accountId) {
            return $charge->destination() === $accountId;
        })->map->amount()->sum();
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
