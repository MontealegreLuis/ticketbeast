<?php

/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace App;

class Reservation
{
    private $tickets;
    private $email;

    public function __construct($tickets, $email)
    {
        $this->tickets = $tickets;
        $this->email = $email;
    }

    public function tickets()
    {
        return $this->tickets;
    }

    public function email()
    {
        return $this->email;
    }

    public function totalCost()
    {
        return $this->tickets->sum('price');
    }

    public function cancel()
    {
        $this->tickets->each(function ($ticket) {
            $ticket->release();
        });
    }
}