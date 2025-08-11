<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Verification;
use App\Models\WalletCheck;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{

    public function get_balance(request $request)
    {

        $bal = User::where('api_key', $request->api_key)->first()->wallet ?? null;

        if ($request->api_key == null) {

            return response()->json([
                'status' => false,
                'message' => "Api key is missing"
            ], 422);

        }

        if ($request->action == null) {

            return response()->json([
                'status' => false,
                'message' => "state can not be null"
            ], 422);

        }


        if ($bal != null && $request->action == "balance") {

            return response()->json([
                'status' => true,
                'main_balance' => $bal
            ], 200);

        }

        return response()->json([
            'status' => false,
            'message' => "Wrong or incorrect api key"
        ], 422);


    }

    public function get_world_countries(request $request)
    {
        return response()->json([
            'status' => true,
            'data' => get_world_countries()
        ], 200);


    }

    public function get_world_services(request $request)
    {
        return response()->json([
            'status' => true,
            'data' => get_world_services()
        ], 200);


    }


    public function check_availability(request $request)
    {


        if ($request->api_key == null) {

            return response()->json([
                'status' => false,
                'message' => "Api key is missing"
            ], 422);

        }

        if ($request->action == null) {

            return response()->json([
                'status' => false,
                'message' => "state can not be null"
            ], 422);

        }


        if ($request->action == "check-availability") {

            $get_key = User::where('api_key', $request->api_key)->first() ?? null;

            if ($get_key == null) {

                return response()->json([
                    'status' => false,
                    'message' => "Wrong Api key"
                ], 422);

            }


            $key = env('WKEY');
            $curl = curl_init();

            $databody = array(
                "country" => $request->country,
                "service" => $request->service,
                'key' => $key,


            );


            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.smspool.net/sms/stock',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $databody,
            ));

            $var1 = curl_exec($curl);

            curl_close($curl);
            $var1 = json_decode($var1);

            $data['stock'] = $var1->amount ?? null;


            $databody = array(
                "key" => $key,
                "country" => $request->country,
                "service" => $request->service,
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
                    'Authorization: Bearer {{apikey}}'
                ),
            ));

            $var3 = curl_exec($curl);
            curl_close($curl);
            $var2 = json_decode($var3);

            $get_s_price = $var2->price ?? null;
            $high_price = $var2->high_price ?? null;
            $rate = $var2->success_rate ?? null;


            if ($high_price == null) {
                $price = $get_s_price * $get_key->api_percentage;
            } elseif ($high_price > 4) {
                $price = $high_price * $get_key->api_percentage ?? 1.3;
            } else {
                $price = $high_price * $get_key->api_percentage;
            }

            $get_rate = Setting::where('id', 1)->first()->rate;
            $margin = Setting::where('id', 1)->first()->margin;
            $ngnprice = ($price * $get_rate) + $margin;


            return response()->json([
                'status' => true,
                'cost' => $ngnprice,
                'stock' => $data['stock'],
                'country' => $request->country,
                'service' => $request->service,

            ], 200);


        } else {
            return response()->json([
                'status' => false,
                'message' => "action incorrect"
            ], 422);
        }
    }


    public function rent_world_number(request $request)
    {

        if ($request->api_key == null) {

            return response()->json([
                'status' => false,
                'message' => "Api key is missing"
            ], 422);

        }

        if ($request->action == null) {

            return response()->json([
                'status' => false,
                'message' => "action can not be null"
            ], 422);

        }


        if ($request->action == "rent-world-number") {


            $user = User::where('api_key', $request->api_key)->first() ?? null;


            $wallet_check = WalletCheck::where('user_id', $user->id)->first();
            if (!$wallet_check) {

                $ck = WalletCheck::where('user_id', $user->id)->first();
                if (!$ck) {
                    $wal = new WalletCheck();
                    $wal->user_id = $user->id;
                    $wal->total_funded = $user->wallet;
                    $wal->wallet_amount = $user->wallet;
                    $wal->save();
                }

            }


            $key = env('WKEY');
            $databody = array(
                "key" => $key,
                "country" => $request->country,
                "service" => $request->service,
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
                    'Authorization: Bearer {{apikey}}'
                ),
            ));

            $var2 = curl_exec($curl);
            curl_close($curl);
            $var2 = json_decode($var2);

            $get_s_price = $var->price ?? null;
            $high_price = $var->high_price ?? null;
            $rate = $var->success_rate ?? null;


            if ($high_price == null) {
                $price = $get_s_price;
            } elseif ($high_price > 4) {
                $price = $high_price ?? 1.3;
            } else {
                $price = $high_price;
            }

            $get_rate = Setting::where('id', 1)->first()->rate;
            $margin = Setting::where('id', 1)->first()->margin;
            $ngnprice = ($price * $get_rate) + $margin;

            if ($user->wallet < $ngnprice) {

                return response()->json([
                    'status' => false,
                    'message' => "INSUFFICIENT FUNDS, FUND YOUR WALLET",
                ], 422);

            }


            $key = env('WKEY');
            $curl = curl_init();

            $databody = [
                "country" => $request->country,
                "service" => $request->service,
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

                    return response()->json([

                        'status' => false,
                        'message' => "Number Currently out of stock, Please check back later",

                    ]);

                }

                if ($success == 1) {

                        Verification::where('phone', $var['cc'] . $var['phonenumber'])->where('status', 2)->delete() ?? null;
                    $currentTime = Carbon::now();
                    $futureTime = $currentTime->addMinutes(15);
                    $formattedTime = $futureTime->format('Y-m-d H:i:s');

                    $ver = new Verification();
                    $ver->user_id = $user->id;
                    $ver->phone = $var['cc'] . $var['phonenumber'];
                    $ver->order_id = $var['order_id'];
                    $ver->country = $var['country'];
                    $ver->service = $var['service'];
                    $ver->expires_in = $var['expires_in'] / 10 - 20;
                    $ver->cost = $ngnprice;
                    $ver->created_at = $formattedTime;
                    $ver->expires_in = 300;
                    $ver->api_cost = $var['cost'];
                    $ver->status = 1;
                    $ver->type = 2;
                    $ver->save();


                    $get_balance = User::where('id', $user->id)->first()->wallet;
                    if ($get_balance < $ngnprice) {
                        return response([
                            'status' => false,
                            'message' => 'Insufficient balance'
                        ], 400);
                    }

                    $balance = $get_balance - $ngnprice;

                    User::where('id', Auth::id())->decrement('wallet', $ngnprice);

                    WalletCheck::where('user_id', Auth::id())->increment('total_bought', $ngnprice);
                    WalletCheck::where('user_id', Auth::id())->decrement('wallet_amount', $ngnprice);

                    $trx = new Transaction();
                    $trx->ref_id = "APIVerification " . $var['order_id'];
                    $trx->user_id = $user->id;
                    $trx->status = 2;
                    $trx->amount = $ngnprice;
                    $trx->balance = $balance;
                    $trx->old_balance = $get_balance;
                    $trx->type = 1;
                    $trx->save();


                    $cost2 = number_format($ngnprice, 2);
                    $cal = $user->wallet - $ngnprice;
                    $bal = number_format($cal, 2);


                    return response()->json([
                        'status' => true,
                        'order_id' => $ver->id,
                        'phone_no' => $var['cc'] . $var['phonenumber'],
                        'country' => $var['country'],
                        'service' => $var['service'],
                        'expires' => $var['expires_in']

                    ], 200);

                }

            }

            return response()->json([
                'status' => false,
                'message' => $var->errors->message,
            ], 422);

        }


    }

    public function rent_usa_number(request $request)
    {

        if ($request->api_key == null) {

            return response()->json([
                'status' => false,
                'message' => "Api key is missing"
            ], 422);

        }

        if ($request->action == null) {

            return response()->json([
                'status' => false,
                'message' => "action can not be null"
            ], 422);

        }


        if ($request->action == "rent-usa-number") {


            $user = User::where('api_key', $request->api_key)->first() ?? null;

            $wallet_check = WalletCheck::where('user_id', $user->id)->first();
            if (!$wallet_check) {

                $ck = WalletCheck::where('user_id', $user->id)->first();
                if (!$ck) {
                    $wal = new WalletCheck();
                    $wal->user_id = $user->id;
                    $wal->total_funded = $user->wallet;
                    $wal->wallet_amount = $user->wallet;
                    $wal->save();
                }

            }


            $service = $request->service;

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

            $response = curl_exec($curl);
            curl_close($curl);
            $data = json_decode($response, true);
            $countryData = reset($data);
            $cost = null;
            foreach ($countryData as $key => $details) {
                if (strcasecmp($details['name'], $service) === 0) {
                    $cost = $details['cost'];
                    break;
                }
            }

            dd($data);


            $settings = Setting::find(1);
            $rate = $settings->rate;
            $margin = $settings->margin;

            if ($cost !== null) {
                $nairaCost = ($cost * $rate) + $margin;
            }


            if ($user->wallet < $nairaCost) {
                return response()->json([
                    'status' => false,
                    'message' => "INSUFFICIENT FUNDS, FUND YOUR WALLET",
                ], 422);

            }


            $APIKEY = env('KEY');
            $curl = curl_init();

            dd($service);

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


            dd($var);

            if (strstr($result, "ACCESS_NUMBER") !== false) {


                if ($user->wallet < $nairaCost) {
                    return response()->json([
                        'status' => false,
                        'message' => "INSUFFICIENT FUNDS, FUND YOUR WALLET",
                    ], 422);

                }

                $parts = explode(":", $result);
                $accessNumber = $parts[0];
                $id = $parts[1];
                $phone = $parts[2];

                Verification::where('phone', $phone)->where('status', 2)->delete() ?? null;

                $ver = new Verification();
                $ver->user_id = $user->id;
                $ver->phone = $phone;
                $ver->order_id = $id;
                $ver->country = "US";
                $ver->service = $service;
                $ver->cost = $nairaCost;
                $ver->api_cost = $cost;
                $ver->status = 1;
                $ver->expires_in = 300;
                $ver->type = 1;
                $ver->save();


                $get_balance = User::where('id', $user->id)->first()->wallet;
                $balance = $get_balance - $nairaCost;

                User::where('id', Auth::id())->decrement('wallet', $nairaCost);

                WalletCheck::where('user_id', Auth::id())->increment('total_bought', $nairaCost);
                WalletCheck::where('user_id', Auth::id())->decrement('wallet_amount', $nairaCost);

                $trx = new Transaction();
                $trx->ref_id = "APIVerification " . $var['order_id'];
                $trx->user_id = $user->id;
                $trx->status = 2;
                $trx->amount = $nairaCost;
                $trx->balance = $balance;
                $trx->old_balance = $get_balance;
                $trx->type = 1;
                $trx->save();


                return response()->json([
                    'status' => true,
                    'order_id' => $ver->id,
                    'phone_no' => $phone,
                    'country' => "USA",
                    'service' => $service,
                    'expires' => $ver->expires_in,

                ], 200);

            }


            return response()->json([

                'status' => false,
                'message' => "Number Currently out of stock, Please check back later",

            ]);


        }


    }


    public function get_world_sms(request $request)
    {

        if ($request->api_key == null) {

            return response()->json([
                'status' => false,
                'message' => "Api key is missing"
            ], 422);

        }

        if ($request->action == null) {

            return response()->json([
                'status' => false,
                'message' => "action can not be null"
            ], 422);

        }


        if ($request->action == "get-world-sms") {

            $full_sms = Verification::where('id', $request->order_id)->first()->full_sms;
            $code = Verification::where('id', $request->order_id)->first()->sms;
            $country = Verification::where('id', $request->order_id)->first()->country;
            $service = Verification::where('id', $request->order_id)->first()->service;
            $status = Verification::where('id', $request->order_id)->first()->status;
            $phone = Verification::where('id', $request->order_id)->first()->phone;

            if ($status == 1) {
                $sms_status = "PENDING";
            } elseif ($status == 2) {
                $sms_status = "COMPLETED";
            } else {
                $sms_status = "REJECTED";
            }


            return response()->json([
                'status' => true,
                'sms_status' => $sms_status,
                'full_sms' => $full_sms,
                'code' => $code,
                'country' => $country,
                'service' => $service,
                'phone' => $phone,
            ], 200);
        }


    }


    public function get_usa_services(request $request)
    {


        if ($request->api_key == null) {

            return response()->json([
                'status' => false,
                'message' => "Api key is missing"
            ], 422);

        }

        if ($request->action == null) {

            return response()->json([
                'status' => false,
                'message' => "action can not be null"
            ], 422);

        }


        if ($request->action == "get-usa-services") {

            return response()->json([
                'status' => true,
                'data' => get_services_api()
            ], 200);

        }


    }


}
