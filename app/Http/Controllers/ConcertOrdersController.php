<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace App\Http\Controllers;

use App\Billing\PaymentFailed;
use App\Billing\PaymentGateway;
use App\Concert;
use App\Http\Requests\PurchaseTicketsRequest;
use App\IdentifierGenerator;
use App\NotEnoughTickets;

class ConcertOrdersController extends Controller
{
    /** @var PaymentGateway */
    private $paymentGateway;

    /** @var IdentifierGenerator */
    private $generator;

    public function __construct(
        PaymentGateway $paymentGateway,
        IdentifierGenerator $generator
    ) {
        $this->paymentGateway = $paymentGateway;
        $this->generator = $generator;
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
                $this->generator
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
