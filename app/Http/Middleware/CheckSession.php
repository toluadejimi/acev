<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CheckSession
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->session_id !== session()->getId()) {
                if(Auth::user()->verify == 0){
                    $email = Auth::user()->email;
                    $email = send_verification_email($email);
                    return redirect('/login')->withErrors('Account verification has been sent to your email, Verify your account.');
                }else{
                    Auth::logout();
                    return redirect('/login')->withErrors('You have been logged out due to another login.');
                }
                }

        }

        return $next($request);
    }
}
