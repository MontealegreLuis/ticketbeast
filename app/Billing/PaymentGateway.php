<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace App\Billing;

interface PaymentGateway
{
    public function getValidTestToken();

    public function charge($amountInCents, $validTestToken);

    public function totalCharges();
}