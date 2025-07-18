<?php

use App\Constants\Status;
use App\Lib\GoogleAuthenticator;
use App\Models\Extension;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Verification;
use App\Models\WalletCheck;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

function create_order($service, $price, $cost, $service_name, $costs)
{

    if (Auth::user()->wallet < $price) {
        return 8;
    }

    if (Auth::user()->wallet < $price) {
        return 8;
    }


    if (Auth::user()->wallet < $price) {
        return 8;
    }


    $currentTime = Carbon::now();
    $futureTime = $currentTime->addMinutes(20);
    $formattedTime = $futureTime->format('Y-m-d H:i:s');


    $APIKEY = env('KEY');
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=getNumber&service=$service&max_price=$cost",
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

    if (strstr($result, "ACCESS_NUMBER") !== false) {


        if (Auth::user()->wallet < $price) {
            return 8;
        }

        $parts = explode(":", $result);
        $accessNumber = $parts[0];
        $id = $parts[1];
        $phone = $parts[2];


        Verification::where('phone', $phone)->where('status', 2)->delete() ?? null;

        $ver = new Verification();
        $ver->user_id = Auth::id();
        $ver->phone = $phone;
        $ver->order_id = $id;
        $ver->country = "US";
        $ver->service = $service_name;
        $ver->cost = $price;
        $ver->api_cost = $cost;
        $ver->status = 1;
        $ver->expires_in = 300;
        $ver->type = 1;
        $ver->save();


        $get_balance = User::where('id', Auth::id())->first()->wallet;

        if ($get_balance < $costs) {
            return response([
                'status' => false,
                'message' => 'Insufficient balance'
            ], 400);
        }

        $get_balance = User::where('id', Auth::id())->first()->wallet;
        $balance = $get_balance - $costs;

        User::where('id', Auth::id())->decrement('wallet', $costs);
        WalletCheck::where('user_id', Auth::id())->increment('total_bought', $costs);
        WalletCheck::where('user_id', Auth::id())->decrement('wallet_amount', $costs);


        $trx = new Transaction();
        $trx->ref_id = "Verification-$id";
        $trx->user_id = Auth::id();
        $trx->status = 2;
        $trx->amount = $costs;
        $trx->balance = $balance;
        $trx->old_balance = $get_balance;
        $trx->type = 1;
        $trx->save();



        $cost2 = number_format($price, 2);
        $cal = Auth::user()->wallet - $costs;
        $bal = number_format($cal, 2);
        $message = Auth::user()->email . " just been ordered number on Diasy NGN $cost2 | NGN $bal ";
        send_notification($message);
        send_notification2($message);

        return 1;

    }

    if ($result == "MAX_PRICE_EXCEEDED" || $result == "NO_NUMBERS" || $result == "TOO_MANY_ACTIVE_RENTALS" || $result == "NO_MONEY") {
        return 0;
    }

}


function cancel_order($orderID)
{


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


        $status = Verification::where('order_id', $orderID)->first()->status ?? null;
        if ($status != 2) {
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

        Log::info('Response received from SMS Pool API', ['response' => $response->body()]);

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
        Log::info('Requesting services from SMS Pool API', ['key' => $key]);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->timeout(50)
            ->post("https://api.smspool.net/service/retrieve_all", [
                'key' => $key,
            ]);

        Log::info('Response received from SMS Pool API', ['response' => $response->body()]);

        if ($response->successful()) {
            return $response->json() ?? null;
        }

        Log::error('API call failed', ['response' => $response->body()]);
        return null;
    });


    return $services;


}


function create_world_order($country, $service, $price, $calculatrdcost)
{


    $wallet_check = WalletCheck::where('user_id', Auth::id())->first();

    if(!$wallet_check){
        return 8;
    }



    $key = env('WKEY');
    $curl = curl_init();

    $databody = [
        'country' => $country,
        'service' => $service,
        'key' => $key,
    ];

    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.smspool.net/purchase/sms',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $databody,
    ]);

    $response = curl_exec($curl);
    curl_close($curl);
    $json_start = strpos($response, '{');
    if ($json_start !== false) {
        $json_string = substr($response, $json_start);
        $var = json_decode($json_string, true);
        $success = $var['success'] ?? null;


        if ($success == 0) {
            return 5;

        }

        if ($success == 1) {

                Verification::where('phone', $var['cc'] . $var['phonenumber'])->where('status', 2)->delete() ?? null;
            $currentTime = Carbon::now();
            $futureTime = $currentTime->addMinutes(15);
            $formattedTime = $futureTime->format('Y-m-d H:i:s');

            $ver = new Verification();
            $ver->user_id = Auth::id();
            $ver->phone = $var['cc'] . $var['phonenumber'];
            $ver->order_id = $var['order_id'];
            $ver->country = $var['country'];
            $ver->service = $var['service'];
            $ver->expires_in = $var['expires_in'] / 10 - 20;
            $ver->cost = $calculatrdcost;
            $ver->created_at = $formattedTime;
            $ver->expires_in = 300;
            $ver->api_cost = $var['cost'];
            $ver->status = 1;
            $ver->type = 2;

            $ver->save();


            $get_balance = User::where('id', Auth::id())->first()->wallet;

            if ($get_balance < $calculatrdcost) {
                return response([
                    'status' => false,
                    'message' => 'Insufficient balance'
                ], 400);
            }

            $balance = $get_balance - $calculatrdcost;

            User::where('id', Auth::id())->decrement('wallet', $calculatrdcost);

            WalletCheck::where('user_id', Auth::id())->increment('total_bought', $calculatrdcost);
            WalletCheck::where('user_id', Auth::id())->decrement('wallet_amount', $calculatrdcost);

            $trx = new Transaction();
            $trx->ref_id = "Verification " . $var['order_id'];
            $trx->user_id = Auth::id();
            $trx->status = 2;
            $trx->amount = $calculatrdcost;
            $trx->balance = $balance;
            $trx->old_balance = $get_balance;
            $trx->type = 1;
            $trx->save();




            $cost2 = number_format($calculatrdcost, 2);
            $cal = Auth::user()->wallet - $calculatrdcost;
            $bal = number_format($cal, 2);
            $message = Auth::user()->email . " just been ordered number on  SMSPOOL NGN $cost2 | NGN $bal ";
            send_notification($message);
            send_notification2($message);

            return 3;


        }
    }


    $status = $var->type ?? null;

    if ($status == "BALANCE_ERROR") {
        return 1;
    }

    if ($status == null) {
        return 2;

    }


}

function cancel_world_order($orderID)
{

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


    dd($var);
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
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=getPrices&service=$service",
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


    foreach ($var as $key => $value) {

        $service2['data'] = $value;

    }

    $result = $service2["data"]->$service->cost;
    return $result;

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
