<?php

use App\Constants\Status;
use App\Lib\GoogleAuthenticator;
use App\Models\Country;
use App\Models\Extension;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Verification;
use App\Models\WalletCheck;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


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
    $APIKEY = env('KEY');
    $settings = Setting::find(1);
    $rate = $settings->rate;
    $extraCharge = $settings->margin;

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=getPricesVerification",
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

    $APIKEY = env('KEY');

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=getPricesVerification",
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
    $var = json_decode($var);


    $services = $var ?? null;

    if ($var == null) {
        $services = null;
    }

    return $services;

}

function create_order($service, $price, $cost, $service_name, $gcost, $area_code, $carrier)
{
    $APIKEY = env('KEY');

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
        $url = "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=getNumber&service=$service&max_price=$maxPrice&areas=$area_code&carriers=$carrier";
    } elseif ($hasArea) {
        $url = "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=getNumber&service=$service&max_price=$maxPrice&areas=$area_code";
    } elseif ($hasCarrier) {
        $url = "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=getNumber&service=$service&max_price=$maxPrice&carriers=$carrier";
    } else {
        $url = "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=getNumber&service=$service&max_price=$maxPrice";
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

        Verification::where('phone', $phone)->where('status', 2)->delete() ?? null;

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

    Log::info("Diasy Response ====>>>" . json_encode($result) . "Data ===> $cost");


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

        $APIKEY = env('KEY');
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=setStatus&id=$orderID&status=8",
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


    $APIKEY = env('KEY');
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=getStatus&id=$orderID",
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

    $key = env('WKEY');

    $countries = Cache::remember('smspool_countries', 3600, function () use ($key) {
        Log::info('Requesting countriees from SMS Pool API', ['key' => $key]);

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

        Log::error('API call failed', ['response' => $response->body()]);
        return null;
    });


    return $countries;


}


function get_world_services()
{


    $key = env('WKEY');

    $services = Cache::remember('smspool_services', 3600, function () use ($key) {

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

        Log::error('API call failed', ['response' => $response->body()]);
        return null;
    });


    return $services;


}



function create_world_order($country, $service)
{
    $user = Auth::user();
    $setting = Setting::find(1);

    if (!$setting) {
        return 98;
    }

    $shortName = Country::where('country_id', $country)->value('short_name');
    if (!$shortName) {
        return 97;
    }

    $gcost = pool_cost($service, $shortName);
    if ($gcost === null || (float) $gcost <= 0) {
        return 5;
    }

    $calculatedCost = ((float) $setting->rate * (float) $gcost) + (float) $setting->margin;

    $wallet_check = WalletCheck::where('user_id', $user->id)->first();
    if (!$wallet_check) {
        return 8;
    }

    if ($user->wallet < $calculatedCost) {
        return 99;
    }

    $key = env('WKEY');

    $response = Http::asForm()->post('https://api.smspool.net/purchase/sms', [
        'country' => $country,
        'service' => $service,
        'key' => $key,
    ]);


    if ($response->failed()) {
        return 2;
    }

    $data = $response->json();

    if (!isset($data['success'])) {
        return 2;
    }

    if ($data['success'] == 0) {
        return 5;
    }

    if ($data['success'] == 1) {

        Verification::where('phone', $data['cc'] . $data['phonenumber'])
            ->where('status', 2)
            ->delete();

        try {
            $out = DB::transaction(function () use ($user, $data, $calculatedCost) {
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
                $ver->service = $data['service'];
                $ver->expires_in = 300;
                $ver->cost = $calculatedCost;
                $ver->api_cost = $data['cost'] ?? 0;
                $ver->status = 1;
                $ver->type = 8;
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

function cancel_world_order($orderID)
{


    $ck_world = check_world_sms($orderID);
    if ($ck_world == 3) {
        return 5;
    }


    $key = env('WKEY');
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

function check_world_sms($orderID)
{

    $key = env('KEY');
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


function pool_cost($service, $country)
{

    $key = env('WKEY');

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
    $APIKEY = env('KEY');
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=getPrices&service=$service",
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
        'X-API-Key' => env('TRUVER_API_KEY'),
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
