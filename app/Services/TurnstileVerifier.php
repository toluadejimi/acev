<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TurnstileVerifier
{
    public function verify(?string $token, ?string $remoteIp = null): bool
    {
        $secret = config('services.cloudflare.turnstile.site_secret');
        if (! $secret || ! $token) {
            return false;
        }

        $response = Http::asForm()->timeout(10)->post(
            'https://challenges.cloudflare.com/turnstile/v0/siteverify',
            array_filter([
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $remoteIp,
            ])
        );

        if (! $response->ok()) {
            return false;
        }

        return (bool) ($response->json('success') ?? false);
    }
}
