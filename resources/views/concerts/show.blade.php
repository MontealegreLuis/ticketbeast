@extends('layouts.master')

@section('csrf')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    @if ($concert->hasPoster())
        @include('concerts.partials.concert-with-poster', ['concert' => $concert])
    @else
        @include('concerts.partials.concert-without-poster', ['concert' => $concert])
    @endif
@endsection

@section('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="/js/stripe.js"></script>
@endsection
