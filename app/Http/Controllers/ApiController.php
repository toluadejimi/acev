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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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
            $user = User::where('api_key', $request->api_key)->first();
            if (!$user) {
                return response()->json(['status' => false, 'message' => "Invalid API Key"], 401);
            }

            $wallet_check = WalletCheck::firstOrCreate(
                ['user_id' => $user->id],
                ['total_funded' => $user->wallet, 'wallet_amount' => $user->wallet]
            );

            $key = env('WKEY');
            $databody = ["key" => $key, "country" => $request->country, "service" => $request->service];
            $response = Http::asForm()->post('https://api.smspool.net/request/price', $databody);

            if (!$response->ok()) {
                return response()->json(['status' => false, 'message' => 'API Error'], 500);
            }

            $priceData = $response->json();
            $get_s_price = $priceData['price'] ?? null;
            $high_price  = $priceData['high_price'] ?? null;
            $rate        = $priceData['success_rate'] ?? null;

            $price = $high_price && $high_price > 4 ? $high_price : ($get_s_price ?? 1.3);
            $settings = Setting::find(1);
            $ngnprice = ($price * $settings->rate) + $settings->margin;

            try {
                $result = DB::transaction(function () use ($user, $ngnprice, $request, $key) {
                    $user->refresh();
                    if ($user->wallet < $ngnprice) {
                        throw new \Exception("INSUFFICIENT FUNDS, FUND YOUR WALLET");
                    }

                    $purchase = Http::asForm()->post('https://api.smspool.net/purchase/sms', [
                        'country' => $request->country,
                        'service' => $request->service,
                        'key'     => $key,
                    ]);

                    if (!$purchase->ok()) {
                        throw new \Exception("API Purchase Failed");
                    }

                    $var = $purchase->json();



                    \Log::info('SMSPOOL purchase response:', $var);

                    if (($var['success'] ?? 0) != 1) {
                        throw new \Exception("Number Currently out of stock, Please check back later");
                    }

                    $old_balance = $user->wallet;
                    $user->decrement('wallet', $ngnprice);
                    $balance = $old_balance - $ngnprice;

                    $ver = Verification::create([
                        'user_id'    => $user->id,
                        'phone'      => ($var['cc'] ?? '') . ($var['phonenumber'] ?? ($var['number'] ?? '')),
                        'order_id'   => $var['order_id'] ?? null,
                        'country'    => $var['country'] ?? $request->country,
                        'service'    => $var['service'] ?? $request->service,
                        'expires_in' => $var['expires_in'] ?? 300,
                        'cost'       => $ngnprice,
                        'api_cost'   => $var['cost'] ?? 0,
                        'status'     => 1,
                        'type'       => 2,
                    ]);

                    WalletCheck::where('user_id', $user->id)->increment('total_bought', $ngnprice);
                    WalletCheck::where('user_id', $user->id)->decrement('wallet_amount', $ngnprice);

                    Transaction::create([
                        'ref_id'      => "APIVerification " . $var['order_id'],
                        'user_id'     => $user->id,
                        'status'      => 2,
                        'amount'      => $ngnprice,
                        'balance'     => $balance,
                        'old_balance' => $old_balance,
                        'type'        => 1,
                    ]);

                    return [
                        'status'   => true,
                        'order_id' => $ver->id,
                        'phone_no' => $ver->phone,
                        'country'  => $ver->country,
                        'service'  => $ver->service,
                        'expires'  => $ver->expires_in,
                    ];
                });

                return response()->json($result, 200);
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
            }
        }



    }

    public function cancel_world_number(request $request)
    {


        if ($request->action === "cancel-usa-sms")


            $order = Verification::where('id', $request->order_id)->first() ?? null;
        $orderID = $order->order_id;
        $can_order = cancel_world_order($orderID);


        if ($can_order == 0) {

            return response()->json([
                'status' => false,
                'message' => "Please wait and try again later"
            ]);

        }


        if ($can_order == 1) {

            sleep(5);

            $amount = number_format($order->cost, 2);
            Verification::where('id', $request->id)->delete();

            User::where('id', $order->user_id)->increment('wallet', $order->cost);
            WalletCheck::where('user_id', $order->user_id)->increment('wallet_amount', $order->cost);

            $get_balance = User::where('id', $order->user_id)->first()->wallet;
            $balance = $get_balance + $order->cost;


            $trx = new Transaction();
            $trx->ref_id = "Order Cancel " . $request->id;
            $trx->user_id = $order->user_id;
            $trx->status = 2;
            $trx->amount = $order->cost;
            $trx->balance = $balance;
            $trx->old_balance = $get_balance;
            $trx->type = 3;
            $trx->save();


            return response()->json([
                'status' => true,
                'message' => "ORDER CANCELLED"
            ]);


        }


        if ($can_order == 3) {
            $amount = number_format($order->cost, 2);
            Verification::where('id', $request->id)->delete();
            return back()->with('message', "Order has been canceled");
        }
    }


    public
    function cancel_usa_number(request $request)
    {

        $order = Verification::where('id', $request->order_id)->first() ?? null;

        if ($order == null) {

            return response()->json([
                'status' => false,
                'message' => "Order not found"
            ]);
        }

        if ($order->status == 2) {
            Verification::where('id', $request->order_id)->delete();

            return response()->json([
                'status' => false,
                'message' => "Order has been successfully deleted"
            ]);

        }

        if ($order->status == 1) {

            $orderID = $order->order_id;
            $corder = cancel_order($orderID);


            if ($corder == 0) {

                return response()->json([
                    'status' => false,
                    'message' => "Please wait and try again later"
                ]);

            }


            if ($corder == 1) {

                sleep(5);
                $amount = number_format($order->cost, 2);

                $user_id = $order->user_id;
                User::where('id', $user_id)->increment('wallet', $order->cost);
                WalletCheck::where('user_id', $user_id)->increment('wallet_amount', $order->cost);


                $get_balance = User::where('id', $user_id)->first()->wallet;
                $balance = $get_balance + $order->cost;

                $trx = new Transaction();
                $trx->ref_id = "API Order Cancel " . $request->id;
                $trx->user_id = $user_id;
                $trx->status = 2;
                $trx->amount = $order->cost;
                $trx->balance = $balance;
                $trx->old_balance = $get_balance;
                $trx->type = 3;
                $trx->save();


                return response()->json([
                    'status' => true,
                    'message' => "ORDER CANCELLED"
                ]);

            }


        }

    }

    public
    function rent_usa_number(request $request)
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
            $service_key = $request->service_key;

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


            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://daisysms.com/stubs/handler_api.php?api_key=$APIKEY&action=getNumber&service=$service_key&max_price=$cost",
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
                $trx->ref_id = "APIVerification " . date('mhis');
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


    public
    function get_world_sms(request $request)
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

            $ver = Verification::where('id', $request->order_id)->first() ?? null;
            if ($ver) {

                if ($ver->status == 1) {
                    $sms_status = "PENDING";
                } elseif ($ver->status == 2) {
                    $sms_status = "COMPLETED";
                } else {
                    $sms_status = "REJECTED";
                }


                return response()->json([
                    'status' => true,
                    'sms_status' => $sms_status,
                    'full_sms' => $ver->full_sms,
                    'code' => $ver->sms,
                    'country' => $ver->country,
                    'service' => $ver->service,
                    'phone' => $ver->phone,
                ], 200);

            }
        }


    }

    public
    function get_usa_sms(request $request)
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


        if ($request->action == "get-usa-sms") {


            $ver = Verification::where('id', $request->order_id)->first() ?? null;
            if ($ver) {

                if ($ver->status == 1) {
                    $sms_status = "PENDING";
                } elseif ($ver->status == 2) {
                    $sms_status = "COMPLETED";
                } else {
                    $sms_status = "REJECTED";
                }


                return response()->json([
                    'status' => true,
                    'sms_status' => $sms_status,
                    'full_sms' => $ver->full_sms,
                    'code' => $ver->sms,
                    'country' => "USA",
                    'service' => $ver->service,
                    'phone' => $ver->phone,
                ], 200);

            }

        }


    }


    public
    function get_usa_services(request $request)
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
