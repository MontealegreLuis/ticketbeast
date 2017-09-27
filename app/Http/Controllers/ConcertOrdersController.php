<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailed;
use App\Billing\PaymentGateway;
use App\Concert;
use App\Http\Requests\PurchaseTicketsRequest;
use App\NotEnoughTickets;
use App\Order;
use App\Reservation;

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
            $reservation = $concert->reserveTickets(\request('ticket_quantity'));

            $this->paymentGateway->charge($reservation->totalCost(), request('payment_token'));

            $order = Order::forPurchase($reservation->tickets(), \request('email'), $reservation->totalCost());

            return response()->json($order->toArray(), 201);
        } catch (PaymentFailed $exception) {
            $reservation->cancel();
            return response()->json([], 422);
        } catch (NotEnoughTickets $exception) {
            return response()->json([], 422);
        }
    }
}
