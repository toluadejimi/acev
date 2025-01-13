<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Setting;
use App\Models\Transaction;
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



        if($get_s_price < 4){
            $price = $get_s_price * 1.3;
        }else{
            $price = $get_s_price;
        }



        if ($price == null) {
            return redirect('world')->with('error', 'Verification not available for selected service');
        } else {

            $get_rate = Setting::where('id', 1)->first()->rate;
            $margin = Setting::where('id', 1)->first()->margin;
            $verification = Verification::where('user_id', Auth::id())->get();
            $count_id = Country::where('country_id', $request->country)->first()->short_name ?? null;

            $data['get_rate'] = Setting::where('id', 1)->first()->rate;
            $data['margin'] = Setting::where('id', 1)->first()->margin;

            $gcost = pool_cost($request->service, $count_id);


            $ngnprice = ($data['get_rate'] * $gcost) + $data['margin'];;

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


    }








    public function order_now(Request $request)
    {





        $total_funded = Transaction::where('user_id', Auth::id())->where('status', 2)->sum('amount');
        $total_bought = verification::where('user_id', Auth::id())->where('status', 2)->sum('cost');
        if ($total_bought > $total_funded) {
            $message = Auth::user()->email . " need to be checked";
            send_notification($message);
            send_notification2($message);
            return back()->with('error', "Kindly Fund your wallet");

        }




        if(Auth::user()->wallet < $total_funded){
            $message = Auth::user()->email . " need to be checked";
            send_notification($message);
            send_notification2($message);
            return back()->with('error', "Please contact admin, for resolution");
        }


        if($request->price < 0 || $request->price == 0){
            return back()->with('error', "something went wrong");
        }

        if($request->price != $request->price2 && $request->price3 != $request->price4 ){

            return back()->with('error', "something went wrong");

        }

        if($request->price < 500 ){
            return back()->with('error', "something went wrong");
        }

        if (Auth::user()->wallet < $request->price) {
            return back()->with('error', "Insufficient Funds");
        }

        $country = $request->country;
        $service = $request->service;
        $price = $request->price;



        $data['get_rate'] = Setting::where('id', 1)->first()->rate;
        $data['margin'] = Setting::where('id', 1)->first()->margin;


        $gcost = pool_cost($service, $country);

        $calculatrdcost = ($data['get_rate'] * $gcost) + $data['margin'];

        if($request->price != $calculatrdcost){

            $message = "Price altred >>>>>>>". Auth::user()->email. " |  Request====>". json_encode($request->all());
            send_notification($message);
            send_notification2($message);

            return back()->with('error', "Price has been altered");
        };



        if($request->price < 1000){
            return back()->with('error', "please try again later");

        }


        if (Auth::user()->wallet < $calculatrdcost) {
            return back()->with('error', "Insufficient Funds");
        }





        $order = create_world_order($country, $service, $price, $calculatrdcost);

        if ($order == 5) {
            return redirect('world')->with('error', 'Number Currently out of stock, Please check back later');
        }


        if ($order == 7) {
            return redirect('world')->with('error', 'kindly fund your account and try again');
        }


        if ($order == 1) {
            $message = "ACESMSVERIFY | Low balance";
            send_notification($message);
            return redirect('world')->with('error', 'Error occurred, Please try again');
        }

        if ($order == 2) {
            $message = "ACESMSVERIFY | Error";
            send_notification($message);
            send_notification2($message);
            return redirect('world')->with('error', 'Error occurred, Please try again');
        }

        if ($order == 3) {

            return redirect('orders');


        }
    }






}
