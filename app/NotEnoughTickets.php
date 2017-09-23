<?php

/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace App;

use RuntimeException;

class NotEnoughTickets extends RuntimeException
{
    public static function available($count, $ticketQuantity)
    {
        return new NotEnoughTickets(
            "Cannot order $ticketQuantity, only $count tickets available"
        );
    }
}