<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{
    public function handle($request, Closure $next)
    {
        $lastActivity = session('last_activity');

        if (Auth::check() && $lastActivity) {
            $timeout = config('session.lifetime') * 60; 

            if (time() - $lastActivity > $timeout) {
                Auth::logout();
                session()->flush();
                return redirect('/login')->with('error', 'Your session has expired due to inactivity. Please log in again.');
            }
        }

        session(['last_activity' => time()]);

        return $next($request);
    }
}