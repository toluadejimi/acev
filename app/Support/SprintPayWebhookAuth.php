<?php

namespace App\Support;

use Illuminate\Http\Request;

class SprintPayWebhookAuth
{
    public static function extractToken(Request $request): ?string
    {
        foreach (['X-Auth-Token', 'X-Webhook-Secret', 'X-SprintPay-Token'] as $header) {
            $v = $request->header($header);
            if ($v !== null && $v !== '') {
                return trim((string) $v);
            }
        }

        $auth = (string) $request->header('Authorization', '');
        if (stripos($auth, 'Bearer ') === 0) {
            return trim(substr($auth, 7));
        }

        return null;
    }

    public static function tokenValid(Request $request, string $secret): bool
    {
        if ($secret === '') {
            return false;
        }
        $token = self::extractToken($request);
        if ($token === null || $token === '') {
            return false;
        }

        return hash_equals($secret, $token);
    }
}
