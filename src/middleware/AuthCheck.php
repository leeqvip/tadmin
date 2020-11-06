<?php

namespace tadmin\middleware;

use tadmin\service\auth\facade\Auth;

class AuthCheck
{
    public function handle($request, \Closure $next)
    {
        if (Auth::guard()->guest()) {
            return redirect((string)url('tadmin.auth.passport.login'), 302);
        }

        return $next($request);
    }
}
