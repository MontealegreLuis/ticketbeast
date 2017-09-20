<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGateway;
use App\Concert;

class ConcertOrdersController extends Controller
{
    private $paymentGateway;

    /**
     * @param $paymentGateway
     */
    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store($concertId)
    {
        $concert = Concert::find($concertId);

        $ticketQuantity = \request('ticket_quantity');
        $amount = $ticketQuantity * $concert->ticket_price;
        $this->paymentGateway->charge($amount, request('payment_token'));

        $concert->orderTickets(\request('email'), $ticketQuantity);

        return response()->json([], 201);
    }
}
