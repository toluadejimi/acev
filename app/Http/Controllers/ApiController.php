<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Verification;
use App\Models\VerificationSms;
use App\Models\WalletCheck;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    private function computeApiWorldNgnPrice(User $user, string $country, string $service): ?float
    {
        $key = env('WKEY');
        $response = Http::asForm()->post('https://api.smspool.net/request/price', [
            'key' => $key,
            'country' => $country,
            'service' => $service,
            'pool' => '',
        ]);

        if (!$response->ok()) {
            return null;
        }

        $priceData = $response->json();
        $get_s_price = $priceData['price'] ?? null;
        $high_price = $priceData['high_price'] ?? null;
        $pct = (float) ($user->api_percentage ?? 1);

        if ($high_price === null) {
            $usd = ($get_s_price ?? 0) * $pct;
        } elseif ($high_price > 4) {
            $usd = $high_price * $pct;
        } else {
            $usd = $high_price * $pct;
        }

        $settings = Setting::find(1);
        if (!$settings) {
            return null;
        }

        return ($usd * (float) $settings->rate) + (float) $settings->margin;
    }

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
            $ngnprice = $this->computeApiWorldNgnPrice($user, (string) $request->country, (string) $request->service);
            if ($ngnprice === null) {
                return response()->json(['status' => false, 'message' => 'Could not fetch pricing'], 500);
            }

            try {
                $result = DB::transaction(function () use ($user, $ngnprice, $request, $key) {
                    $locked = User::where('id', $user->id)->lockForUpdate()->first();
                    if (!$locked || (float) $locked->wallet < $ngnprice) {
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

                    $old_balance = (float) $locked->wallet;
                    User::where('id', $user->id)->decrement('wallet', $ngnprice);
                    $balance = $old_balance - $ngnprice;

                    $rentPhone = ($var['cc'] ?? '') . ($var['phonenumber'] ?? ($var['number'] ?? ''));
                    $existingIds = Verification::where('phone', $rentPhone)->pluck('id');
                    if ($existingIds->isNotEmpty()) {
                        VerificationSms::whereIn('verification_id', $existingIds)->delete();
                        Verification::whereIn('id', $existingIds)->delete();
                    }

                    $ver = Verification::create([
                        'user_id'    => $user->id,
                        'phone'      => $rentPhone,
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

    public function cancel_world_number(Request $request)
    {
        $request->validate([
            'action'   => 'required|string',
            'order_id' => 'required|integer',
        ]);

        if ($request->action !== "cancel-world-sms") {
            return response()->json([
                'status'  => false,
                'message' => "Invalid action",
            ], 422);
        }

        $order = Verification::where('id', $request->order_id)->first();
        if (!$order) {
            return response()->json([
                'status'  => false,
                'message' => "Order not found",
            ], 404);
        }

        // Only pending world/API orders can be cancelled/refunded
        if ((int) $order->status !== 1 || !in_array((int) $order->type, [2, 8, 9, 10], true)) {
            return response()->json([
                'status'  => false,
                'message' => "Order cannot be cancelled at this stage",
            ]);
        }

        $orderID = $order->order_id;
        $can_order = cancel_world_order($orderID);

        if ($can_order == 0) {
            return response()->json([
                'status'  => false,
                'message' => "Please wait and try again later",
            ]);
        }

        if ($can_order == 5) {
            return response()->json([
                'status'  => false,
                'message' => "SMS Found already",
            ]);
        }

        if ($can_order == 3) {
            // Provider says order not found / already removed – do not refund to avoid abuse.
            return response()->json([
                'status'  => false,
                'message' => "Order no longer active on provider side",
            ]);
        }

        if ($can_order != 1) {
            return response()->json([
                'status'  => false,
                'message' => "Cancellation failed",
            ]);
        }

        DB::beginTransaction();

        try {
            $user = User::lockForUpdate()->find($order->user_id);
            if (!$user) {
                DB::rollBack();
                return response()->json([
                    'status'  => false,
                    'message' => "User not found",
                ]);
            }

            $old_balance = (float) $user->wallet;
            $refund = (float) $order->cost;
            $user->wallet = $old_balance + $refund;
            $user->save();

            WalletCheck::where('user_id', $user->id)
                ->increment('wallet_amount', $refund);

            $new_balance = (float) $user->wallet;

            $trx = new Transaction();
            $trx->ref_id = "Order API Cancel " . $order->id;
            $trx->user_id = $user->id;
            $trx->status = 2;
            $trx->amount = $refund;
            $trx->balance = $new_balance;
            $trx->old_balance = $old_balance;
            $trx->type = 3;
            $trx->save();

            // Mark as cancelled instead of deleting, so it can't be cancelled twice.
            $order->status = 99;
            $order->save();

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => "ORDER CANCELLED",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => "Could not cancel order",
            ], 500);
        }
    }


    public function cancel_usa_number(Request $request)
    {
        // Validate input
        $request->validate([
            'order_id' => 'nullable|integer',
            'phone'    => 'nullable|string'
        ]);

        // Try to find order
        $order = null;

        if ($request->order_id) {
            $order = Verification::where('id', $request->order_id)->first();
        }

        if (!$order && $request->phone) {
            $order = Verification::where('phone', $request->phone)->first();
        }

        if (!$order) {
            return response()->json([
                'status'  => false,
                'message' => "Order not found"
            ]);
        }

        // If already cancelled
        if ($order->status == 2) {
            return response()->json([
                'status'  => false,
                'message' => "Order already cancelled"
            ]);
        }

        // If order is active
        if ($order->status != 1) {
            return response()->json([
                'status'  => false,
                'message' => "Order cannot be cancelled at this stage"
            ]);
        }

        // Call external cancel API
        $corder = cancel_order($order->order_id);

        if ($corder == 0) {
            return response()->json([
                'status'  => false,
                'message' => "Please wait and try again later"
            ]);
        }

        if ($corder == 5) {
            return response()->json([
                'status'  => false,
                'message' => "SMS already received. Cannot cancel."
            ]);
        }

        if ($corder != 1) {
            return response()->json([
                'status'  => false,
                'message' => "Cancellation failed"
            ]);
        }

        // Process refund safely
        DB::beginTransaction();

        try {

            $user = User::lockForUpdate()->find($order->user_id);

            if (!$user) {
                DB::rollBack();
                return response()->json([
                    'status'  => false,
                    'message' => "User not found"
                ]);
            }

            $old_balance = $user->wallet;

            // Refund wallet
            $user->wallet += $order->cost;
            $user->save();

            // Update wallet check
            WalletCheck::where('user_id', $user->id)
                ->increment('wallet_amount', $order->cost);

            $new_balance = $user->wallet;

            // Log transaction
            $trx = new Transaction();
            $trx->ref_id = "API_ORDER_CANCEL_" . $order->id;
            $trx->user_id = $user->id;
            $trx->status = 2; // success
            $trx->amount = $order->cost;
            $trx->balance = $new_balance;
            $trx->old_balance = $old_balance;
            $trx->type = 3; // refund type
            $trx->save();

            $order->delete();

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => "ORDER CANCELLED & REFUNDED"
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => "Something went wrong. Please try again."
            ]);
        }
    }


    public function rent_usa_number(Request $request)
    {
        if (!$request->api_key) {
            return response()->json(['status' => false, 'message' => "Api key is missing"], 422);
        }

        if (!$request->action) {
            return response()->json(['status' => false, 'message' => "Action cannot be null"], 422);
        }

        if ($request->action !== "rent-usa-number") {
            return response()->json(['status' => false, 'message' => "Invalid action"], 422);
        }

        $user = User::where('api_key', $request->api_key)->first();
        if (!$user) {
            return response()->json(['status' => false, 'message' => "Invalid API key"], 422);
        }

        return response()->json([
            'status' => false,
            'message' => 'USA Server 1 is no longer available. Use USA Server 2 or World rent endpoints.',
        ], 410);
    }



    public function get_world_sms(Request $request)
    {
        if (!$request->api_key) {
            return response()->json([
                'status' => false,
                'message' => "Api key is missing"
            ], 422);
        }

        if (!$request->action) {
            return response()->json([
                'status' => false,
                'message' => "Action can not be null"
            ], 422);
        }

        if ($request->action == "get-world-sms") {

            $user = User::where('api_key', $request->api_key)->first();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => "Invalid API Key"
                ], 401);
            }

            $ver = Verification::where('id', $request->order_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$ver) {
                return response()->json([
                    'status' => false,
                    'message' => "Order not found"
                ], 404);
            }

            switch ($ver->status) {
                case 1:
                    $sms_status = "PENDING";
                    break;
                case 2:
                    $sms_status = "COMPLETED";
                    break;
                case 99:
                    $sms_status = "CANCELLED";
                    break;
                default:
                    $sms_status = "REJECTED";
            }

            return response()->json([
                'status' => true,
                'sms_status' => $sms_status,
                'full_sms' => $ver->full_sms ?? null,
                'code' => $ver->sms ?? null,
                'country' => $ver->country,
                'service' => $ver->service,
                'phone' => $ver->phone,
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => "Invalid action"
        ], 400);
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



    public function get_usa_services(Request $request)
    {
        if (!$request->api_key) {
            return response()->json([
                'status' => false,
                'message' => "Api key is missing"
            ], 422);
        }

        if (!$request->action) {
            return response()->json([
                'status' => false,
                'message' => "Action can not be null"
            ], 422);
        }

        $user = User::where('api_key', $request->api_key)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => "Invalid API Key"
            ], 401);
        }

        if ($request->action == "get-usa-services") {
            return response()->json([
                'status' => false,
                'message' => 'USA Server 1 service list is no longer available. Use World or USA Server 2 APIs.',
            ], 410);
        }

        return response()->json([
            'status' => false,
            'message' => "Invalid action"
        ], 400);
    }



}
