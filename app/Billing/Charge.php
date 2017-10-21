<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace App\Billing;

class Charge
{
    /** @var array */
    private $details;

    public function __construct(array $details)
    {
        $this->details = $details;
    }

    public function amount(): int
    {
        return $this->details['amount'];
    }

    public function cardLastFour(): string
    {
        return $this->details['card_last_four'];
    }
}