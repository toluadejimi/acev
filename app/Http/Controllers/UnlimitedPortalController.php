<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Verification;
use App\Models\WalletCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

        if (Auth::user()->wallet < $request->price) {
            $data['status'] = false;
            $data['message'] = "Insufficient Funds";

            return $data;
        }

        if (Auth::user()->wallet < $request->price) {
            $data['status'] = false;
            $data['message'] = "Insufficient Funds";

            return $data;
        }

        $data2['get_rate'] = Setting::where('id', 3)->first()->rate;
        $data2['margin'] = Setting::where('id', 3)->first()->margin;



        $res  = $this->sendRequest('list_services',[
        'service' => $request->service,
        ]);

        $gcost = (double)$res['message'][0]['price'];


        $costs = ($data2['get_rate'] * $gcost) + $data2['margin'];


        if (Auth::user()->wallet < $costs) {
            $data['status'] = false;
            $data['message'] = "Insufficient Funds";
            return $data;
        }


        $service = $request->provider;
        $price = $request->price;
        $cost = $request->cost;
        $service_name = $request->service;
        $area_code = $request->areaCode;
        $carrier = $request->carrier;


        $order = $this->create_order_usa2($service, $price, $cost, $service_name, $gcost, $area_code);


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
        $sms = Verification::where('phone', $request->num)->first()->sms ?? null;
        $order_id = Verification::where('phone', $request->num)->first()->order_id ?? null;


        $ck_sms = $this->check_sms($order_id);


        $ck_phone = Verification::where('phone', $request->num)->first()->type ?? null;


        $originalString = 'waiting for sms';
        $processedString = str_replace('"', '', $originalString);


        if ($sms == null) {
            return response()->json([
                'message' => $processedString
            ]);
        } else {

            return response()->json([
                'message' => $sms
            ]);
        }

    }

    public function delete_order(Request $request)
    {

        $id = $request->id;
       $ver =  Verification::where('id', $id)->first();
       if($ver){
           if($ver->status == 1){
               $ck_sms = $this->check_sms($ver->order_id);
               if($ck_sms === 0){

                   $res2 =  $this->sendRequest('reject', [
                       'id' => $id,
                   ]);


                   $result = $res2['status'];
                   if (strstr($result, "ok") !== false){


                       DB::beginTransaction();

                       try {
                           $order = Verification::where('id', $request->id)->lockForUpdate()->first();

                           if (!$order) {
                               DB::rollBack();
                               return back()->with('error', "Order not found");
                           }

                           if ($order->status != 1) {
                               DB::rollBack();
                               return back()->with('error', "Order already processed or canceled");
                           }


                           $user = User::where('id', $order->user_id)->lockForUpdate()->first();

                           $old_balance = $user->wallet;
                           $user->increment('wallet', $order->cost);
                           $new_balance = $old_balance + $order->cost;

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

                           return back()->with('message', "Order canceled, NGN{$order->cost} refunded");
                       } catch (\Exception $e) {
                           DB::rollBack();
                           return back()->with('error', "Error: " . $e->getMessage());
                       }

                   }

               }
           }

           return back()->with('error', "Order already processed or canceled");

       }


    }

    private function create_order_usa2($service, $price, $cost, $service_name, $gcost, $area_code)
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


        if($area_code != null){


            $finalCost = $price + ($price * 0.20);
            if (Auth::user()->wallet < $finalCost) {
                return 8;
            }


        }



        $rent = $this->sendRequest('request', [
            'service' => $service,
            'areacode' => $area_code ?? null,
        ]);



        $result = $rent['status'];


        if (strstr($result, "NO_NUMBERS") !== false) {

            return 56;

        }

        if (strstr($result, "MAX_PRICE_EXCEEDED") !== false) {

            return 54;

        }


        if (strstr($result, "ok") !== false) {


            if (Auth::user()->wallet < $price) {
                return 8;
            }

            $id = $rent['message'][0]['id'];
            $phone = $rent['message'][0]['mdn'];


            Verification::where('phone', $phone)->where('status', 2)->delete() ?? null;


            if($area_code != null ){

                $finalCost = $price + ($price * 0.20);

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
                $ver->type = 3;
                $ver->save();


                $get_balance = User::where('id', Auth::id())->first()->wallet;
                $balance = $get_balance - $finalCost;

                User::where('id', Auth::id())->decrement('wallet', $finalCost);
                WalletCheck::where('user_id', Auth::id())->increment('total_bought', $finalCost);
                WalletCheck::where('user_id', Auth::id())->decrement('wallet_amount', $finalCost);


                $trx = new Transaction();
                $trx->ref_id = "Verification-$id";
                $trx->user_id = Auth::id();
                $trx->status = 2;
                $trx->amount = $finalCost;
                $trx->balance = $balance;
                $trx->old_balance = $get_balance;
                $trx->type = 1;
                $trx->save();

                return 1;

            }


            $ver = new Verification();
            $ver->user_id = Auth::id();
            $ver->phone = $phone;
            $ver->order_id = $id;
            $ver->country = "US";
            $ver->service = $service_name;
            $ver->cost = $price;
            $ver->api_cost = $gcost;
            $ver->status = 1;
            $ver->expires_in = 300;
            $ver->type = 3;
            $ver->save();



            $get_balance = User::where('id', Auth::id())->first()->wallet;
            $balance = $get_balance - $price;

            User::where('id', Auth::id())->decrement('wallet', $price);
            WalletCheck::where('user_id', Auth::id())->increment('total_bought', $price);
            WalletCheck::where('user_id', Auth::id())->decrement('wallet_amount', $price);


            $trx = new Transaction();
            $trx->ref_id = "Verification-$id";
            $trx->user_id = Auth::id();
            $trx->status = 2;
            $trx->amount = $price;
            $trx->balance = $balance;
            $trx->old_balance = $get_balance;
            $trx->type = 1;
            $trx->save();


            return 1;

        }

        Log::info("Unlimited SNS Response ====>>>". json_encode($result)."Data ===> $cost");


        if ($result == "MAX_PRICE_EXCEEDED" || $result == "NO_NUMBERS" || $result == "TOO_MANY_ACTIVE_RENTALS" || $result == "NO_MONEY") {
            return 0;
        }



    }
    private function check_sms($id)
    {

        $ph = Verification::where('order_id', $id)->first()->phone;

        $res = $this->sendRequest('read_sms', [
            'mdn' => $ph,
        ]);


        $result = $res['status'];
        if (strstr($result, "ok") !== false) {



            $status = Verification::where('order_id', $id)->first() ?? null;
            if ($status) {
                $sms = $res['message'][0]['pin'];
                $fullsms = $res['message'][0]['reply'];


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
