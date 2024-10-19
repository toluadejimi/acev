<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Token;

class SessionTimeout
{
    public function handle($request, Closure $next)
    {

//        $token =
//
//        $tokenRecord = Token::where('id', $token)->first();
//
//        if ($tokenRecord && !$tokenRecord->revoked && $tokenRecord->expires_at > now()) {
//            return response()->json(['message' => 'Token is valid']);
//        }
//
//        return response()->json(['message' => 'Invalid token'], 401);
//
//
//            if (time() - $lastActivity > $timeout) {
//                Auth::logout();
//                session()->flush();
//                return redirect('/login')->with('error', 'Your session has expired due to inactivity. Please log in again.');
//            }


        return $next($request);
    }
}
