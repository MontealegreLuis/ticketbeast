<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailed;
use App\Billing\PaymentGateway;
use App\Concert;
use App\ConfirmationNumberGenerator;
use App\Http\Requests\PurchaseTicketsRequest;
use App\NotEnoughTickets;

class ConcertOrdersController extends Controller
{
    /** @var PaymentGateway */
    private $paymentGateway;

    /** @var ConfirmationNumberGenerator */
    private $numberGenerator;

    public function __construct(
        PaymentGateway $paymentGateway,
        ConfirmationNumberGenerator $numberGenerator
    ) {
        $this->paymentGateway = $paymentGateway;
        $this->numberGenerator = $numberGenerator;
    }

    public function store($concertId, PurchaseTicketsRequest $request)
    {
        /** @var \App\Concert $concert */
        $concert = Concert::published()->findOrFail($concertId);
        try {
            $reservation = $concert->reserveTickets(\request('ticket_quantity'), \request('email'));

            $order = $reservation->complete(
                $this->paymentGateway,
                \request('payment_token'),
                $this->numberGenerator
            );

            return response()->json($order, 201);
        } catch (PaymentFailed $exception) {
            $reservation->cancel();
            return response()->json([], 422);
        } catch (NotEnoughTickets $exception) {
            return response()->json([], 422);
        }
    }
}
