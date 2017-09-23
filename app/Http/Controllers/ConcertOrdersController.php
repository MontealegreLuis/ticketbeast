<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailed;
use App\Billing\PaymentGateway;
use App\Concert;
use App\Http\Requests\PurchaseTicketsRequest;
use App\NotEnoughTickets;

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
        try {
            $concert = Concert::published()->findOrFail($concertId);

            $quantity = \request('ticket_quantity');
            $order = $concert->orderTickets(\request('email'), $quantity);

            $this->paymentGateway->charge($concert->ticketsTotal($quantity), request('payment_token'));


            return response()->json([], 201);
        } catch (PaymentFailed $exception) {
            $order->cancel();
            return response()->json([], 422);
        } catch (NotEnoughTickets $exception) {
            return response()->json([], 422);
        }
    }
}
