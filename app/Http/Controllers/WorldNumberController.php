<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Setting;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorldNumberController extends Controller
{

    public function home(request $request)
    {

        $countries = get_world_countries();
        $services = get_world_services();

        $verification = Verification::where('user_id', Auth::id())->get();
        $verifications = Verification::where('user_id', Auth::id())->where('status', 1)->get();



        $data['services'] = $services;
        $data['countries'] = $countries;
        $data['verification'] = $verification;


        $data['product'] = null;

        $data['orders'] = Verification::where('user_id', Auth::id())->get();


        return view('world', $data);
    }



    public function check_av(Request $request)
    {

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

        $var = curl_exec($curl);
        curl_close($curl);

        $var = json_decode($var);



        $get_s_price = $var->price ?? null;
        $high_price = $var->high_price ?? null;
        $rate = $var->success_rate ?? null;
        $product = 1;



        if($high_price > 4){
            $price = $high_price * 1.3;
        }else{
            $price = $high_price;
        }






        if ($price == null) {
            return redirect('world')->with('error', 'Verification not available for selected service');
        } else {

            $get_rate = Setting::where('id', 1)->first()->rate;
            $margin = Setting::where('id', 1)->first()->margin;
            $verification = Verification::where('user_id', Auth::id())->get();
            $count_id = Country::where('country_id', $request->country)->first()->short_name ?? null;

            $ngnprice = ($price * $get_rate) + $margin;


            $data['count_id'] = $count_id;
            $data['serv'] = $request->service;
            $data['verification'] = $verification;
            $countries = get_world_countries();
            $services = get_world_services();
            $data['services'] = $services;
            $data['countries'] = $countries;
            $data['rate'] = $rate;
            $data['price'] = $ngnprice;
            $data['product'] = 1;
            $data['orders'] = Verification::where('user_id', Auth::id())->get();


            $data['country'] =

            $data['number_order'] = null;

            $verifications = Verification::where('user_id', Auth::id())->where('status', 1)->get();
            if ($verifications->count() > 1) {
                $data['pend'] = 1;
            } else {
                $data['pend'] = 0;
            }


            return view('world', $data);
        }
    }



    public function  get_smscode(request $request)
    {


        //$sms =  Verification::where('phone', $request->num)->first()->sms ?? null;
        $sms =  Verification::where('phone', $request->num)->first()->sms ?? null;



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


    public function webhook(request $request)
    {

        $message = json_encode($request->all());
        send_notification($message);
    }








    public function order_now(Request $request)
    {

        if (Auth::user()->wallet < $request->price) {
            return back()->with('error', "Insufficient Funds");
        }


        $ckn = Verification::where('user_id', Auth::id())->where('status', 1) ?? null;
        if ($ckn->count() == 1) {
            return redirect('world')->with('error', "Complete or End Pending Order");
        }



        User::where('id', Auth::id())->decrement('wallet', $request->price);

        $country = $request->country;
        $service = $request->service;
        $price = $request->price;


        $order = create_world_order($country, $service, $price);

        if ($order == 5) {
            User::where('id', Auth::id())->increment('wallet', $request->price);
            return redirect('world')->with('error', 'Number Currently out of stock, Please check back later');
        }

        if ($order == 1) {
            User::where('id', Auth::id())->increment('wallet', $request->price);
            $message = "ACESMSVERIFY | Low balance";
            send_notification($message);

            return redirect('world')->with('error', 'Error occurred, Please try again');
        }

        if ($order == 2) {
            User::where('id', Auth::id())->increment('wallet', $request->price);
            $message = "ACESMSVERIFY | Error";
            send_notification($message);
            send_notification3($message);

            return redirect('world')->with('error', 'Error occurred, Please try again');
        }

        if ($order == 3) {

            $countries = get_world_countries();
            $services = get_world_services();

            $verification = Verification::where('user_id', Auth::id())->get();
            $sms = Verification::where('user_id', Auth::id())->where('status', 1)->first()->sms;
            $number = Verification::where('user_id', Auth::id())->where('status', 1)->first()->phone;
            $num = Verification::where('user_id', Auth::id())->where('status', 1)->first();

            $data['services'] = $services;
            $data['countries'] = $countries;
            $data['verification'] = $verification;
            $data['sms'] = $sms;
            $data['number'] = $number;
            $data['product'] = null;

            $data['number_order'] = 1;
            $data['product'] = null;

            $data['num'] = $num;


            $data['services'] = get_world_services();
            $data['get_rate'] = Setting::where('id', 1)->first()->rate;
            $data['margin'] = Setting::where('id', 1)->first()->margin;
            $data['sms_order'] = Verification::where('user_id', Auth::id())->where('status' , 1)->first();
            $data['order'] = 1;

            $data['verification'] = Verification::where('user_id', Auth::id())->paginate(10);


            return view('receivesmsworld', $data);
        }
    }


    public function cancle_sms(Request $request)
    {

        $order = Verification::where('id', $request->id)->first() ?? null;

        if ($order == null) {
            return redirect('home')->with('error', 'Order not found');
        }

        if ($order->status == 2) {
            return redirect('home')->with('message', "Order Completed");
        }

        if ($order->status == 1) {

            $orderID = $order->order_id;
            $can_order = cancel_order($orderID);

            if ($can_order == 0) {
                return back()->with('error', "Please wait and try again later");
            }


            if ($can_order == 1) {
                $amount = number_format($order->cost, 2);
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return redirect('home')->with('message', "Order has been cancled, NGN$amount has been refunded");
            }


            if ($can_order == 3) {


                $order = Verification::where('id', $request->id)->first() ?? null;
                if ($order->status != 1 || $order == null) {
                    return redirect('home')->with('error', "Please try again later");
                }

                $amount = number_format($order->cost, 2);
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return redirect('home')->with('message', "Order has been cancled, NGN$amount has been refunded");
            }
        }
    }


    public function check_sms(Request $request)
    {

        $order = Verification::where('id', $request->id)->first() ?? null;

        if ($order == null) {
            return back()->with('error', 'Order not found');
        }

        if ($order->status == 1) {

            $orderID = $order->order_id;
            $sms = check_sms($orderID);

            if ($sms == 1) {
                return redirect('home2')->with('error', 'Sms Pending, please wait and refresh again');
            }

            if ($sms == 6) {
                $amount = number_format($order->cost, 2);
                User::where('id', Auth::id())->increment('wallet', $order->cost);
                Verification::where('id', $request->id)->delete();
                return redirect('home')->with('message', "Order has been canceled, NGN$amount has been refunded");
            }

            if ($sms == 6) {
                return back()->with('message', 'Sms Received, order completed');
            }
        }
    }

}
