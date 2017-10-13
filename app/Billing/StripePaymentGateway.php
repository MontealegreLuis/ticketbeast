<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace App\Billing;

use Stripe\Charge;
use Stripe\Error\InvalidRequest;
use Stripe\Token;

class StripePaymentGateway implements PaymentGateway
{
    /** @var string */
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function charge(int $amount, string $token): void
    {
        try {
            Charge::create([
                'amount' => $amount,
                'currency' => 'usd',
                'source' => $token,
                'description' => 'Tickets paid - Thank you! -- Ticketbeast',
            ], ['api_key' => $this->apiKey]);
        } catch (InvalidRequest $e) {
            throw new PaymentFailed($e);
        }
    }

    public function getValidTestToken(): string
    {
        return Token::create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 12,
                'exp_year' => date('Y') + 1,
                'cvc' => '123'
            ],
        ], ['api_key' => $this->apiKey])->id;
    }

    public function newChargesDuring(callable $callback)
    {
        $latestCharge = $this->lastCharge();
        $callback($this);
        return $this->newChargesSince($latestCharge)->pluck('amount');
    }

    private function lastCharge()
    {
        return array_first(Charge::all(
            ['limit' => 1],
            ['api_key' => $this->apiKey]
        )['data']);
    }

    public function newChargesSince($charge = null)
    {
        $charges = Charge::all(
            ['ending_before' => $charge ? $charge->id : null],
            ['api_key' => $this->apiKey]
        )['data'];
        return collect($charges);
    }
}
