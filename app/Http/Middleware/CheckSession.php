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

            $ck = User::where('email', $request->email)->first()->verify ?? null;
            if ($ck == 0) {
                $email = $request->email;
                $expiryTimestamp = time() + 24 * 60 * 60; // 24 hours in seconds
                $url = url('') . "/verify-account-now?code=$expiryTimestamp&email=$request->email";
                $username = User::where('email', $request->email)->first()->username ?? null;

                User::where('email', $email)->update([
                    'code' => $expiryTimestamp
                ]);

                $data = array(
                    'fromsender' => 'noreply@acesmsverify.com', 'ACEVERIFY',
                    'subject' => "Verify Account",
                    'toreceiver' => $email,
                    'url' => $url,
                    'user' => $username,
                );


                Mail::send('verify-account', ["data1" => $data], function ($message) use ($data) {
                    $message->from($data['fromsender']);
                    $message->to(Auth::user()->email);
                    $message->subject($data['subject']);
                });

                $username = Auth::user()->username;
                return back()->with('message', 'Account verification has been sent to your email, Verify your account');

            }

            if ($user->session_id !== session()->getId()) {
                Auth::logout();
                return redirect('/login')->withErrors('You have been logged out due to another login.');
            }
        }

        return $next($request);
    }
}
