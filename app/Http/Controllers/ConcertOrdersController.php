<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Concert;
use App\Http\Requests\PurchaseTicketsRequest;

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

    public function store($concertId, PurchaseTicketsRequest $request)
    {
        try {
            $concert = Concert::published()->findOrFail($concertId);

            $ticketQuantity = \request('ticket_quantity');
            $amount = $ticketQuantity * $concert->ticket_price;
            $this->paymentGateway->charge($amount, request('payment_token'));

            $concert->orderTickets(\request('email'), $ticketQuantity);

            return response()->json([], 201);
        } catch (PaymentFailedException $exception) {
            return response()->json([], 422);
        }
    }
}
