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
                <p class="lead orders-label">
                    Orders
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
@endsection
