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
        <div class="col-xs-12 col-md-6 col-md-offset-3">
            <h1 class="text-center">New Message</h1>

            @if (session()->has('flash'))
                <div class="alert alert-info">Message sent!</div>
            @endif

            <form action="{{ url()->route('backstage.concert-messages.store', $concert) }}" method="POST">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input
                        id="subject"
                        name="subject"
                        class="form-control"
                        value="{{ old('subject') }}"
                    >
                    @if ($errors->has('subject'))
                        <div class="alert alert-danger">
                            @foreach ($errors->get('subject') as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea
                        class="form-control"
                        name="message"
                        id="message"
                        rows="10"
                    >{{ old('message') }}</textarea>
                    @if ($errors->has('message'))
                        <div class="alert alert-danger">
                            @foreach ($errors->get('message') as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div>
                    <button class="btn btn-primary btn-block">Send Now</button>
                </div>
            </form>
        </div>
    </div>
@endsection
