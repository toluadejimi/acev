<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureLandingTurnstile
{
    /**
     * @var list<string>
     */
    protected static array $botSubstrings = [
        'Googlebot',
        'Google-InspectionTool',
        'Bingbot',
        'Slurp',
        'DuckDuckBot',
        'Baiduspider',
        'YandexBot',
        'facebookexternalhit',
        'Facebot',
        'Twitterbot',
        'LinkedInBot',
        'Applebot',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('services.cloudflare.turnstile.landing_gate', true)) {
            return $next($request);
        }

        $siteKey = config('services.cloudflare.turnstile.site_key');
        $siteSecret = config('services.cloudflare.turnstile.site_secret');
        if (! $siteKey || ! $siteSecret) {
            return $next($request);
        }

        if (Auth::check()) {
            return $next($request);
        }

        if ($this->isLikelyGoodBot($request)) {
            return $next($request);
        }

        if ($request->session()->get('landing_turnstile_verified')) {
            return $next($request);
        }

        if ($request->routeIs('landing.human-check', 'landing.human-check.verify')) {
            return $next($request);
        }

        $request->session()->put('landing_turnstile_intended', $request->getRequestUri());

        return redirect()->route('landing.human-check');
    }

    protected function isLikelyGoodBot(Request $request): bool
    {
        $ua = (string) $request->userAgent();
        if ($ua === '') {
            return false;
        }

        foreach (self::$botSubstrings as $needle) {
            if (stripos($ua, $needle) !== false) {
                return true;
            }
        }

        return false;
    }
}
