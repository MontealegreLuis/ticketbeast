<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailed;
use App\Billing\PaymentGateway;
use App\Concert;
use App\Http\Requests\PurchaseTicketsRequest;
use App\NotEnoughTickets;

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
            $concert->orderTickets(\request('email'), $ticketQuantity);

            $amount = $ticketQuantity * $concert->ticket_price;
            $this->paymentGateway->charge($amount, request('payment_token'));


            return response()->json([], 201);
        } catch (PaymentFailed | NotEnoughTickets $exception) {
            return response()->json([], 422);
        }
    }
}
