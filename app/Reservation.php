<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace App;

use App\Billing\PaymentGateway;
use Illuminate\Support\Collection;

class Reservation
{
    /** @var Collection */
    private $tickets;

    /** @var string */
    private $email;

    public function __construct(Collection $tickets, string $email)
    {
        $this->tickets = $tickets;
        $this->email = $email;
    }

    public function tickets(): Collection
    {
        return $this->tickets;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function totalCost(): int
    {
        return $this->tickets->sum('price');
    }

    public function cancel(): void
    {
        $this->tickets->each(function ($ticket) {
            $ticket->release();
        });
    }

    public function complete(PaymentGateway $paymentGateway, string $paymentToken): Order
    {
        $paymentGateway->charge($this->totalCost(), $paymentToken);
        return Order::forPurchase($this->tickets(), $this->email(), $this->totalCost());
    }
}