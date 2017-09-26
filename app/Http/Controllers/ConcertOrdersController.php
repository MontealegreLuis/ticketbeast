<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailed;
use App\Billing\PaymentGateway;
use App\Concert;
use App\Http\Requests\PurchaseTicketsRequest;
use App\NotEnoughTickets;
use App\Order;

class ConcertOrdersController extends Controller
{
    /** @var PaymentGateway */
    private $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store($concertId, PurchaseTicketsRequest $request)
    {
        $concert = Concert::published()->findOrFail($concertId);
        try {
            $tickets = $concert->findTickets(\request('ticket_quantity'));

            $this->paymentGateway->charge($tickets->sum('price'), request('payment_token'));

            $order = Order::forPurchase($tickets, \request('email'));

            return response()->json($order->toArray(), 201);
        } catch (NotEnoughTickets | PaymentFailed $exception) {
            return response()->json([], 422);
        }
    }
}
