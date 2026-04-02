<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Verification;
use App\Models\WalletCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UnlimitedPortalController extends Controller
{
    private $baseUrl = 'https://unlimitedportal.com/api_command.php';
    private $apiKey;
    private $user;

    public function __construct()
    {
        $this->apiKey = env('UNLIMITED_API_KEY');
        $this->user = env('UNLIMITED_USER');
    }


    private function sendRequest($command, $params = [])
    {
        $payload = array_merge(['api_key' => $this->apiKey, 'user' => $this->user, 'cmd' => $command], $params);

        $response = Http::asForm()->post($this->baseUrl, $payload);




        if ($response->failed()) {

            Log::error("UNLIMITED ERROR =====>".json_encode($response->json()));


            return response()->json(['error' => 'API request failed', 'details' => $response->body()], 500);
        }



        return $response->json();
    }

    /**
     * @return array{api_price: float, base_ngn: float}|null
     */
    private function resolveUsa2QuoteByServiceName(string $serviceName): ?array
    {
        $res = $this->sendRequest('list_services');
        if (!is_array($res) || empty($res['message']) || !is_array($res['message'])) {
            return null;
        }

        foreach ($res['message'] as $row) {
            $row = (array) $row;
            if (($row['name'] ?? '') === $serviceName) {
                $apiPrice = (float) ($row['price'] ?? 0);
                if ($apiPrice <= 0) {
                    return null;
                }
                $s = Setting::find(3);
                if (!$s) {
                    return null;
                }
                $baseNgn = ((float) $s->rate * $apiPrice) + (float) $s->margin;

                return ['api_price' => $apiPrice, 'base_ngn' => $baseNgn];
            }
        }

        return null;
    }

    /**
     * Assistant helper: resolve service name from a loose user query.
     *
     * @return array{name:string, api_price:float, base_ngn:float}|null
     */
    private function resolveUsa2QuoteByServiceQuery(string $query): ?array
    {
        $res = $this->sendRequest('list_services');
        if (!is_array($res) || empty($res['message']) || !is_array($res['message'])) {
            return null;
        }

        $setting = Setting::find(3);
        if (!$setting) {
            return null;
        }

        $norm = static function (string $v): string {
            return preg_replace('/[^a-z0-9]+/', '', strtolower($v)) ?? '';
        };

        $qNorm = $norm($query);
        if ($qNorm === '') {
            return null;
        }

        $best = null;
        foreach ($res['message'] as $row) {
            $row = (array) $row;
            $name = (string) ($row['name'] ?? '');
            $apiPrice = (float) ($row['price'] ?? 0);
            if ($name === '' || $apiPrice <= 0) {
                continue;
            }
            $n = $norm($name);
            $score = 0;
            if ($n === $qNorm) {
                $score = 100;
            } elseif (str_contains($n, $qNorm) || str_contains($qNorm, $n)) {
                $score = 70;
            } elseif (str_contains($n, substr($qNorm, 0, min(strlen($qNorm), 4)))) {
                $score = 40;
            }
            if ($score > 0 && ($best === null || $score > $best['score'])) {
                $best = [
                    'score' => $score,
                    'name' => $name,
                    'api_price' => $apiPrice,
                ];
            }
        }

        if ($best === null) {
            return null;
        }

        $baseNgn = ((float) $setting->rate * $best['api_price']) + (float) $setting->margin;
        return [
            'name' => $best['name'],
            'api_price' => (float) $best['api_price'],
            'base_ngn' => (float) $baseNgn,
        ];
    }

    /**
     * Assistant action: order a USA number by free-text service query.
     *
     * @return array{ok:bool,message:string}
     */
    public function assistantOrderUsaByQuery(string $query): array
    {
        $quote = $this->resolveUsa2QuoteByServiceQuery($query);
        if ($quote === null) {
            return ['ok' => false, 'message' => 'I could not find that USA service. Try: whatsapp, telegram, gmail, signal.'];
        }

        if ((float) (Auth::user()->wallet ?? 0) < (float) $quote['base_ngn']) {
            return ['ok' => false, 'message' => 'Insufficient wallet balance for this order.'];
        }

        $order = $this->create_order_usa2(
            (string) $quote['name'],
            (float) $quote['base_ngn'],
            (float) $quote['api_price'],
            (string) $quote['name'],
            (float) $quote['api_price'],
            null
        );

        if ($order == 1) {
            return ['ok' => true, 'message' => "Ordered successfully: {$quote['name']}. Refreshing your requests now."];
        }
        if ($order == 8) {
            return ['ok' => false, 'message' => 'Insufficient funds.'];
        }
        if ($order == 54) {
            return ['ok' => false, 'message' => 'Price changed. Please retry.'];
        }
        if ($order == 56) {
            return ['ok' => false, 'message' => 'No number available for that service right now.'];
        }

        return ['ok' => false, 'message' => 'Order failed. Please try again.'];
    }


    public function server_2_index(Request $request)
    {

        $response = $this->sendRequest('list_services');

        $services = $response['message'] ?? [];
        $allServices = [];
        foreach ($services as $service) {
            $allServices[] = (object)$service;
        }


        $data['allServices'] = $allServices;

        $data['get_rate'] = Setting::where('id', 3)->first()->rate;
        $data['margin'] = Setting::where('id', 3)->first()->margin;
        $data['verification'] = Verification::latest()->where('user_id', Auth::id())->take(10)->get();
        $data['order'] = 0;
        $verification = Verification::where('user_id', Auth::id())->get();
        $data['pend'] = 0;
        $data['product'] = null;
        $data['orders'] = Verification::where('user_id', Auth::id())->get();


        return view('usa2', $data);

    }


    public function order_now(Request $request)
    {


        $wallet_check = WalletCheck::where('user_id', Auth::id())->first();
        if (!$wallet_check) {
            Auth::logout();
            return redirect('login');
        }


        if (Auth::user()->wallet < 0) {

            $data['status'] = false;
            $data['message'] = "Insufficient Funds";

            return $data;

        }

        $service_name = (string) $request->service;
        $area_code = $request->areaCode;
        $hasArea = $area_code !== null && trim((string) $area_code) !== '';

        $quote = $this->resolveUsa2QuoteByServiceName($service_name);
        if ($quote === null) {
            $data['status'] = false;
            $data['message'] = 'Invalid service or pricing unavailable. Please refresh and try again.';

            return $data;
        }

        $finalNgn = $hasArea ? $quote['base_ngn'] * 1.2 : $quote['base_ngn'];
        $clientPrice = (float) $request->price;
        if (abs($clientPrice - $finalNgn) > 0.05) {
            $data['status'] = false;
            $data['message'] = 'Price has been updated, Please re-order number';

            return $data;
        }

        if (Auth::user()->wallet < $finalNgn) {
            $data['status'] = false;
            $data['message'] = "Insufficient Funds";

            return $data;
        }

        $service = $request->provider;

        $order = $this->create_order_usa2(
            $service,
            $quote['base_ngn'],
            $quote['api_price'],
            $service_name,
            $quote['api_price'],
            $area_code
        );


        if ($order == 8) {
            $data['status'] = false;
            $data['message'] = "Insufficient Funds";
            return $data;
        }

        if ($order == 54) {
            $data['status'] = false;
            $data['message'] = "Price has been updated, Please re-order number";
            return $data;
        }

        if ($order == 7) {
            Auth::logout();
            return redirect('login')->with('error', "Please Contact admin");
        }

        if ($order == 8) {

            $data['status'] = false;
            $data['message'] = "Insufficient Funds";

            return $data;
        }

        if ($order == 8) {
            $data['status'] = false;
            $data['message'] = "Insufficient Funds";

            return $data;
        }


        //dd($order);

        if ($order == 9) {

            $ver = Verification::where('status', 1)->first() ?? null;
            if ($ver != null) {
                return redirect('us');
            }
            return redirect('us');
        }

        if ($order == 0) {

            $data['status'] = false;
            $data['message'] = "Number Currently out of stock, Please check back later";
            return $data;
        }

        if ($order == 56) {

            $data['status'] = false;
            $data['message'] = "No number found";
            return $data;
        }


        if ($order == 1) {
            return response()->json([
                'status' => true,
                'reload' => true,
                'message' => "Successful"
            ]);
        }
    }


    public function check_sms_usa2(Request $request)
    {
        $request->validate([
            'num' => 'required|string',
        ]);

        $ver = Verification::query()
            ->where('phone', $request->input('num'))
            ->where('user_id', Auth::id())
            ->orderByDesc('id')
            ->first();

        if (! $ver) {
            return response()->json([
                'message' => 'waiting for sms',
                'status' => null,
            ]);
        }

        if ($ver->order_id !== null && $ver->order_id !== '') {
            $this->check_sms($ver->order_id);
            $ver->refresh();
        }

        $waiting = 'waiting for sms';
        // Prefer the parsed `sms` code, but fall back to `full_sms` if needed.
        $sms = $ver->sms ?: $ver->full_sms;

        if ($sms === null || $sms === '') {
            return response()->json([
                'message' => $waiting,
                'status' => (int) $ver->status,
            ]);
        }

        return response()->json([
            'message' => $sms,
            'status' => (int) $ver->status,
        ]);
    }

    public function delete_order(Request $request)
    {

        $id = $request->id;
        $ver = Verification::where('id', $id)->first();
        if (!$ver) {
            return redirect()->back()->with('error', "Order not found");
        }

        if ((int) $ver->status !== 1) {
            return redirect()->back()->with('message', "Order already processed or canceled");
        }

        // Apply cancel lock window for USA2 before allowing reject/refund.
        if ($ver->created_at && $ver->created_at->copy()->addSeconds(120)->isFuture()) {
            $left = now()->diffInSeconds($ver->created_at->copy()->addSeconds(120), false);
            $left = max(1, (int) $left);
            return redirect()->back()->with('error', "Please wait {$left}s before canceling this order.");
        }

        // If SMS already arrived, don't allow cancel/refund.
        $ck_sms = $this->check_sms($ver->order_id);
        if ($ck_sms !== 0) {
            return redirect()->back()->with('message', "Order already processed or canceled");
        }

        // IMPORTANT: UnlimitedPortal expects the provider activation/rental id, not our local DB row id.
        $providerId = (string) ($ver->order_id ?? '');
        if ($providerId === '') {
            return redirect()->back()->with('error', "Provider order id missing");
        }

        Log::info('usa2 cancel: attempting reject', [
            'verification_id' => (int) $ver->id,
            'user_id' => (int) $ver->user_id,
            'provider_order_id' => $providerId,
            'phone' => (string) $ver->phone,
        ]);

        $res2 = $this->sendRequest('reject', [
            'id' => $providerId,
            // Some UnlimitedPortal accounts accept mdn instead of id; send both for compatibility.
            'mdn' => (string) $ver->phone,
        ]);

        if (!is_array($res2)) {
            return redirect()->back()->with('error', "Cancel failed (provider unreachable). Try again.");
        }

        $result = (string) ($res2['status'] ?? '');
        if (strstr($result, "ok") === false) {
            $details = is_scalar($res2['message'] ?? null) ? (string) $res2['message'] : json_encode($res2['message'] ?? $res2);
            Log::warning('usa2 cancel: reject failed', [
                'verification_id' => (int) $ver->id,
                'provider_order_id' => $providerId,
                'provider_response' => $res2,
            ]);

            // Provider can return "Unable to reject an MDN" for some stale states.
            // If SMS is still not received, allow local refund to avoid trapping user funds.
            if (stripos($details, 'Unable to reject an MDN') !== false) {
                $recheck = $this->check_sms($ver->order_id);
                if ($recheck === 0) {
                    Log::warning('usa2 cancel: applying local refund fallback', [
                        'verification_id' => (int) $ver->id,
                        'provider_order_id' => $providerId,
                    ]);
                    return $this->refundPendingOrderAndDelete((int) $request->id, true);
                }
            }

            return redirect()->back()->with('error', "Cancel failed: {$details}");
        }

        Log::info('usa2 cancel: reject ok', [
            'verification_id' => (int) $ver->id,
            'provider_order_id' => $providerId,
            'provider_response' => $res2,
        ]);

        return $this->refundPendingOrderAndDelete((int) $request->id, false);
    }

    private function refundPendingOrderAndDelete(int $orderId, bool $providerFallback): \Illuminate\Http\RedirectResponse
    {
        DB::beginTransaction();

        try {
            $order = Verification::where('id', $orderId)->lockForUpdate()->first();

            if (!$order) {
                DB::rollBack();
                return back()->with('error', "Order not found");
            }

            if ((int) $order->status !== 1) {
                DB::rollBack();
                return back()->with('error', "Order already processed or canceled");
            }

            $user = User::where('id', $order->user_id)->lockForUpdate()->first();
            if (!$user) {
                DB::rollBack();
                return back()->with('error', "User not found");
            }

            $old_balance = (float) $user->wallet;
            $user->increment('wallet', $order->cost);
            $new_balance = $old_balance + (float) $order->cost;

            WalletCheck::where('user_id', $order->user_id)
                ->increment('wallet_amount', $order->cost);

            $bb = number_format($new_balance, 2);
            $message = $user->email . " | just canceled | $order->service | type is $order->type | NGN{$order->cost} refunded | Balance is $bb";
            send_notification($message);
            send_notification2($message);

            $trx = new Transaction();
            $trx->ref_id      = "Order Cancel " . $order->id;
            $trx->user_id     = $order->user_id;
            $trx->status      = 2;
            $trx->amount      = $order->cost;
            $trx->balance     = $new_balance;
            $trx->old_balance = $old_balance;
            $trx->type        = 3;
            $trx->save();

            $order->delete();

            DB::commit();

            if ($providerFallback) {
                return redirect()->back()->with('message', "Order canceled with fallback, NGN{$order->cost} refunded");
            }

            return redirect()->back()->with('message', "Order canceled, NGN{$order->cost} refunded");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Error: " . $e->getMessage());
        }
    }

    private function create_order_usa2($service, $price, $cost, $service_name, $gcost, $area_code)
    {
        $hasArea = $area_code !== null && trim((string) $area_code) !== '';
        $finalCostPreview = $hasArea ? $price * 1.2 : $price;

        try {
            return DB::transaction(function () use ($service, $price, $cost, $service_name, $gcost, $area_code, $hasArea, $finalCostPreview) {
                $userId = Auth::id();
                $user = User::where('id', $userId)->lockForUpdate()->first();
                if (!$user || (float) $user->wallet < $finalCostPreview) {
                    return 8;
                }

                $rent = $this->sendRequest('request', [
                    'service' => $service,
                    'areacode' => $hasArea ? trim((string) $area_code) : null,
                ]);

                if (!is_array($rent)) {
                    return 0;
                }

                $result = $rent['status'] ?? '';

                if (strstr((string) $result, 'NO_NUMBERS') !== false) {
                    return 56;
                }

                if (strstr((string) $result, 'MAX_PRICE_EXCEEDED') !== false) {
                    return 54;
                }

                if (strstr((string) $result, 'ok') === false) {
                    Log::info('Unlimited SNS Response ====>>> ' . json_encode($result) . ' Data ===> ' . $cost);
                    if ($result == 'MAX_PRICE_EXCEEDED' || $result == 'NO_NUMBERS' || $result == 'TOO_MANY_ACTIVE_RENTALS' || $result == 'NO_MONEY') {
                        return 0;
                    }

                    return 0;
                }

                $id = $rent['message'][0]['id'] ?? null;
                $phone = $rent['message'][0]['mdn'] ?? null;
                if ($id === null || $phone === null) {
                    return 0;
                }

                $finalCost = $hasArea ? $price + ($price * 0.20) : $price;

                $user = User::where('id', $userId)->lockForUpdate()->first();
                if (!$user || (float) $user->wallet < $finalCost) {
                    $this->sendRequest('reject', [
                        'id' => $id,
                        'mdn' => (string) $phone,
                    ]);

                    return 8;
                }

                Verification::where('phone', $phone)->delete();

                $oldBalance = (float) $user->wallet;
                $balance = $oldBalance - $finalCost;

                $ver = new Verification();
                $ver->user_id = $userId;
                $ver->phone = $phone;
                $ver->order_id = $id;
                $ver->country = 'US';
                $ver->service = $service_name;
                $ver->cost = $finalCost;
                $ver->api_cost = $gcost;
                $ver->status = 1;
                $ver->expires_in = 300;
                $ver->type = 3;
                $ver->save();

                User::where('id', $userId)->decrement('wallet', $finalCost);
                WalletCheck::where('user_id', $userId)->increment('total_bought', $finalCost);
                WalletCheck::where('user_id', $userId)->decrement('wallet_amount', $finalCost);

                $trx = new Transaction();
                $trx->ref_id = "Verification-$id";
                $trx->user_id = $userId;
                $trx->status = 2;
                $trx->amount = $finalCost;
                $trx->balance = $balance;
                $trx->old_balance = $oldBalance;
                $trx->type = 1;
                $trx->save();

                return 1;
            });
        } catch (\Throwable $e) {
            Log::error('create_order_usa2 failed', ['e' => $e->getMessage()]);

            return 0;
        }
    }
    private function check_sms($id)
    {
        if ($id === null || $id === '') {
            return 0;
        }

        $row = Verification::where('order_id', $id)->first();
        if (! $row) {
            return 0;
        }

        $ph = $row->phone;

        $res = $this->sendRequest('read_sms', [
            'mdn' => $ph,
        ]);


        $result = $res['status'];
        if (strstr($result, "ok") !== false) {



            $status = Verification::where('order_id', $id)->first() ?? null;
            if ($status) {
                $sms = trim((string) ($res['message'][0]['pin'] ?? ''));
                $fullsms = (string) ($res['message'][0]['reply'] ?? '');

                // Some providers may respond "ok" with an empty pin before the real OTP arrives.
                // Do not mark orders completed unless we have a real code to store.
                if ($sms === '') {
                    return 0;
                }


                $data['sms'] = $sms;
                $data['full_sms'] = $fullsms;

                Verification::where('order_id', $id)->update([
                    'status' => 2,
                    'sms' => $sms,
                    'full_sms' => $fullsms,
                ]);


                $message = "$id | completed";
                send_notification($message);


                return 3;

            }


        }else{


            return 0;

        }


    }


    public function requestStatus(Request $request)
    {
        return $this->sendRequest('request_status', [
            'id' => $request->id,
        ]);
    }

    public function rejectMDN(Request $request)
    {
        return $this->sendRequest('reject', [
            'id' => $request->id,
        ]);
    }



    public function sendSMS(Request $request)
    {
        return $this->sendRequest('send_sms', [
            'id' => $request->id,
            'msg' => $request->msg,
        ]);
    }



    public function rentMDN(Request $request)
    {
        return $this->sendRequest('ltr_rent', [
            'service' => $request->service,
            'country' => $request->country,
        ]);
    }

    public function renewRent(Request $request)
    {
        return $this->sendRequest('ltr_autorenew', [
            'id' => $request->id,
        ]);
    }


    public function rentStatus(Request $request)
    {
        return $this->sendRequest('ltr_status', [
            'id' => $request->id,
        ]);
    }

    public function activateRent(Request $request)
    {
        return $this->sendRequest('ltr_activate', [
            'id' => $request->id,
        ]);
    }

    public function listServices()
    {
        return $this->sendRequest('list_services');
    }

    public function getBalance()
    {
        return $this->sendRequest('balance');
    }
}
