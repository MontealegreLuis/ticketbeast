@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <h1>Order Summary</h1>
            <a href="#" class="pull-left">{{ $order->confirmation_number }}</a>
            <hr>
            <p class="lead">Order total: ${{ number_format($order->amount / 100, 2)  }}</p>
            <p class="text-muted">
                Billed to card #: **** **** **** {{ $order->card_last_four_digits }}
            </p>
            <hr>
            <h2>Your tickets</h2>
            @foreach($order->tickets as $ticket)
                <div class="panel">
                    <div class="panel-heading">
                        <h3>{{ $ticket->concert->title }}</h3>
                        <h4>{{ $ticket->concert->subtitle }}</h4>
                        <div class="pull-right">
                            <p class="lead">General admission</p>
                            <p class="text-muted">Admit one</p>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="col-sm-6">
                            <div class="media">
                                <div class="media-left">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </div>
                                <div class="media-body">
                                    <p class="lead">
                                        <time datetime="{{ $ticket->concert->date->format('Y-m-d H:i') }}">
                                            {{ $ticket->concert->date->format('l, F jS, Y') }}
                                        </time>
                                    </p>
                                    <p class="text-muted">
                                        Doors at {{ $ticket->concert->date->format('g:ia') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="media">
                                <div class="media-left">
                                    <span class="glyphicon glyphicon-map-marker"></span>
                                </div>
                                <div class="media-body">
                                    <p class="lead">{{ $ticket->concert->venue }}</p>
                                    <div class="text-muted">
                                        <p>{{ $ticket->concert->venue_address }}</p>
                                        <p>
                                            {{ $ticket->concert->city }}, {{ $ticket->concert->state }} {{ $ticket->concert->zip }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <p>{{ $ticket->code }}</p>
                        <p class="pull-right">
                            {{ $order->email }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
