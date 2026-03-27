<?php

use App\Constants\Status;
use App\Lib\GoogleAuthenticator;
use App\Models\Country;
use App\Models\Extension;
use App\Models\AppConfig;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Verification;
use App\Models\VerificationSms;
use App\Models\WalletCheck;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Delete any existing verification row(s) for this phone (any status) before assigning a new number.
 * Also removes related VerificationSms rows when present.
 */
function purge_existing_verifications_for_phone(string $phone): void
{
    $phone = trim($phone);
    if ($phone === '') {
        return;
    }

    $ids = Verification::where('phone', $phone)->pluck('id');
    if ($ids->isEmpty()) {
        return;
    }

    $idList = $ids->all();
    try {
        VerificationSms::whereIn('verification_id', $idList)->delete();
    } catch (\Throwable $e) {
        // ignore if table/column missing on older DBs
    }

    Verification::whereIn('id', $idList)->delete();
}


function sms_server_config_value(string $key, ?string $default = null): ?string
{
    $cacheKey = "app_config:$key";

    return Cache::remember($cacheKey, 600, function () use ($key, $default) {
        $row = AppConfig::query()->where('config_key', $key)->first();
        if (!$row || $row->config_value === null || trim((string) $row->config_value) === '') {
            return $default;
        }

        return trim((string) $row->config_value);
    });
}

function app_config_bool(string $key, bool $default = false): bool
{
    $fallback = $default ? '1' : '0';
    $raw = sms_server_config_value($key, $fallback);
    if ($raw === null) {
        return $default;
    }

    $val = strtolower(trim((string) $raw));
    return in_array($val, ['1', 'true', 'yes', 'on'], true);
}

function verification_server_flags(): array
{
    return [
        // USA Server 1 removed — keep flag false regardless of legacy DB config.
        'us1' => false,
        'us2' => app_config_bool('verification_server_us2_enabled', true),
        'world' => app_config_bool('verification_server_world_enabled', true), // SMS Pool world
        'world_hero' => app_config_bool('verification_server_world_hero_enabled', true),
    ];
}

function verification_server_api_key(string $server): string
{
    $server = strtolower($server);
    if ($server === 'us2') {
        return (string) (sms_server_config_value('verification_server_us2_api_key', env('TRUVER_API_KEY', '')) ?? '');
    }
    if ($server === 'world_hero') {
        return (string) (sms_server_config_value('verification_server_world_hero_api_key', env('SMS_SERVER_HERO_API_KEY', '')) ?? '');
    }
    if ($server === 'world') {
        return (string) (sms_server_config_value('verification_server_world_api_key', env('WKEY', '')) ?? '');
    }

    $us1 = sms_server_config_value('verification_server_us1_api_key', env('KEY', ''));
    if (is_string($us1) && trim($us1) !== '') {
        return trim($us1);
    }

    $legacy = sms_server_config_value('sms_server_api_key', null);
    if (is_string($legacy) && trim($legacy) !== '') {
        return trim($legacy);
    }

    return (string) env('KEY', '');
}

function verification_server_rate(string $server): float
{
    $server = strtolower($server);
    if ($server === 'world_hero') {
        $raw = sms_server_config_value('verification_server_world_hero_rate', (string) (Setting::find(2)->rate ?? 0));
        return (float) $raw;
    }
    if ($server === 'us2') {
        return (float) (Setting::find(3)->rate ?? 0);
    }
    if ($server === 'world') {
        return (float) (Setting::find(2)->rate ?? 0);
    }
    return (float) (Setting::find(1)->rate ?? 0);
}

function verification_server_margin(string $server): float
{
    $server = strtolower($server);
    if ($server === 'world_hero') {
        $raw = sms_server_config_value('verification_server_world_hero_margin', (string) (Setting::find(2)->margin ?? 0));
        return (float) $raw;
    }
    if ($server === 'us2') {
        return (float) (Setting::find(3)->margin ?? 0);
    }
    if ($server === 'world') {
        return (float) (Setting::find(2)->margin ?? 0);
    }
    return (float) (Setting::find(1)->margin ?? 0);
}

function world_sms_handler_url(array $query): string
{
    $base = rtrim((string) env('SMS_SERVER_HERO_BASE_URL', 'https://hero-sms.com'), '/');
    $query = array_merge(['api_key' => verification_server_api_key('world_hero')], $query);

    return $base . '/stubs/handler_api.php?' . http_build_query($query);
}

function sms_server_name(): string
{
    return strtolower((string) sms_server_config_value('sms_server_name', env('SMS_SERVER_NAME', 'herosms')));
}

function sms_server_base_url(): string
{
    // Legacy SMS handler base (USA Server 1 removed). Default aligns with Hero-style pools.
    $fallback = rtrim((string) env('SMS_SERVER_BASE_URL', env('SMS_SERVER_HERO_BASE_URL', 'https://hero-sms.com')), '/');
    $raw = sms_server_config_value('verification_server_us1_base_url', $fallback)
        ?? sms_server_config_value('sms_server_base_url', $fallback)
        ?? $fallback;

    return rtrim((string) $raw, '/');
}

function sms_server_api_key(): string
{
    $fallback = sms_server_name() === 'herosms'
        ? env('SMS_SERVER_HERO_API_KEY', env('KEY', ''))
        : env('KEY', '');

    $us1 = verification_server_api_key('us1');
    if ($us1 !== '') {
        return $us1;
    }

    return (string) (sms_server_config_value('sms_server_api_key', $fallback) ?? $fallback);
}

function sms_server_handler_url(array $query): string
{
    $base = sms_server_base_url() . '/stubs/handler_api.php';
    $query = array_merge(['api_key' => sms_server_api_key()], $query);

    return $base . '?' . http_build_query($query);
}

function resolve_complete($order_id)
{

    $curl = curl_init();

    $databody = array('order_id' => "$order_id");

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://web.sprintpay.online/api/resolve-complete',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $databody,
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);


    $status = $var->status ?? null;
    if ($status == true) {
        return 200;
    } else {
        return 500;
    }
}


function send_notification($message)
{

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.telegram.org/bot7592678907:AAH4Eu7NbRiClgyTbvr5tS6XhATpZwt0JrQ/sendMessage?chat_id=1316552414',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
            'chat_id' => "1316552414",
            'text' => $message,
        ),
        CURLOPT_HTTPHEADER => array(),
    ));

    $var = curl_exec($curl);
    curl_close($curl);

    $var = json_decode($var);
}

function send_notification2($message)
{

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.telegram.org/bot7158474563:AAGGy_0ZGUV_vZbagi3V1MvMuleMLpUseEc/sendMessage?chat_id=7109127373',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
            'chat_id' => "7109127373",
            'text' => $message,

        ),
        CURLOPT_HTTPHEADER => array(),
    ));

    $var = curl_exec($curl);
    curl_close($curl);

    $var = json_decode($var);
}

function session_resolve($session_id, $ref)
{

    $curl = curl_init();

    $databody = array(
        'session_id' => "$session_id",
        'ref' => "$ref"
    );


    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://web.sprintpay.online/api/resolve',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $databody,
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);

    $message = $var->message ?? null;
    $status = $var->status ?? null;

    $amount = $var->amount ?? null;

    return array([
        'status' => $status,
        'amount' => $amount,
        'message' => $message
    ]);


}


function get_services_api()
{
    $settings = Setting::find(1);
    $rate = $settings->rate;
    $extraCharge = $settings->margin;

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => sms_server_handler_url(['action' => 'getPricesVerification']),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json',
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    $data = json_decode($response, true);

    if (!$data || empty($data)) {
        return null;
    }

    $newData = [];

    foreach ($data as $serviceKey => $countries) {
        foreach ($countries as $countryId => $details) {
            $usdCost = (float)$details['cost'];
            $nairaCost = ($usdCost * $rate) + $extraCharge;

            $serviceDetails = $details;
            unset($serviceDetails['cost'], $serviceDetails['ttl']);

            $serviceDetails['service_key'] = $serviceKey; // add service key
            $serviceDetails['country_id'] = $countryId;   // keep country if needed
            $serviceDetails['cost_ngn'] = round($nairaCost, 2);

            $newData[] = $serviceDetails;
        }
    }

    return $newData;
}


function get_services()
{

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => sms_server_handler_url(['action' => 'getPricesVerification']),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json',
        ),
    ));

    $var = curl_exec($curl);

    curl_close($curl);
    $var = json_decode($var, true);


    $services = $var ?? null;

    if ($var == null) {
        $services = null;
    }

    return $services;

}

function create_order($service, $price, $cost, $service_name, $gcost, $area_code, $carrier)
{
    $hasArea = $area_code !== null && trim((string) $area_code) !== '';
    $hasCarrier = $carrier !== null && trim((string) $carrier) !== '';
    $finalCostPreview = ($hasArea || $hasCarrier) ? $price * 1.2 : $price;

    if (Auth::user()->wallet < $finalCostPreview) {
        return 8;
    }

    $maxPrice = $gcost;
    if ($maxPrice === null || (float) $maxPrice <= 0) {
        $maxPrice = $cost;
    }
    $maxPrice = (float) $maxPrice;

    if ($hasArea && $hasCarrier) {
        $url = sms_server_handler_url([
            'action' => 'getNumber',
            'service' => $service,
            'max_price' => $maxPrice,
            'areas' => $area_code,
            'carriers' => $carrier,
        ]);
    } elseif ($hasArea) {
        $url = sms_server_handler_url([
            'action' => 'getNumber',
            'service' => $service,
            'max_price' => $maxPrice,
            'areas' => $area_code,
        ]);
    } elseif ($hasCarrier) {
        $url = sms_server_handler_url([
            'action' => 'getNumber',
            'service' => $service,
            'max_price' => $maxPrice,
            'carriers' => $carrier,
        ]);
    } else {
        $url = sms_server_handler_url([
            'action' => 'getNumber',
            'service' => $service,
            'max_price' => $maxPrice,
        ]);
    }

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $result = $var ?? null;

    if (strstr($result, "NO_NUMBERS") !== false) {

        return 56;

    }

    if (strstr($result, "MAX_PRICE_EXCEEDED") !== false) {

        return 54;

    }


    if (strstr($result, "ACCESS_NUMBER") !== false) {

        $parts = explode(":", $result);
        if (count($parts) < 3) {
            return 0;
        }

        $id = $parts[1];
        $phone = $parts[2];

        $finalCost = ($hasArea || $hasCarrier) ? $price + ($price * 0.20) : $price;

        purge_existing_verifications_for_phone($phone);

        try {
            return DB::transaction(function () use ($service_name, $gcost, $id, $phone, $finalCost) {
                $user = User::where('id', Auth::id())->lockForUpdate()->first();
                if (!$user || (float) $user->wallet < $finalCost) {
                    return 8;
                }

                $oldBalance = (float) $user->wallet;
                $newBalance = $oldBalance - $finalCost;

                $ver = new Verification();
                $ver->user_id = Auth::id();
                $ver->phone = $phone;
                $ver->order_id = $id;
                $ver->country = "US";
                $ver->service = $service_name;
                $ver->cost = $finalCost;
                $ver->api_cost = $gcost;
                $ver->status = 1;
                $ver->expires_in = 300;
                $ver->type = 1;
                $ver->save();

                User::where('id', Auth::id())->decrement('wallet', $finalCost);
                WalletCheck::where('user_id', Auth::id())->increment('total_bought', $finalCost);
                WalletCheck::where('user_id', Auth::id())->decrement('wallet_amount', $finalCost);

                $trx = new Transaction();
                $trx->ref_id = "Verification-$id";
                $trx->user_id = Auth::id();
                $trx->status = 2;
                $trx->amount = $finalCost;
                $trx->balance = $newBalance;
                $trx->old_balance = $oldBalance;
                $trx->type = 1;
                $trx->save();

                return 1;
            });
        } catch (\Throwable $e) {
            Log::error('create_order debit failed', ['e' => $e->getMessage()]);

            return 0;
        }

    }

    Log::info('create_order provider response', ['result' => $result, 'cost' => $cost]);


    if ($result == "MAX_PRICE_EXCEEDED" || $result == "NO_NUMBERS" || $result == "TOO_MANY_ACTIVE_RENTALS" || $result == "NO_MONEY") {
        return 0;
    }

}


function cancel_order($orderID)
{


    $check_order = check_sms($orderID);

    if ($check_order == 3) {
        return 5;

    } else {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => sms_server_handler_url([
                'action' => 'setStatus',
                'id' => $orderID,
                'status' => 8,
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $result = $var ?? null;

        if (strstr($result, "ACCESS_CANCEL") !== false) {
            return 1;
        } else {
            return 0;
        }


    }


}

function check_true_sms($num)
{


}


function check_sms($orderID)
{


    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => sms_server_handler_url([
            'action' => 'getStatus',
            'id' => $orderID,
        ]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $result = $var ?? null;

    if (strstr($result, "NO_ACTIVATION") !== false) {

        return 1;

    }

    if (strstr($result, "NO_ACTIVATION") !== false) {

        return 1;

    }

    if (strstr($result, "STATUS_WAIT_CODE") !== false) {

        return 2;

    }

    if (strstr($result, "STATUS_CANCEL") !== false) {

        return 4;

    }


    if (strstr($result, "STATUS_OK") !== false) {


        $status = Verification::where('order_id', $orderID)->first() ?? null;
        if ($status) {
            $parts = explode(":", $result);
            $text = $parts[0];
            $sms = $parts[1];

            $data['sms'] = $sms;
            $data['full_sms'] = $sms;

            Verification::where('order_id', $orderID)->update([
                'status' => 2,
                'sms' => $sms,
                'full_sms' => $sms,
            ]);

            try {

                $order = Verification::where('order_id', $orderID)->first() ?? null;
                $user_id = Verification::where('order_id', $orderID)->first()->user_id ?? null;
                //User::where('id', $user_id)->decrement('hold_wallet', $order->cost);


            } catch (\Exception $e) {
                $message = $e->getMessage();
                send_notification($message);
                send_notification2($message);
            }

            $message = "$orderID | completed";
            send_notification($message);


            return 3;

        }


    }


}


function get_world_countries()
{
    $provider = 'smspool';
    $key = verification_server_api_key('world');

    $cacheKey = $provider === 'herosms' ? 'world_countries_herosms' : 'smspool_countries';
    $countries = Cache::remember($cacheKey, 3600, function () use ($provider, $key) {
        if ($provider === 'herosms') {
            $response = Http::timeout(50)->get(world_sms_handler_url(['action' => 'getCountries']));
            if (!$response->successful()) {
                Log::error('HeroSMS countries API call failed', ['response' => $response->body()]);
                return null;
            }
            $json = $response->json();
            if (!is_array($json)) {
                return null;
            }
            $mapped = [];
            foreach ($json as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $id = $row['id'] ?? null;
                $name = $row['eng'] ?? $row['name'] ?? $row['rus'] ?? null;
                if ($id === null || $name === null) {
                    continue;
                }
                $mapped[] = ['ID' => $id, 'name' => $name];
            }
            return $mapped;
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->timeout(50)
            ->post("https://api.smspool.net/country/retrieve_all", [
                'key' => $key,
            ]);

        if ($response->successful()) {
            return $response->json() ?? null;
        }

        Log::error('SMS Pool countries API call failed', ['response' => $response->body()]);
        return null;
    });

    return $countries;
}


function get_world_services()
{
    $provider = 'smspool';
    $key = verification_server_api_key('world');

    $cacheKey = $provider === 'herosms' ? 'world_services_herosms' : 'smspool_services';
    $services = Cache::remember($cacheKey, 3600, function () use ($provider, $key) {
        if ($provider === 'herosms') {
            $response = Http::timeout(50)->get(world_sms_handler_url(['action' => 'getServicesList', 'lang' => 'en']));
            if (!$response->successful()) {
                Log::error('HeroSMS services API call failed', ['response' => $response->body()]);
                return null;
            }
            $json = $response->json();
            $arr = is_array($json) ? ($json['services'] ?? []) : [];
            if (!is_array($arr)) {
                return null;
            }
            $mapped = [];
            foreach ($arr as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $id = $row['code'] ?? null;
                $name = $row['name'] ?? null;
                if ($id === null || $name === null) {
                    continue;
                }
                $mapped[] = ['ID' => $id, 'name' => $name];
            }
            return $mapped;
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->timeout(50)
            ->post("https://api.smspool.net/service/retrieve_all", [
                'key' => $key,
            ]);

        if ($response->successful()) {
            return $response->json() ?? null;
        }

        Log::error('SMS Pool services API call failed', ['response' => $response->body()]);
        return null;
    });

    return $services;
}



function create_world_order($country, $service, string $provider = 'smspool', ?float $heroApiCost = null, ?string $serviceLabel = null)
{
    $user = Auth::user();
    $provider = strtolower(trim($provider));
    if (!in_array($provider, ['smspool', 'herosms'], true)) {
        $provider = 'smspool';
    }
    $setting = Setting::find(2);

    if (!$setting) {
        return 98;
    }

    $countryToken = (string) $country;
    if ($provider !== 'herosms') {
        $shortName = Country::where('country_id', $country)->value('short_name');
        if (!$shortName) {
            return 97;
        }
        $countryToken = (string) $shortName;
    }

    if ($provider === 'herosms' && $heroApiCost !== null && $heroApiCost > 0) {
        if (!hero_sms_api_cost_is_allowed((float) $heroApiCost, (string) $service, $countryToken)) {
            Log::warning('create_world_order HeroSMS api cost not in current price list', [
                'country' => $country,
                'country_token' => $countryToken,
                'service' => $service,
                'hero_api_cost' => $heroApiCost,
            ]);

            return 5;
        }
        $gcost = (float) $heroApiCost;
    } else {
        $gcost = pool_cost($service, $countryToken, $provider);
    }
    if ($gcost === null || (float) $gcost <= 0) {
        Log::warning('create_world_order unavailable cost', [
            'provider' => $provider,
            'country' => $country,
            'country_token' => $countryToken,
            'service' => $service,
            'gcost' => $gcost,
        ]);
        return 5;
    }

    $rate = $provider === 'herosms' ? verification_server_rate('world_hero') : (float) $setting->rate;
    $margin = $provider === 'herosms' ? verification_server_margin('world_hero') : (float) $setting->margin;
    $calculatedCost = ($rate * (float) $gcost) + $margin;

    $wallet_check = WalletCheck::where('user_id', $user->id)->first();
    if (!$wallet_check) {
        return 8;
    }

    if ($user->wallet < $calculatedCost) {
        return 99;
    }

    $key = $provider === 'herosms'
        ? verification_server_api_key('world_hero')
        : verification_server_api_key('world');

    if ($provider === 'herosms') {
        Log::info('HeroSMS purchase request', [
            'user_id' => $user->id,
            'country' => $country,
            'service' => $service,
            'rate' => $rate,
            'margin' => $margin,
            'api_cost' => $gcost,
            'calculated_cost' => $calculatedCost,
        ]);
        $heroQuery = [
            'action' => 'getNumber',
            'service' => $service,
            'country' => $country,
            'maxPrice' => (string) $gcost,
        ];
        $response = Http::timeout(50)->get(world_sms_handler_url($heroQuery));
    } else {
        $response = Http::asForm()->post('https://api.smspool.net/purchase/sms', [
            'country' => $country,
            'service' => $service,
            'key' => $key,
        ]);
    }


    if ($response->failed()) {
        Log::error('create_world_order request failed', [
            'provider' => $provider,
            'status' => $response->status(),
            'body' => $response->body(),
            'country' => $country,
            'service' => $service,
            'user_id' => $user->id,
        ]);
        return 2;
    }

    if ($provider === 'herosms') {
        $raw = trim((string) $response->body());
        Log::info('HeroSMS purchase response', [
            'user_id' => $user->id,
            'country' => $country,
            'service' => $service,
            'raw' => $raw,
        ]);
        if (str_starts_with($raw, 'ACCESS_NUMBER:')) {
            $parts = explode(':', $raw);
            if (count($parts) >= 3) {
                $data = [
                    'success' => 1,
                    'order_id' => $parts[1],
                    'cc' => '',
                    'phonenumber' => $parts[2],
                    'country' => $country,
                    'service' => $service,
                    'cost' => (float) $gcost,
                ];
            } else {
                $data = ['success' => 0];
            }
        } else {
            $data = ['success' => 0];
        }
    } else {
        $data = $response->json();
    }

    if (!isset($data['success'])) {
        return 2;
    }

    if ($data['success'] == 0) {
        Log::warning('create_world_order provider returned unavailable', [
            'provider' => $provider,
            'country' => $country,
            'service' => $service,
            'payload' => $data,
        ]);
        return 5;
    }

    if ($data['success'] == 1) {

        purge_existing_verifications_for_phone($data['cc'] . $data['phonenumber']);

        $displayService = ($serviceLabel !== null && trim($serviceLabel) !== '')
            ? trim($serviceLabel)
            : (string) $service;

        try {
            $out = DB::transaction(function () use ($user, $data, $calculatedCost, $displayService, $provider) {
                $locked = User::where('id', $user->id)->lockForUpdate()->first();
                if (!$locked || (float) $locked->wallet < $calculatedCost) {
                    return 99;
                }

                $wc = WalletCheck::where('user_id', $user->id)->lockForUpdate()->first();
                if (!$wc) {
                    return 8;
                }

                $oldBalance = (float) $locked->wallet;
                $newBalance = $oldBalance - $calculatedCost;

                $ver = new Verification();
                $ver->user_id = $user->id;
                $ver->phone = $data['cc'] . $data['phonenumber'];
                $ver->order_id = $data['order_id'];
                $ver->country = $data['country'];
                $ver->service = $displayService;
                $ver->expires_in = 300;
                $ver->cost = $calculatedCost;
                $ver->api_cost = $data['cost'] ?? 0;
                $ver->status = 1;
                $ver->type = $provider === 'herosms' ? 9 : 8;
                $ver->save();

                User::where('id', $user->id)->decrement('wallet', $calculatedCost);
                $wc->increment('total_bought', $calculatedCost);
                $wc->decrement('wallet_amount', $calculatedCost);

                $trx = new Transaction();
                $trx->ref_id = "Verification " . $data['order_id'];
                $trx->user_id = $user->id;
                $trx->status = 2;
                $trx->amount = $calculatedCost;
                $trx->balance = $newBalance;
                $trx->old_balance = $oldBalance;
                $trx->type = 1;
                $trx->save();

                return 3;
            });

            if ($out === 3) {
                $locked = User::where('id', $user->id)->first();
                $cost2 = number_format($calculatedCost, 2);
                $bal = number_format((float) $locked->wallet, 2);
                $message = "{$locked->email} just ordered a number on SMSPOOL — NGN {$cost2} | Balance: NGN {$bal}";
                if ($provider === 'herosms') {
                    $message = "{$locked->email} just ordered a number on HEROSMS — NGN {$cost2} | Balance: NGN {$bal}";
                }
                send_notification($message);
                send_notification2($message);
            }

            return $out;
        } catch (\Throwable $e) {
            Log::error('create_world_order debit failed', ['e' => $e->getMessage()]);

            return 2;
        }

    }

    return 2;
}

function cancel_world_order($orderID, string $provider = 'smspool')
{
    $provider = strtolower(trim($provider));
    if (!in_array($provider, ['smspool', 'herosms'], true)) {
        $provider = 'smspool';
    }


    $ck_world = check_world_sms($orderID, $provider);
    if ($ck_world == 3) {
        return 5;
    }


    $key = $provider === 'herosms'
        ? verification_server_api_key('world_hero')
        : verification_server_api_key('world');
    if ($provider === 'herosms') {
        $resp = Http::timeout(30)->get(world_sms_handler_url([
            'action' => 'setStatus',
            'id' => $orderID,
            'status' => 8,
        ]));
        $raw = trim((string) $resp->body());
        if ($raw === '') {
            Log::warning('HeroSMS cancel empty response', ['order_id' => $orderID]);

            return 0;
        }
        if (str_contains($raw, 'ACCESS_CANCEL')) {
            return 1;
        }
        Log::warning('HeroSMS cancel not confirmed', ['order_id' => $orderID, 'raw' => $raw]);

        return 0;
    }

    $curl = curl_init();
    $databody = array(
        'orderid' => $orderID,
        'key' => $key,
    );
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.smspool.net/sms/cancel',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $databody,
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);

    $status = $var->success ?? null;
    $message = $var->message ?? null;

    if ($status == 0 && $message == "We could not find this order!") {
        return 3;
    }

    if ($status == 0 && $message == "Your order cannot be cancelled yet, please try again later.") {
        return 0;
    }


    if ($status == 1) {
        return 1;
    }


}

function check_world_sms($orderID, string $provider = 'smspool')
{
    $provider = strtolower(trim($provider));
    if (!in_array($provider, ['smspool', 'herosms'], true)) {
        $provider = 'smspool';
    }
    $key = $provider === 'herosms'
        ? verification_server_api_key('world_hero')
        : verification_server_api_key('world');
    if ($provider === 'herosms') {
        $resp = Http::timeout(30)->get(world_sms_handler_url([
            'action' => 'getStatus',
            'id' => $orderID,
        ]));
        $raw = trim((string) $resp->body());
        if (str_contains($raw, 'STATUS_WAIT_CODE')) {
            return 1;
        }
        if (str_contains($raw, 'STATUS_CANCEL')) {
            return 6;
        }
        if (str_contains($raw, 'STATUS_OK:')) {
            $parts = explode(':', $raw, 2);
            $sms = $parts[1] ?? null;
            Verification::where('order_id', $orderID)->update([
                'status' => 2,
                'sms' => $sms,
                'full_sms' => $sms,
            ]);
            return 3;
        }
        return 1;
    }

    $curl = curl_init();

    $databody = array(
        'orderid' => $orderID,
        'key' => $key,
    );
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.smspool.net/sms/check',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $databody,
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);

    $status = $var->status ?? null;
    $sms = $var->sms ?? null;
    $full_sms = $var->full_sms ?? null;


    if ($status == 1) {

        Verification::where('order_id', $orderID)->update([
            'expires_in' => $var->time_left / 10 - 20,
        ]);

        return 1;
    }

    if ($status == 6) {
        return 6;
    }


    if ($status == 3) {

        $data['sms'] = $sms;
        $data['full_sms'] = $full_sms;

        Verification::where('order_id', $orderID)->update([
            'status' => 2,
            'sms' => $sms,
            'full_sms' => $full_sms,
        ]);

        return 3;
    }


}


/**
 * Collect every HeroSMS price tier from getPrices (nested arrays with a numeric "cost").
 *
 * @return array<int, array{cost: float, label: string, meta: array}>
 */
function hero_sms_get_price_options(string $service, string $country): array
{
    $resp = Http::timeout(30)->get(world_sms_handler_url([
        'action' => 'getPrices',
        'service' => $service,
        'country' => $country,
    ]));
    if ($resp->failed()) {
        Log::warning('HeroSMS getPrices HTTP failed', [
            'status' => $resp->status(),
            'snippet' => substr((string) $resp->body(), 0, 400),
            'service' => $service,
            'country' => $country,
        ]);

        return [];
    }
    $json = $resp->json();
    if (!is_array($json)) {
        return [];
    }
    $rawRows = [];
    hero_sms_collect_price_entries($json, $rawRows);

    $seen = [];
    $out = [];
    foreach ($rawRows as $row) {
        if (!is_array($row) || !isset($row['cost']) || !is_numeric($row['cost'])) {
            continue;
        }
        $cost = (float) $row['cost'];
        if ($cost <= 0) {
            continue;
        }
        $label = hero_sms_format_price_label($row);
        $key = sprintf('%.8f|%s', $cost, $label);
        if (isset($seen[$key])) {
            continue;
        }
        $seen[$key] = true;
        $out[] = [
            'cost' => $cost,
            'label' => $label,
            'meta' => $row,
        ];
    }
    usort($out, static fn (array $a, array $b): int => $a['cost'] <=> $b['cost']);

    return $out;
}

function hero_sms_collect_price_entries(mixed $node, array &$bucket): void
{
    if (!is_array($node)) {
        return;
    }
    if (array_key_exists('cost', $node) && is_numeric($node['cost'])) {
        $bucket[] = $node;

        return;
    }
    foreach ($node as $child) {
        if (is_array($child)) {
            hero_sms_collect_price_entries($child, $bucket);
        }
    }
}

function hero_sms_format_price_label(array $row): string
{
    $parts = [];
    foreach (['name', 'operator', 'provider', 'count', 'ttl', 'repeatable', 'ltrPrice'] as $k) {
        if (!array_key_exists($k, $row) || $row[$k] === '' || $row[$k] === null) {
            continue;
        }
        $v = $row[$k];
        if (is_bool($v)) {
            $v = $v ? 'yes' : 'no';
        }
        $parts[] = $k . ': ' . $v;
    }

    return $parts !== [] ? implode(' · ', $parts) : 'Standard';
}

function hero_sms_api_cost_is_allowed(float $cost, string $service, string $countryToken): bool
{
    foreach (hero_sms_get_price_options($service, $countryToken) as $opt) {
        if (abs($opt['cost'] - $cost) < 0.000001) {
            return true;
        }
    }

    return false;
}

function pool_cost($service, $country, string $provider = 'smspool')
{
    $provider = strtolower(trim($provider));
    if (!in_array($provider, ['smspool', 'herosms'], true)) {
        $provider = 'smspool';
    }
    if ($provider === 'herosms') {
        $opts = hero_sms_get_price_options((string) $service, (string) $country);

        return $opts[0]['cost'] ?? null;
    }

    $key = verification_server_api_key('world');
    $databody = array(
        "key" => $key,
        "country" => $country,
        "service" => $service,
        "pool" => '',
    );

    $body = json_encode($databody);

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.smspool.net/request/price',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $databody,
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer $key"
        ),
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);


    $get_s_price = $var->price ?? null;
    $high_price = $var->high_price ?? null;
    $rate = $var->success_rate ?? null;

    if ($get_s_price < 4) {
        $price = $get_s_price * 1.3;
    } else {
        $price = $get_s_price;
    }


    return $price;


}

function get_d_price($service)
{
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => sms_server_handler_url([
            'action' => 'getPrices',
            'service' => $service,
        ]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    $data = json_decode($response, true);

    if (!is_array($data) || empty($data)) {
        return null;
    }

    foreach ($data as $serviceCode => $countries) {
        if (is_array($countries)) {
            foreach ($countries as $countryCode => $details) {
                if (isset($details['cost'])) {
                    return $details['cost'];
                }
            }
        }
    }

    return null;
}


function send_verification_email($email)
{


    try {

        $expiryTimestamp = time() + 24 * 60 * 60; // 24 hours in seconds
        $url = url('') . "/verify-account-now?code=$expiryTimestamp&email=$email";
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


        return 1;

    } catch (Exception $e) {

        return 0;
    }


}


function get_services_usa_server_2(array $services = [], $zip = null)
{
    $url = "https://app.truverifi.com/api/checkService";

    $payload = [];

    if (!empty($services)) {
        $payload['services'] = [];
    }

    if (!is_null($zip)) {
        $payload['zip'] = $zip;
    }

    $response = Http::withHeaders([
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'X-API-Key' => verification_server_api_key('us2'),
    ])->post($url, $payload);


    if ($response->failed()) {
        return [
            'success' => false,
            'error' => $response->json('error') ?? $response->body(),
        ];
    }

    $data = $response->json();


    return [
        'success' => true,
        'available' => $data['available'] ?? false,
        'availableServices' => $data['availableServices'] ?? [],
        'availableZips' => $data['availableZips'] ?? [],
        'creditsCost' => $data['creditsCost'] ?? null,
    ];
}
