<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace App\Billing;

use Stripe\Charge;

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
        Charge::create([
            'amount' => $amount,
            'currency' => 'usd',
            'source' => $token,
            'description' => 'Tickets paid - Thank you! -- Ticketbeast',
        ], ['api_key' => $this->apiKey]);
    }

    public function totalCharges(): int
    {
        return 0;
    }
}
