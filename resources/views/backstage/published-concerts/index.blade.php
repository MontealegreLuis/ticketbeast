@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="pull-left">
                <h1>
                    {{ $concert->title }}
                    <small>
                        /
                        {{ $concert->formatted_date }}
                    </small>
                </h1>
            </div>
            <div class="pull-right">
                <p class="orders-label lead">
                    <span>Orders</span> &nbsp;&nbsp;
                    <a href="{{ route('backstage.concert-messages.new', $concert) }}">
                        Message attendees
                    </a>
                </p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12"><h2>Overview</h2></div>
        <div class="col-xs-12">
            <p class="lead">This show is {{ $concert->percentSoldOut() }}% sold out.</p>
            <progress
                class="progress"
                value="{{ $concert->ticketsSold() }}"
                max="{{ $concert->totalTickets() }}"
            >
                {{ $concert->percentSoldOut() }}%
            </progress>
            <div class="well">
                <div class="row">
                    <div class="col-xs-12 col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <h3>Total Tickets Remaining</h3>
                                <p class="text-jumbo">
                                    {{ $concert->ticketsRemaining() }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <h3>Total Tickets Sold</h3>
                                <p class="text-jumbo">
                                    {{ $concert->ticketsSold() }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <h3>Total Revenue</h3>
                                <p class="text-jumbo">
                                    ${{ $concert->revenueInDollars() }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <h2>Recent orders</h2>
        </div>
        <div class="col-xs-12">
            @if ($orders->isEmpty())
                <p class="lead text-center">No orders yet.</p>
            @else
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Email</th>
                        <th>Tickets</th>
                        <th>Amount</th>
                        <th>Card</th>
                        <th>Purchased</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        @foreach($orders as $order)
                            <td>{{ $order->email }}</td>
                            <td>{{ $order->ticketsQuantity() }}</td>
                            <td>${{ round($order->amount / 100, 2) }}</td>
                            <td class="text-muted">
                                ****{{ $order->card_last_four_digits }}
                            </td>
                            <td class="text-muted">
                                {{ $order->created_at->format('M j, Y @ g:ia') }}
                            </td>
                        @endforeach
                    </tr>
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
