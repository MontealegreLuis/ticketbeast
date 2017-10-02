@extends('layouts.master')

@section('content')
    <div class="col-md-offset-3 col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1>{{ $concert->title }}</h1>
                <h2><small>{{ $concert->subtitle }}</small></h2>
            </div>
            <div class="panel-body">
                <p class="lead">
                    <span class="glyphicon glyphicon-calendar"></span>
                    {{ $concert->formattedDate }}
                </p>
                <p class="lead">
                    <span class="glyphicon glyphicon-time"></span>
                    Doors open at {{ $concert->formattedStartTime }}
                </p>
                <p class="lead">
                    <span class="glyphicon glyphicon-usd"></span>
                    {{ $concert->ticketPriceInDollars }}
                </p>
                <p class="lead">
                    <span class="glyphicon glyphicon-map-marker"></span>
                    {{ $concert->venue }}
                </p>
                <p class="lead text-muted">
                    &nbsp;{{ $concert->venue_address }}
                    <br>
                    &nbsp;{{ $concert->city }}, {{ $concert->state }} {{ $concert->zip }}
                </p>
                <p class="lead">
                    <span class="glyphicon glyphicon-info-sign"></span>
                    Additional information
                </p>
                <p class="lead text-muted">
                    &nbsp;{{ $concert->additional_information }}
                </p>
                <div class="form-group">
                    <label class="lead">
                        <span class="glyphicon glyphicon-tag"></span>
                        Tickets
                    </label>
                    <input
                        type="number"
                        class="form-control"
                        id="tickets-quantity"
                        data-price="{{ $concert->ticketPriceInDollars }}"
                    >
                </div>
            </div>
            <form action="/concerts/{{ $concert->id }}/orders" method="POST">
                <script
                    id="stripe-form"
                    src="https://checkout.stripe.com/checkout.js"
                    class="stripe-button"
                    data-key="pk_test_qNELjqOYTWiuiNQ4kS2jMNGz"
                    data-amount="0"
                    data-name="Ticketbeast"
                    data-description="Orders Widget"
                    data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
                    data-locale="auto">
                </script>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="/js/stripe.js"></script>
@endsection
