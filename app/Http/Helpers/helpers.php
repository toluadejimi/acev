<?php

use App\Constants\Status;
use App\Models\Extension;
use App\Models\Verification;
use App\Lib\GoogleAuthenticator;
use Illuminate\Support\Facades\Auth;



function resolve_complete($order_id)
{

    $curl = curl_init();

    $databody = array('order_id' => "$order_id");

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://web.enkpay.com/api/resolve-complete',
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
            CURLOPT_URL => 'https://api.telegram.org/bot6140179825:AAGfAmHK6JQTLegsdpnaklnhBZ4qA1m2c64/sendMessage?chat_id=1316552414',
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
            // CURLOPT_URL => 'https://api.telegram.org/bot7059926156:AAHrb7Kt_uqNlSjblpQuf2xbgwIwggZxJng/sendMessage?chat_id=986615350',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'chat_id' => "986615350",
                'text' => $message,

            ),
            CURLOPT_HTTPHEADER => array(),
        ));

        $var = curl_exec($curl);
        curl_close($curl);

        $var = json_decode($var);
    }



function session_resolve($session_id, $ref){

    $curl = curl_init();

    $databody = array(
        'session_id' => "$session_id",
        'ref' => "$ref"
    );


    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://web.enkpay.com/api/resolve',
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




function get_services(){

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


function create_order($service, $price, $cost, $service_name){


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
   $result = $var ??  null;

    if(strstr($result, "ACCESS_NUMBER") !== false) {

        $parts = explode(":", $result);
        $accessNumber = $parts[0];
        $id = $parts[1];
        $phone = $parts[2];

        $ver = new Verification();
        $ver->user_id = Auth::id();
        $ver->phone = $phone;
        $ver->order_id = $id;
        $ver->country = "US";
        $ver->service = $service_name;
        $ver->cost = $price;
        $ver->api_cost = $cost;
        $ver->status = 1;
        $ver->type = 1;
        $ver->save();
        return 1;

    }elseif($result == "MAX_PRICE_EXCEEDED" || $result == "NO_NUMBERS" || $result == "TOO_MANY_ACTIVE_RENTALS" || $result == "NO_MONEY") {
        return 0;
    }else{
        return 0;
    }




}

function cancel_order($orderID){


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

    if(strstr($result, "ACCESS_CANCEL") !== false){

        return 1;

    }else{

        return 0;

    }




}

function check_sms($orderID){



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

    if(strstr($result, "NO_ACTIVATION") !== false){

        return 1;

    }

    if(strstr($result, "NO_ACTIVATION") !== false){

        return 1;

    }

    if(strstr($result, "STATUS_WAIT_CODE") !== false){

        return 2;

    }

    if(strstr($result, "STATUS_CANCEL") !== false){

        return 4;

    }




    if(strstr($result, "STATUS_OK") !== false) {


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

        return 3;

    }


}




function get_world_countries(){
    $key = env('WKEY');
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.smspool.net/country/retrieve_all?key=$key",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json',
        ),
    ));

    $var = curl_exec($curl);
    curl_close($curl);
    $var = json_decode($var);
    $countries = $var ?? null;




    // $history = [];
    // foreach ($countries as $key => $value) {
    //     $history[] = array(
    //         "country_id" => $value->ID,
    //         "name" => $value->name,
    //         "short_name" => $value->short_name,

    //     );
    // }

    // $rr =  DB::table('countries')->insert($history);




    if ($var == null) {
        $coiuntries = null;
    }


    return $countries;
}


function get_world_services(){

    $key = env('WKEY');

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.smspool.net/service/retrieve_all?key=$key",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
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


function create_world_order($country, $service, $price){

    $key = env('WKEY');
    $curl = curl_init();

    $databody = array(
        'country' => $country,
        'service' => $service,
        'key' => $key,


    );
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.smspool.net/purchase/sms',
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


    $success = $var->success ?? null;

    if($success == 0){
        return 5;

    }



    if($success == 1){

        $ver = new Verification();
        $ver->user_id = Auth::id();
        $ver->phone = $var->cc.$var->phonenumber;
        $ver->order_id = $var->order_id;
        $ver->country = $var->country;
        $ver->service = $var->service;
        $ver->expires_in = $var->expires_in / 10 - 20;
        $ver->cost = $price;
        $ver->api_cost = $var->cost;
        $ver->status = 1;
        $ver->type = 2;

        $ver->save();

        return 3;



    }








    $status = $var->type ?? null;

    if($status == "BALANCE_ERROR"){
        return  1;
    }

    if($status == null){
        return 2;

    }




}

function cancel_world_order($orderID){

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

    if($status == 0 && $message == "We could not find this order!"){
        return 3;
    }

    if($status == 0 && $message == "Your order cannot be cancelled yet, please try again later."){
        return 0;
    }



    if($status == 0){
        return 0;
    }

    if($status == 1){
        return 1;
    }




}

function check_world_sms($orderID){

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


    if($status == 1){

        Verification::where('order_id', $orderID)->update([
            'expires_in' => $var->time_left / 10 - 20,
        ]);

        return 1;
    }

    if($status == 6){
        return 6;
    }


    if($status == 3){

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

