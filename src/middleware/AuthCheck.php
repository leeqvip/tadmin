<?php

namespace tadmin\middleware;

use tadmin\service\auth\facade\Auth;

class AuthCheck
{
    public function handle($request, \Closure $next)
    {
        if (Auth::guard()->guest()) {
            return redirect('tadmin.auth.passport.login');
        }

        return $next($request);
    }
}
