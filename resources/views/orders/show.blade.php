@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <h1>Order Summary</h1>
            <a href="#" class="pull-left">{{ $order->confirmation_number }}</a>
            <hr>
            <p class="lead">Order total: ${{ number_format($order->amount / 100, 2)  }}</p>
            <p class="muted">
                Billed to card #: **** **** **** {{ $order->card_last_four_digits }}
            </p>
            <hr>
            <h2>Your tickets</h2>
            @foreach($order->tickets as $ticket)
            <div class="well">
                <p>{{ $ticket->code }}</p>
            </div>
            @endforeach
        </div>
    </div>
@endsection
