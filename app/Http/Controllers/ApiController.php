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
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    private function apiKeyLogSuffix(?string $apiKey): string
    {
        if ($apiKey === null || $apiKey === '') {
            return '(empty)';
        }
        $len = strlen($apiKey);

        return $len <= 4 ? '****' : '…' . substr($apiKey, -4);
    }

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
        if ($request->api_key == null) {
            return response()->json([
                'status' => false,
                'message' => "Api key is missing",
            ], 422);
        }

        if ($request->action == null) {
            return response()->json([
                'status' => false,
                'message' => "state can not be null",
            ], 422);
        }

        $user = User::where('api_key', $request->api_key)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => "Wrong or incorrect api key",
            ], 422);
        }

        if ($request->action === 'balance') {
            return response()->json([
                'status' => true,
                'main_balance' => (float) $user->wallet,
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => "Wrong or incorrect api key",
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

                    $providerOrderId = (string) ($var['order_id'] ?? '');
                    if ($providerOrderId === '') {
                        throw new \Exception('Invalid provider response: missing order_id');
                    }

                    $debitRef = 'APIVerification ' . $providerOrderId;

                    $ver = Verification::create([
                        'user_id'    => $user->id,
                        'phone'      => $rentPhone,
                        'order_id'   => $providerOrderId,
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
                        'ref_id'      => $debitRef,
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
            'api_key'  => 'required|string',
            'action'   => 'required|string',
            'order_id' => 'required|integer',
        ]);

        $internalOrderId = $request->integer('order_id');
        $logBase = [
            'endpoint' => 'cancel-world-sms',
            'internal_order_id' => $internalOrderId,
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 240),
            'api_key_suffix' => $this->apiKeyLogSuffix($request->input('api_key')),
        ];

        Log::info('cancel-world-sms: request', $logBase);

        if ($request->action !== "cancel-world-sms") {
            Log::warning('cancel-world-sms: invalid action', array_merge($logBase, ['action' => $request->action]));

            return response()->json([
                'status'  => false,
                'message' => "Invalid action",
            ], 422);
        }

        $caller = User::where('api_key', $request->api_key)->first();
        if (!$caller) {
            Log::warning('cancel-world-sms: invalid api_key', $logBase);

            return response()->json([
                'status'  => false,
                'message' => "Invalid API key",
            ], 401);
        }

        $logBase['user_id'] = (int) $caller->id;

        try {
            $result = DB::transaction(function () use ($request, $caller, $logBase) {
                $order = Verification::where('id', $request->order_id)->lockForUpdate()->first();
                if (!$order) {
                    Log::info('cancel-world-sms: order not found', $logBase);

                    return ['http' => 404, 'body' => ['status' => false, 'message' => 'Order not found']];
                }

                $logOrder = array_merge($logBase, [
                    'verification_id' => (int) $order->id,
                    'provider_order_id' => (string) ($order->order_id ?? ''),
                    'order_type' => (int) ($order->type ?? 0),
                    'order_status' => (int) ($order->status ?? 0),
                ]);

                // Only the account that created the order may cancel via API (prevents arbitrary refunds).
                if ((int) $order->user_id !== (int) $caller->id) {
                    Log::warning('cancel-world-sms: api_key does not own order', [
                        'verification_id' => (int) $order->id,
                        'order_user_id' => (int) $order->user_id,
                        'caller_id' => (int) $caller->id,
                    ]);

                    return ['http' => 403, 'body' => ['status' => false, 'message' => 'Order does not belong to this API key']];
                }

                $refundRefId = 'Order API Cancel ' . $order->id;

                // Idempotent: refund already recorded — never credit twice.
                if (Transaction::where('ref_id', $refundRefId)->exists()) {
                    Log::info('cancel-world-sms: idempotent — refund already exists', $logOrder);

                    return [
                        'http' => 200,
                        'body' => ['status' => true, 'message' => 'ORDER ALREADY CANCELLED'],
                    ];
                }

                if ((int) $order->status === 99) {
                    Log::info('cancel-world-sms: order already status 99', $logOrder);

                    return [
                        'http' => 200,
                        'body' => ['status' => true, 'message' => 'ORDER ALREADY CANCELLED'],
                    ];
                }

                // Only pending world/API orders can be cancelled/refunded
                if ((int) $order->status !== 1 || !in_array((int) $order->type, [2, 8, 9, 10], true)) {
                    Log::info('cancel-world-sms: cannot cancel at this stage', $logOrder);

                    return [
                        'http' => 400,
                        'body' => ['status' => false, 'message' => 'Order cannot be cancelled at this stage'],
                    ];
                }

                $orderID = $order->order_id;
                $can_order = cancel_world_order($orderID);

                if ($can_order == 0) {
                    Log::info('cancel-world-sms: provider cancel deferred (try later)', $logOrder);

                    return [
                        'http' => 422,
                        'body' => ['status' => false, 'message' => 'Please wait and try again later'],
                    ];
                }

                if ($can_order == 5) {
                    Log::info('cancel-world-sms: provider reports SMS already present', $logOrder);

                    return [
                        'http' => 422,
                        'body' => ['status' => false, 'message' => 'SMS Found already'],
                    ];
                }

                if ($can_order == 3) {
                    Log::info('cancel-world-sms: provider order not active', $logOrder);

                    return [
                        'http' => 422,
                        'body' => ['status' => false, 'message' => 'Order no longer active on provider side'],
                    ];
                }

                if ($can_order != 1) {
                    Log::warning('cancel-world-sms: provider cancel unexpected code', array_merge($logOrder, ['can_order' => $can_order]));

                    return [
                        'http' => 422,
                        'body' => ['status' => false, 'message' => 'Cancellation failed'],
                    ];
                }

                $user = User::where('id', $order->user_id)->lockForUpdate()->first();
                if (!$user) {
                    Log::error('cancel-world-sms: user missing during refund', $logOrder);

                    return ['http' => 500, 'body' => ['status' => false, 'message' => 'User not found']];
                }

                $old_balance = (float) $user->wallet;
                $refund = (float) $order->cost;
                $user->wallet = $old_balance + $refund;
                $user->save();

                WalletCheck::where('user_id', $user->id)->increment('wallet_amount', $refund);
                WalletCheck::where('user_id', $user->id)->decrement('total_bought', $refund);

                $new_balance = (float) $user->wallet;

                Transaction::create([
                    'ref_id'      => $refundRefId,
                    'user_id'     => $user->id,
                    'status'      => 2,
                    'amount'      => $refund,
                    'balance'     => $new_balance,
                    'old_balance' => $old_balance,
                    'type'        => 3,
                ]);

                $order->status = 99;
                $order->save();

                Log::info('cancel-world-sms: refunded and order marked cancelled', array_merge($logOrder, [
                    'refund_ngn' => $refund,
                    'wallet_before' => $old_balance,
                    'wallet_after' => $new_balance,
                    'transaction_ref_id' => $refundRefId,
                ]));

                return [
                    'http' => 200,
                    'body' => ['status' => true, 'message' => 'ORDER CANCELLED'],
                ];
            }, 3);
        } catch (\Throwable $e) {
            Log::error('cancel-world-sms: exception', array_merge($logBase, [
                'e' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]));

            return response()->json([
                'status'  => false,
                'message' => 'Could not cancel order',
            ], 500);
        }

        Log::info('cancel-world-sms: response', array_merge($logBase, [
            'http_status' => $result['http'],
            'response_message' => $result['body']['message'] ?? null,
            'response_ok' => ($result['body']['status'] ?? false) === true,
        ]));

        return response()->json($result['body'], $result['http']);
    }


    public function cancel_usa_number(Request $request)
    {
        $request->validate([
            'api_key'  => 'required|string',
            'action'   => 'nullable|string|in:cancel-usa-sms',
            'order_id' => 'nullable|integer',
            'phone'    => 'nullable|string',
        ]);

        if (!$request->filled('order_id') && !$request->filled('phone')) {
            return response()->json([
                'status'  => false,
                'message' => 'order_id or phone is required',
            ], 422);
        }

        $caller = User::where('api_key', $request->api_key)->first();
        if (!$caller) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid API key',
            ], 401);
        }

        try {
            $payload = DB::transaction(function () use ($request, $caller) {
                $q = Verification::query()->where('user_id', $caller->id);
                if ($request->filled('order_id')) {
                    $q->where('id', $request->integer('order_id'));
                } else {
                    $q->where('phone', $request->input('phone'));
                }

                $order = $q->lockForUpdate()->orderByDesc('id')->first();
                if (!$order) {
                    return ['http' => 404, 'body' => ['status' => false, 'message' => 'Order not found']];
                }

                $refundRef = 'API_ORDER_CANCEL_' . $order->id;
                if (Transaction::where('ref_id', $refundRef)->exists()) {
                    return [
                        'http' => 200,
                        'body' => ['status' => true, 'message' => 'ORDER ALREADY CANCELLED'],
                    ];
                }

                // USA Server 1 API orders only (Hero-style pool); not world / unlimited.
                if ((int) $order->type !== 1) {
                    return [
                        'http' => 400,
                        'body' => ['status' => false, 'message' => 'This order cannot be cancelled via USA API cancel'],
                    ];
                }

                if ((int) $order->status === 2) {
                    return [
                        'http' => 400,
                        'body' => ['status' => false, 'message' => 'Order already completed (SMS received)'],
                    ];
                }

                if ((int) $order->status !== 1) {
                    return [
                        'http' => 400,
                        'body' => ['status' => false, 'message' => 'Order cannot be cancelled at this stage'],
                    ];
                }

                $corder = cancel_order($order->order_id);

                if ($corder == 0) {
                    return [
                        'http' => 422,
                        'body' => ['status' => false, 'message' => 'Please wait and try again later'],
                    ];
                }

                if ($corder == 5) {
                    return [
                        'http' => 422,
                        'body' => ['status' => false, 'message' => 'SMS already received. Cannot cancel.'],
                    ];
                }

                if ($corder != 1) {
                    return [
                        'http' => 422,
                        'body' => ['status' => false, 'message' => 'Cancellation failed'],
                    ];
                }

                $user = User::where('id', $order->user_id)->lockForUpdate()->first();
                if (!$user) {
                    return ['http' => 500, 'body' => ['status' => false, 'message' => 'User not found']];
                }

                $old_balance = (float) $user->wallet;
                $refund = (float) $order->cost;
                $user->wallet = $old_balance + $refund;
                $user->save();

                WalletCheck::where('user_id', $user->id)->increment('wallet_amount', $refund);
                WalletCheck::where('user_id', $user->id)->decrement('total_bought', $refund);

                $new_balance = (float) $user->wallet;

                Transaction::create([
                    'ref_id'      => $refundRef,
                    'user_id'     => $user->id,
                    'status'      => 2,
                    'amount'      => $refund,
                    'balance'     => $new_balance,
                    'old_balance' => $old_balance,
                    'type'        => 3,
                ]);

                $order->delete();

                return [
                    'http' => 200,
                    'body' => ['status' => true, 'message' => 'ORDER CANCELLED & REFUNDED'],
                ];
            }, 3);
        } catch (\Throwable $e) {
            Log::error('cancel-usa-sms failed', ['e' => $e->getMessage()]);

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }

        return response()->json($payload['body'], $payload['http']);
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

            if ($request->order_id === null || $request->order_id === '') {
                return response()->json([
                    'status' => false,
                    'message' => 'order_id is required',
                ], 422);
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
            $user = User::where('api_key', $request->api_key)->first();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid API Key',
                ], 401);
            }

            if ($request->order_id === null || $request->order_id === '') {
                return response()->json([
                    'status' => false,
                    'message' => 'order_id is required',
                ], 422);
            }

            $ver = Verification::where('id', $request->order_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$ver) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            if ($ver->status == 1) {
                $sms_status = 'PENDING';
            } elseif ($ver->status == 2) {
                $sms_status = 'COMPLETED';
            } else {
                $sms_status = 'REJECTED';
            }

            return response()->json([
                'status'     => true,
                'sms_status' => $sms_status,
                'full_sms'   => $ver->full_sms,
                'code'       => $ver->sms,
                'country'    => 'USA',
                'service'    => $ver->service,
                'phone'      => $ver->phone,
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid action',
        ], 400);
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
