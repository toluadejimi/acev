<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSession
{
    public function handle($request, Closure $next)
    {

//        dd($request->all());
//
//        if (Auth::check()) {
//            $user = Auth::user();
//            if ($user->session_id !== session()->getId()) {
//                Auth::logout();
//                return redirect('/login')->withErrors('You have been logged out due to another login.');
//            }
//        }
//
//        return $next($request);
    }
}
