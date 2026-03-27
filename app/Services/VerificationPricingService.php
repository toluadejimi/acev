<?php

namespace App\Services;

use App\Models\Setting;

class VerificationPricingService
{
    /**
     * Legacy USA Server 1 pricing (retired): server-side NGN from provider code and optional area/carrier surcharge.
     *
     * @return array{api_cost: float, base_ngn: float, final_ngn: float}|null
     */
    public static function usaServer1Quote(string $provider, $areaCode, $carrier): ?array
    {
        $provider = trim($provider);
        if ($provider === '') {
            return null;
        }

        $apiCost = get_d_price($provider);
        if ($apiCost === null || (float) $apiCost <= 0) {
            return null;
        }

        $apiCost = (float) $apiCost;
        $setting = Setting::find(1);
        if (!$setting) {
            return null;
        }

        $baseNgn = ((float) $setting->rate * $apiCost) + (float) $setting->margin;

        $hasArea = $areaCode !== null && trim((string) $areaCode) !== '';
        $hasCarrier = $carrier !== null && trim((string) $carrier) !== '';
        $finalNgn = ($hasArea || $hasCarrier) ? $baseNgn * 1.2 : $baseNgn;

        return [
            'api_cost' => $apiCost,
            'base_ngn' => $baseNgn,
            'final_ngn' => $finalNgn,
        ];
    }
}
