<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace App\Billing;

interface PaymentGateway
{
    public function charge(int $amountInCents, string $validToken): Charge;

    public function getValidTestToken(string $cardNumber): string;

    public function newChargesDuring(callable $callback);
}
