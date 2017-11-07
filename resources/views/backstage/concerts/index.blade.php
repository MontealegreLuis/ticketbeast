@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <h1>Your concerts</h1>
            <a href="{{ route('backstage.concerts.new') }}" class="btn btn-primary">
                Add concert
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <h2>Published</h2>
            <div class="row">
                @foreach ($concerts->filter->isPublished() as $concert)
                    <div class="col-xs-12 col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h1>{{ $concert->title }}</h1>
                                <p class="lead">{{ $concert->subtitle }}</p>
                            </div>
                            <div class="panel-body">
                                <p>
                                    <span class="glyphicon glyphicon-map-marker"></span>
                                    {{ $concert->venue }} &ndash; {{ $concert->city }}
                                    , {{ $concert->state }}
                                </p>
                                <p>
                                    <span class="glyphicon glyphicon-calendar"></span>
                                    {{ $concert->formatted_date }}
                                    @ {{ $concert->formatted_start_time }}
                                </p>
                            </div>
                            <a
                                href="{{ route('concerts.show', $concert) }}"
                                class="btn btn-block btn-default"
                            >
                                Get Ticket Link
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <h2>Drafts</h2>
            <div class="row">
                @foreach ($concerts->reject->isPublished() as $concert)
                    <div class="col-xs-12 col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h1>{{ $concert->title }}</h1>
                                <p class="lead">{{ $concert->subtitle }}</p>
                            </div>
                            <div class="panel-body">
                                <p>
                                    <span class="glyphicon glyphicon-map-marker"></span>
                                    {{ $concert->venue }} &ndash; {{ $concert->city }}
                                    , {{ $concert->state }}
                                </p>
                                <p>
                                    <span class="glyphicon glyphicon-calendar"></span>
                                    {{ $concert->formatted_date }}
                                    @ {{ $concert->formatted_start_time }}
                                </p>
                            </div>
                            <a
                                href="{{ route('backstage.concerts.edit', $concert) }}"
                                class="btn btn-block btn-default"
                            >
                                Edit
                            </a>
                            <form
                                class="form-inline"
                                action="{{ route('backstage.published-concerts.store') }}"
                                method="POST"
                            >
                                {{ csrf_field() }}
                                <input
                                    type="hidden"
                                    name="concert_id"
                                    value="{{ $concert->id }}"
                                >
                                <button type="submit" class="btn btn-block btn-primary">
                                    Publish
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
