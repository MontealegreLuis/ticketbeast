@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-lg-6 col-lg-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h1>Join TicketBeast</h1>
                </div>
                <div class="panel-body">
                    <form action="/register" method="POST">
                        {{ csrf_field() }}
                        <input type="hidden" name="invitation_code" value="{{ $invitation->code }}">
                        <div class="form-group {{ $errors->first('email', 'has-error') }}">
                            <label class="form-label">Email address</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-user"></span>
                                </span>
                                <input type="email" name="email" class="form-control"
                                       placeholder="Email address" value="{{ old('email') }}">
                            </div>
                            @if ($errors->has('email'))
                                <p class="text-danger">{{ $errors->first('email') }}</p>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->first('password', 'has-error') }}">
                            <label class="form-label pseudo-hidden">Password</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-lock"></span>
                                </span>
                                <input type="password" name="password" class="form-control"
                                       placeholder="Password">
                            </div>
                            @if ($errors->has('password'))
                                <p class="text-danger">{{ $errors->first('password') }}</p>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-block btn-primary">
                            Create Account
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
