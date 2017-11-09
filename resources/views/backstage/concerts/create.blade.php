@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-md-10 col-md-offset-1">
            <h1>Add a concert</h1>
            <hr>
        </div>
    </div>
    <form action="/backstage/concerts" method="post">
        {{ csrf_field() }}
        <div class="row">
            @if ($errors->any())
                <div class="col-xs-12 col-md-10 col-md-offset-1">
                    <div class="alert alert-danger">
                        <h2>
                            There {{ $errors->count() == 1 ? 'is' : 'are' }} {{ $errors->count() }} {{ str_plural('error', $errors->count() )}}
                            with this concert:
                        </h2>
                        <ul class="text-danger">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-5 col-md-offset-1">
                <h3>Concert details</h3>
                <p class="text-muted">Tell us who's playing! <em>Please be Slayer!</em></p>
                <p class="text-muted">
                    Include the headliner in the concert name, use the subtitle section to list any
                    opening bands, and any important information to the description.
                </p>
            </div>
            <div class="col-xs-12 col-sm-5">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        placeholder="The Headliners"
                        class="form-control"
                        value="{{ old('title') }}"
                    >
                </div>
                <div class="form-group">
                    <label for="subtitle">Subtitle</label>
                    <input
                        type="text"
                        id="subtitle"
                        name="subtitle"
                        placeholder="With the openers (Optional)"
                        class="form-control"
                        value="{{ old('subtitle') }}"
                    >
                </div>
                <div class="form-group">
                    <label for="additional_information">Additional information</label>
                    <textarea
                        name="additional_information"
                        id="additional_information"
                        class="form-control"
                        cols="30"
                        rows="10"
                    >{{ old('additional_information') }}</textarea>
                </div>
            </div>
            <hr class="col-xs-12 col-md-10 col-md-offset-1">
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-5 col-md-offset-1">
                <h3>Date & Time</h3>
                <p class="text-muted">
                    True metalheads really only care about the obscure openers, so make sure they
                    don't get late there!
                </p>
            </div>
            <div class="col-xs-12 col-sm-5">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="date">Date</label>
                            <input
                                type="date"
                                id="date"
                                name="date"
                                placeholder="yyyy-mm-dd"
                                class="form-control"
                                value="{{ old('date') }}"
                            >
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="time">Start Time</label>
                            <input
                                type="text"
                                id="time"
                                name="time"
                                placeholder="7:00pm"
                                class="form-control"
                                value="{{ old('time') }}"
                            >
                        </div>
                    </div>
                </div>
            </div>
            <hr class="col-xs-12 col-md-10 col-md-offset-1">
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-5 col-md-offset-1">
                <h3>Venue information</h3>
                <p class="text-muted">
                    Where is the show? Let attendees know the venue name and address so they can
                    bring the mosh
                </p>
            </div>
            <div class="col-xs-12 col-sm-5">
                <div class="form-group">
                    <label for="venue">Venue Name</label>
                    <input
                        type="text"
                        id="venue"
                        name="venue"
                        placeholder="The Mosh Pit"
                        class="form-control"
                        value="{{ old('venue') }}"
                    >
                </div>
                <div class="form-group">
                    <label for="venue_address">Street Address</label>
                    <input
                        type="text"
                        id="venue_address"
                        name="venue_address"
                        placeholder="500 Example Ave."
                        class="form-control"
                        value="{{ old('venue_address') }}"
                    >
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input
                                type="text"
                                id="city"
                                name="city"
                                placeholder="Laraville"
                                class="form-control"
                                value="{{ old('city') }}"
                            >
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="state">State/Province</label>
                            <input
                                type="text"
                                id="state"
                                name="state"
                                placeholder="ON"
                                class="form-control"
                                value="{{ old('state') }}"
                            >
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label for="zip">ZIP</label>
                        <input
                            type="text"
                            id="zip"
                            name="zip"
                            placeholder="90210"
                            class="form-control"
                            value="{{ old('zip') }}"
                        >
                    </div>
                </div>
            </div>
            <hr class="col-xs-12 col-md-10 col-md-offset-1">
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-5 col-md-offset-1">
                <h3>Tickets & Pricing</h3>
                <p class="text-muted">
                    Set your ticket price and availability, but don't forget, metalheads are cheap
                    so keep it reasonable.
                </p>
            </div>
            <div class="col-xs-12 col-sm-5">
                <div class="row">
                    <div class="col-sm-6">
                        <label for="ticket_price">Price</label>
                        <input
                            type="text"
                            id="ticket_price"
                            name="ticket_price"
                            placeholder="$0.00"
                            class="form-control"
                            value="{{ old('ticket_price') }}"
                        >
                    </div>
                    <div class="col-sm-6">
                        <label for="ticket_quantity">Tickets Available</label>
                        <input
                            type="text"
                            id="ticket_quantity"
                            name="ticket_quantity"
                            placeholder="250"
                            class="form-control"
                            value="{{ old('ticket_quantity') }}"
                        >
                    </div>
                </div>
            </div>
            <hr class="col-xs-12 col-md-10 col-md-offset-1">
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-10 col-md-offset-1">
                <button class="btn btn-primary btn-block">Add Concert</button>
            </div>
        </div>
    </form>
@endsection
