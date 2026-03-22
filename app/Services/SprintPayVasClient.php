<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class SprintPayVasClient
{
    public static function configured(): bool
    {
        $key = (string) config('services.sprintpay.webkey', '');
        $secret = (string) config('services.sprintpay.webhook_secret', '');

        return $key !== '' && $secret !== '';
    }

    public static function baseUrl(): string
    {
        return (string) config('services.sprintpay.api_base', 'https://web.sprintpay.online/api');
    }

    public static function webkey(): string
    {
        return (string) config('services.sprintpay.webkey', '');
    }

    public static function bearerSecret(): string
    {
        return (string) config('services.sprintpay.webhook_secret', '');
    }

    /**
     * Public catalogue (no Bearer). Paths are relative to API base, e.g. "get-data-variations".
     */
    public static function getPublic(string $path, array $query = []): Response
    {
        $url = self::baseUrl() . '/' . ltrim($path, '/');

        return Http::timeout(45)
            ->acceptJson()
            ->get($url, $query);
    }

    /**
     * Merchant VAS: Bearer = SprintPay webhook secret, body/query includes webkey.
     */
    public static function postMerchantVas(string $path, array $body = []): Response
    {
        $url = self::baseUrl() . '/' . ltrim($path, '/');
        $body['key'] = self::webkey();

        return Http::timeout(60)
            ->acceptJson()
            ->withToken(self::bearerSecret())
            ->post($url, $body);
    }

    public static function getMerchantVas(string $path, array $query = []): Response
    {
        $url = self::baseUrl() . '/' . ltrim($path, '/');
        $query['key'] = self::webkey();

        return Http::timeout(45)
            ->acceptJson()
            ->withToken(self::bearerSecret())
            ->get($url, $query);
    }

    public static function responseIndicatesSuccess(Response $response): bool
    {
        if (!$response->successful()) {
            return false;
        }

        $data = $response->json();
        if (!is_array($data)) {
            return true;
        }

        if (array_key_exists('status', $data)) {
            $s = $data['status'];
            if (is_bool($s)) {
                return $s;
            }
            if (is_string($s)) {
                return in_array(strtolower($s), ['1', 'true', 'success', 'successful'], true)
                    || $s === 'success';
            }
            if (is_numeric($s)) {
                return (int) $s === 1 || (int) $s === 200;
            }
        }

        if (array_key_exists('success', $data)) {
            return (bool) $data['success'];
        }

        if (array_key_exists('response_code', $data)) {
            return (string) $data['response_code'] === '000' || (string) $data['response_code'] === '00';
        }

        return true;
    }

    public static function extractMessage(Response $response): string
    {
        $data = $response->json();
        if (is_array($data)) {
            foreach (['message', 'msg', 'error', 'errors'] as $k) {
                if (!array_key_exists($k, $data)) {
                    continue;
                }
                $v = $data[$k];
                if (is_string($v) && $v !== '') {
                    return $v;
                }
                if (is_array($v)) {
                    return json_encode($v);
                }
            }
        }

        $body = trim($response->body());
        if ($body !== '') {
            return strlen($body) > 280 ? substr($body, 0, 280) . '…' : $body;
        }

        return 'Request failed (' . $response->status() . ')';
    }
}
