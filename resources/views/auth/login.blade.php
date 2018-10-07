@extends('layouts.master');

@section('content')
    <div class="login">
        <div class="wrapper">
            <form class="form-signin" method="post" action="{{ url('login') }}">
            @csrf
            <h2 class="form-signin-heading">Backstage Login</h2>
            <input type="email" class="form-control" name="email" placeholder="Email Address" required="" autofocus="" />
            <input type="password" class="form-control" name="password" placeholder="Password" required=""/>      
            <button class="btn btn-lg btn-primary btn-block" type="submit">Log in</button>   
            <div class="text-center pt-20">
                <span class="text-danger">{{ $errors->first('email') }}</span>
            </div>
            </form>
        </div>
    </div>
@endsection