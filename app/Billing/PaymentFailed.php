<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace App\Billing;

use RuntimeException;

class PaymentFailed extends RuntimeException
{
    public static function withToken($token): PaymentFailed
    {
        return new PaymentFailed("Invalid token '$token' provided");
    }
}
