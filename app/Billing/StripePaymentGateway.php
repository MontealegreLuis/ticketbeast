<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace App\Billing;

use Stripe\Charge as StripeCharge;
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

    public function charge(int $amount, string $token, string $accountId): Charge
    {
        try {
            $stripeCharge = StripeCharge::create([
                'amount' => $amount,
                'currency' => 'usd',
                'source' => $token,
                'description' => 'Tickets paid - Thank you! -- Ticketbeast',
                'destination' => [
                    'account' => $accountId,
                    'amount' => round($amount * .9),
                ]
            ], ['api_key' => $this->apiKey]);

            return new Charge([
                'amount' => $stripeCharge['amount'],
                'card_last_four' => $stripeCharge['source']['last4'],
                'destination' => $accountId,
            ]);

        } catch (InvalidRequest $e) {
            throw new PaymentFailed($e);
        }
    }

    public function getValidTestToken(string $cardNumber = '4242424242424242'): string
    {
        return Token::create([
            'card' => [
                'number' => $cardNumber,
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
        return $this->newChargesSince($latestCharge)->map(function (StripeCharge $stripeCharge) {
            return new Charge([
                'amount' => $stripeCharge['amount'],
                'card_last_four' => $stripeCharge['source']['last4']
            ]);
        });
    }

    private function lastCharge()
    {
        return array_first(StripeCharge::all(
            ['limit' => 1],
            ['api_key' => $this->apiKey]
        )['data']);
    }

    public function newChargesSince($charge = null)
    {
        $charges = StripeCharge::all(
            ['ending_before' => $charge ? $charge->id : null],
            ['api_key' => $this->apiKey]
        )['data'];
        return collect($charges);
    }
}
