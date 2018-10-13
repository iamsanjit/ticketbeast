<?php

namespace App\Http\Middleware;


class Authenticate extends \Illuminate\Auth\Middleware\Authenticate
{
    protected function redirectTo($request)
    {
        return route('auth.login');
    }
}