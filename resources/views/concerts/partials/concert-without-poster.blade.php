<div class="col-md-offset-3 col-md-6">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h1 id="concert-title">{{ $concert->title }}</h1>
            <h2>
                <small>{{ $concert->subtitle }}</small>
            </h2>
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

            <form action="/concerts/{{ $concert->id }}/orders" method="POST">
                <input type="hidden" id="concert-id" value="{{ $concert->id }}">
                <div class="row">
                    <div class="col-xs-6 form-group">
                        <label>Price</label>
                        <span class="form-control" id="ticket-price">
                                ${{ $concert->ticketPriceInDollars }}
                            </span>
                    </div>
                    <div class="col-xs-6 form-group">
                        <label for="quantity">Qty</label>
                        <input class="form-control" id="quantity">
                    </div>
                    <button class="btn btn-primary btn-block" id="buy-tickets">
                        Buy Tickets
                    </button>
                </div>
                <script src="https://checkout.stripe.com/checkout.js"></script>
            </form>
        </div>
    </div>
</div>
