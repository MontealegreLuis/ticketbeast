@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-lg-6 col-lg-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h1>Login</h1>
                </div>
                <div class="panel-body">
                    <form action="/login" method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" name="email" id="email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password"
                                   class="form-control">
                        </div>
                        <button class="btn btn-block btn-primary">Log in</button>
                        @if($errors->any())
                            <div
                                class="alert alert-danger"
                                role="alert"
                                style="margin-top: 1em"
                            >
                                These credentials do not match our records.
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
