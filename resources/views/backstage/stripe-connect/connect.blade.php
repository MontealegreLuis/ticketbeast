@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h1>Connect your Stripe account</h1>
                    <p class="lead text-center">
                        Good news, TicketBeast now integrates directly with your Stripe account!
                    </p>
                    <p class="text-center">
                        To continue, connect your Stripe account by clicking the button below:
                    </p>
                    <div>
                        <a
                            href="{{ route('backstage.stripe-connect.authorize') }}"
                            class="btn btn-block btn-primary"
                        >
                            Connect with Stripe
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
