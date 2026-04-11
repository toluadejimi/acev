<?php

namespace App\Http\Controllers;

use App\Services\TurnstileVerifier;
use Illuminate\Http\Request;

class LandingHumanController extends Controller
{
    public function show(Request $request)
    {
        if (! config('services.cloudflare.turnstile.site_key')) {
            return redirect('/');
        }

        return view('landing-human-check');
    }

    public function verify(Request $request, TurnstileVerifier $turnstile)
    {
        $token = $request->input('cf-turnstile-response');
        if (! $turnstile->verify($token, $request->ip())) {
            return back()->with('error', 'Please complete the verification and try again.');
        }

        $request->session()->put('landing_turnstile_verified', true);

        $uri = $request->session()->pull('landing_turnstile_intended', '/');
        if (! is_string($uri) || $uri === '' || str_starts_with($uri, '//')) {
            $uri = '/';
        }

        return redirect()->to($uri);
    }
}
