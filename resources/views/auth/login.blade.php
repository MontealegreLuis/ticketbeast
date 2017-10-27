@extends('layouts.master')

@section('content')
    <h1>Login</h1>
    <form action="/login" method="post">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="email">Email</label>
            <input type="text" name="email" id="email" class="form-control">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control">
        </div>
        <button class="btn btn-primary">Log in</button>
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
@endsection
