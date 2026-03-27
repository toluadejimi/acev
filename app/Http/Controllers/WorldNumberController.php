<?php
//
//namespace App\Http\Controllers;
//
//use App\Models\Country;
//use App\Models\Setting;
//use App\Models\Transaction;
//use App\Models\User;
//use App\Models\Verification;
//use App\Models\WalletCheck;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;
//
//class WorldNumberController extends Controller
//{
//
//    public function home(request $request)
//    {
//
//        $countries = get_world_countries();
//        $services = get_world_services();
//
//        $verification = Verification::where('user_id', Auth::id())->get();
//        $verifications = Verification::where('user_id', Auth::id())->where('status', 1)->get();
//
//
//
//        $data['services'] = $services;
//        $data['countries'] = $countries;
//        $data['verification'] = $verification;
//
//
//        $data['product'] = null;
//
//        $data['orders'] = Verification::where('user_id', Auth::id())->get();
//
//
//        return view('world', $data);
//    }
//
//
//
//    public function check_av(Request $request)
//    {
//
//        $key = env('WKEY');
//
//
//        $databody = array(
//            "key" => $key,
//            "country" => $request->country,
//            "service" => $request->service,
//            "pool" => '',
//        );
//
//
//
//        $body = json_encode($databody);
//        $curl = curl_init();
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => 'https://api.smspool.net/request/price',
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => 'POST',
//            CURLOPT_POSTFIELDS => $databody,
//            CURLOPT_HTTPHEADER => array(
//                'Authorization: Bearer {{apikey}}'
//            ),
//        ));
//
//        $var = curl_exec($curl);
//        curl_close($curl);
//
//        $var = json_decode($var);
//
//
//        $get_s_price = $var->price ?? null;
//        $high_price = $var->high_price ?? null;
//        $rate = $var->success_rate ?? null;
//        $product = 1;
//
//
//        if($get_s_price < 4){
//            $price = $get_s_price * 1.3;
//        }else{
//            $price = $get_s_price;
//        }
//
//
//
//        if ($price == null) {
//            return redirect('world')->with('error', 'Verification not available for selected service');
//        } else {
//
//            $get_rate = Setting::where('id', 1)->first()->rate;
//            $margin = Setting::where('id', 1)->first()->margin;
//            $verification = Verification::where('user_id', Auth::id())->get();
//            $count_id = Country::where('country_id', $request->country)->first()->short_name ?? null;
//
//            $data['get_rate'] = Setting::where('id', 1)->first()->rate;
//            $data['margin'] = Setting::where('id', 1)->first()->margin;
//
//            $gcost = pool_cost($request->service, $count_id);
//
//
//            $ngnprice = ($data['get_rate'] * $gcost) + $data['margin'];;
//
//            $data['count_id'] = $count_id;
//            $data['serv'] = $request->service;
//            $data['verification'] = $verification;
//            $countries = get_world_countries();
//            $services = get_world_services();
//            $data['services'] = $services;
//            $data['countries'] = $countries;
//            $data['rate'] = $rate;
//            $data['price'] = $ngnprice;
//            $data['product'] = 1;
//            $data['orders'] = Verification::where('user_id', Auth::id())->get();
//
//
//            $data['country'] =
//
//            $data['number_order'] = null;
//
//            $verifications = Verification::where('user_id', Auth::id())->where('status', 1)->get();
//            if ($verifications->count() > 1) {
//                $data['pend'] = 1;
//            } else {
//                $data['pend'] = 0;
//            }
//
//            return view('world', $data);
//        }
//    }
//
//
//
//    public function  get_smscode(request $request)
//    {
//
//
//        //$sms =  Verification::where('phone', $request->num)->first()->sms ?? null;
//        $sms =  Verification::where('phone', $request->num)->first()->sms ?? null;
//
//
//
//        $originalString = 'waiting for sms';
//        $processedString = str_replace('"', '', $originalString);
//
//
//        if ($sms == null) {
//            return response()->json([
//                'message' => $processedString
//            ]);
//        } else {
//
//            return response()->json([
//                'message' => $sms
//            ]);
//        }
//    }
//
//
//    public function webhook(request $request)
//    {
//
//
//    }
//
//
//
//
//
//
//
//
//    public function order_now(Request $request)
//    {
//        $wallet_check = WalletCheck::where('user_id', Auth::id())->first();
//
//        if(!$wallet_check){
//            Auth::logout();
//            return redirect('login');
//        }
//
//
//
//        if($request->price < 0 || $request->price == 0){
//            return redirect('world')->with('error', "something went wrong");
//        }
//
//        if($request->price != $request->price2 && $request->price3 != $request->price4 ){
//
//            return redirect('world')->with('error', "something went wrong");
//
//        }
//
//        if($request->price < 500 ){
//            return redirect('world')->with('error', "something went wrong");
//        }
//
//        if (Auth::user()->wallet < $request->price) {
//            return redirect('world')->with('error', "Insufficient Funds");
//        }
//
//        $country = $request->country;
//        $service = $request->service;
//        $price = $request->price;
//
//
//
//
//        $data['get_rate'] = Setting::where('id', 1)->first()->rate;
//        $data['margin'] = Setting::where('id', 1)->first()->margin;
//
//
//        $gcost = pool_cost($service, $country);
//        $calculatrdcost = ($data['get_rate'] * $gcost) + $data['margin'];
//
//
//        if((int)$request->price != (int)$calculatrdcost){
//            $message = "Price altred >>>>>>>". Auth::user()->email. " |  Request====>". json_encode($request->all());
//            send_notification($message);
//            send_notification2($message);
//
//            return redirect('world')->with('error', "Price has been altered");
//        };
//
//
//
//        if($request->price < 1000){
//            return redirect('world')->with('error', "please try again later");
//
//        }
//
//
//        if (Auth::user()->wallet < $calculatrdcost) {
//
//            return redirect('world')->with('error', "Insufficient Funds");
//        }
//
//
//
//
//        $order = create_world_order($country, $service, $price, $calculatrdcost);
//
//        if ($order == 99) {
//            return redirect('world')->with('error', "Insufficient Funds");
//        }
//
//
//        if ($order == 5) {
//            return redirect('world')->with('error', 'Number Currently out of stock, Please check back later');
//        }
//
//
//        if ($order == 7) {
//            Auth::logout();
//            return redirect('login')->with('error', "Please Contact admin");
//        }
//
//
//        if ($order == 1) {
//            $message = "ACESMSVERIFY | Low balance";
//            send_notification($message);
//            return redirect('world')->with('error', 'Error occurred, Please try again');
//        }
//
//        if ($order == 2) {
//            $message = "ACESMSVERIFY | Error";
//            send_notification($message);
//            send_notification2($message);
//            return redirect('world')->with('error', 'Error occurred, Please try again');
//        }
//
//        if ($order == 3) {
//            return redirect('us');
//        }
//    }
//
//
//
//
//
//
//}


namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Setting;
use App\Models\Verification;
use App\Models\WalletCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WorldNumberController extends Controller
{
    public function home()
    {
        session(['world_provider' => 'smspool']);
        $flags = verification_server_flags();
        if (empty($flags['world'])) {
            if (!empty($flags['us1'])) {
                return redirect()->route('verification.index')->with('topMessage', 'World server is currently unavailable.');
            }
            if (!empty($flags['us2'])) {
                return redirect('/usa2')->with('topMessage', 'World server is currently unavailable.');
            }
            return redirect('/home')->with('topMessage', 'Verification service is currently unavailable.');
        }

        $countries = get_world_countries();
        $data['countries'] = $countries;
        $data['services'] = []; // initially empty
        $data['product'] = null;
        $data['verification'] = Verification::latest()->where('user_id', Auth::id())->get();
        $data['verificationServers'] = $flags;
        $data['worldServer'] = 'world';


        return view('world', $data);
    }

    public function heroHome()
    {
        session(['world_provider' => 'herosms']);
        $flags = verification_server_flags();
        if (empty($flags['world_hero'])) {
            if (!empty($flags['us1'])) {
                return redirect()->route('verification.index')->with('topMessage', 'HeroSMS World server is currently unavailable.');
            }
            if (!empty($flags['us2'])) {
                return redirect('/usa2')->with('topMessage', 'HeroSMS World server is currently unavailable.');
            }
            if (!empty($flags['world'])) {
                return redirect('/world')->with('topMessage', 'HeroSMS World server is currently unavailable.');
            }
            return redirect('/home')->with('topMessage', 'Verification service is currently unavailable.');
        }

        $countries = $this->getHeroCountries();
        $data['countries'] = $countries;
        $data['services'] = [];
        $data['product'] = null;
        $data['verification'] = Verification::latest()->where('user_id', Auth::id())->get();
        $data['verificationServers'] = $flags;
        $data['worldServer'] = 'world_hero';

        return view('world', $data);
    }

    public function sv3Home()
    {
        session(['world_provider' => 'sv3']);
        $flags = verification_server_flags();
        if (empty($flags['world_sv3'])) {
            if (!empty($flags['us2'])) {
                return redirect('/usa2')->with('topMessage', 'World SV3 server is currently unavailable.');
            }
            if (!empty($flags['world'])) {
                return redirect('/world')->with('topMessage', 'World SV3 server is currently unavailable.');
            }
            return redirect('/home')->with('topMessage', 'Verification service is currently unavailable.');
        }

        $countries = $this->getSv3Countries();
        if ($countries === []) {
            // Fallback so UI still works if provider country endpoint is down.
            $countries = $this->getHeroCountries();
        }
        $data['countries'] = $countries;
        $data['services'] = [];
        $data['product'] = null;
        $data['verification'] = Verification::latest()->where('user_id', Auth::id())->get();
        $data['verificationServers'] = $flags;
        $data['worldServer'] = 'world_sv3';

        return view('world', $data);
    }

    public function getServices($countryID)
    {
        $provider = $this->resolveProvider(request());
        if ($provider === 'herosms') {
            if (!verification_server_flags()['world_hero']) {
                return response()->json(['status' => 'error', 'message' => 'Hero world server disabled'], 403);
            }
            return response()->json($this->getHeroServices());
        } elseif ($provider === 'sv3') {
            if (!verification_server_flags()['world_sv3']) {
                return response()->json(['status' => 'error', 'message' => 'World SV3 disabled'], 403);
            }
            $services = $this->getSv3Services();
            if ($services === []) {
                // Fallback for temporary provider response issues.
                $services = $this->getHeroServices();
            }
            return response()->json($services);
        }
        if (!verification_server_flags()['world']) {
            return response()->json(['status' => 'error', 'message' => 'World server disabled'], 403);
        }
        $services = get_world_services();
        return response()->json($services);
    }

    public function checkAvailability(Request $request)
    {
        $provider = $this->resolveProvider($request);
        if ($provider === 'herosms') {
            if (!verification_server_flags()['world_hero']) {
                return response()->json(['status' => 'error', 'message' => 'Hero world server disabled'], 403);
            }
            $opts = hero_sms_get_price_options((string) $request->service, (string) $request->country);
            if ($opts === []) {
                Log::warning('HeroSMS availability failed: no price', [
                    'user_id' => Auth::id(),
                    'country' => $request->country,
                    'service' => $request->service,
                ]);

                return response()->json(['status' => 'error', 'message' => 'Service not available']);
            }
            $rate = verification_server_rate('world_hero');
            $margin = verification_server_margin('world_hero');
            $priceOptions = [];
            foreach ($opts as $opt) {
                $apiCost = (float) $opt['cost'];
                $ratePortion = $rate * $apiCost;
                $ngnTotal = $ratePortion + $margin;
                $priceOptions[] = [
                    'label' => (string) $opt['label'],
                    'api_cost' => $apiCost,
                    'api_cost_formatted' => number_format($apiCost, 4, '.', ''),
                    'rate_multiplier' => (float) $rate,
                    'margin_ngn' => (float) $margin,
                    'margin_ngn_formatted' => number_format((float) $margin, 2, '.', ''),
                    'rate_amount_ngn' => (float) $ratePortion,
                    'rate_amount_ngn_formatted' => number_format((float) $ratePortion, 2, '.', ''),
                    'ngn_total' => (float) $ngnTotal,
                    'ngn_total_formatted' => number_format((float) $ngnTotal, 2, '.', ''),
                ];
            }
            $firstNgn = $priceOptions[0]['ngn_total_formatted'];

            return response()->json([
                'status' => 'success',
                'price' => $firstNgn,
                'rate' => 0,
                'price_options' => $priceOptions,
            ]);
        } elseif ($provider === 'sv3') {
            if (!verification_server_flags()['world_sv3']) {
                return response()->json(['status' => 'error', 'message' => 'World SV3 disabled'], 403);
            }
            $opts = world_sv3_get_price_options((string) $request->service, (string) $request->country);
            if ($opts === []) {
                return response()->json(['status' => 'error', 'message' => 'Service not available']);
            }
            $rate = verification_server_rate('world_sv3');
            $margin = verification_server_margin('world_sv3');
            $priceOptions = [];
            foreach ($opts as $opt) {
                $apiCost = (float) $opt['cost'];
                $ratePortion = $rate * $apiCost;
                $ngnTotal = $ratePortion + $margin;
                $priceOptions[] = [
                    'label' => (string) $opt['label'],
                    'api_cost' => $apiCost,
                    'api_cost_formatted' => number_format($apiCost, 4, '.', ''),
                    'rate_multiplier' => (float) $rate,
                    'margin_ngn' => (float) $margin,
                    'margin_ngn_formatted' => number_format((float) $margin, 2, '.', ''),
                    'rate_amount_ngn' => (float) $ratePortion,
                    'rate_amount_ngn_formatted' => number_format((float) $ratePortion, 2, '.', ''),
                    'ngn_total' => (float) $ngnTotal,
                    'ngn_total_formatted' => number_format((float) $ngnTotal, 2, '.', ''),
                ];
            }
            return response()->json([
                'status' => 'success',
                'price' => $priceOptions[0]['ngn_total_formatted'],
                'rate' => 0,
                'price_options' => $priceOptions,
            ]);
        }
        if (!verification_server_flags()['world']) {
            return response()->json(['status' => 'error', 'message' => 'World server disabled'], 403);
        }
        $key = env('WKEY');

        $databody = [
            "key" => $key,
            "country" => $request->country,
            "service" => $request->service,
            "pool" => '',
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.smspool.net/request/price',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $databody,
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $res = json_decode($response);

        if (!isset($res->price)) {
            return response()->json(['status' => 'error', 'message' => 'Service not available']);
        }

        $price = $res->price < 4 ? $res->price * 1.3 : $res->price;

        $setting =  Setting::find(2);
        $rate = $setting->rate;
        $margin = $setting->margin;
        $count_id = Country::where('country_id', $request->country)->first()->short_name ?? null;

        $gcost = pool_cost($request->service, $count_id);


        $ngnPrice = ($rate * $gcost) + $margin;


        return response()->json([
            'status' => 'success',
            'price' => number_format($ngnPrice, 2),
            'rate' => $res->success_rate ?? 0
        ]);
    }

    public function orderNumber(Request $request)
    {
        $provider = $this->resolveProvider($request);
        if ($provider === 'herosms') {
            if (!verification_server_flags()['world_hero']) {
                return response()->json(['status' => 'error', 'message' => 'Hero world server disabled'], 403);
            }
        } elseif ($provider === 'sv3') {
            if (!verification_server_flags()['world_sv3']) {
                return response()->json(['status' => 'error', 'message' => 'World SV3 disabled'], 403);
            }
        } elseif (!verification_server_flags()['world']) {
            return response()->json(['status' => 'error', 'message' => 'World server disabled'], 403);
        }
        $validator = Validator::make($request->all(), array_merge([
            'country' => 'required',
            'service' => 'required',
            'service_name' => 'nullable|string|max:255',
        ], in_array($provider, ['herosms', 'sv3'], true) ? [
            'api_cost' => 'nullable|numeric|min:0.0000001',
            'hero_api_cost' => 'nullable|numeric|min:0.0000001',
        ] : []));

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $countryToken = (string) $request->country;
        if (!in_array($provider, ['herosms', 'sv3'], true)) {
            $countryRow = Country::where('country_id', $request->country)->first();
            if (!$countryRow || !$countryRow->short_name) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid country',
                ], 400);
            }
            $countryToken = (string) $countryRow->short_name;
        }

        if (in_array($provider, ['herosms', 'sv3'], true)) {
            $pickedRaw = $request->input('api_cost', $request->input('hero_api_cost'));
            if ($pickedRaw === null || $pickedRaw === '') {
                Log::warning('World order missing api_cost; falling back to lowest tier', [
                    'provider' => $provider,
                    'user_id' => Auth::id(),
                    'country' => (string) $request->country,
                    'service' => (string) $request->service,
                ]);
                $gcost = pool_cost($request->service, $countryToken, $provider);
            } else {
                $picked = (float) $pickedRaw;
                $allowed = $provider === 'herosms'
                    ? hero_sms_api_cost_is_allowed($picked, (string) $request->service, $countryToken)
                    : world_sv3_api_cost_is_allowed($picked, (string) $request->service, $countryToken);
                if (!$allowed) {
                    Log::warning('World alt-provider order rejected: api cost not in live price list', [
                        'provider' => $provider,
                        'user_id' => Auth::id(),
                        'country' => $request->country,
                        'service' => $request->service,
                        'api_cost' => $picked,
                    ]);

                    return response()->json([
                        'status' => 'error',
                        'message' => 'That price tier is no longer available. Please refresh and choose again.',
                    ], 400);
                }
                $gcost = $picked;
            }
        } else {
            $gcost = pool_cost($request->service, $countryToken, $provider);
        }
        if ($gcost === null || (float) $gcost <= 0) {
            if (in_array($provider, ['herosms', 'sv3'], true)) {
                Log::warning('World alt-provider order failed at cost lookup', [
                    'provider' => $provider,
                    'user_id' => Auth::id(),
                    'country' => $request->country,
                    'country_token' => $countryToken,
                    'service' => $request->service,
                ]);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Service not available',
            ], 400);
        }

        if ($provider === 'herosms') {
            $rate = verification_server_rate('world_hero');
            $margin = verification_server_margin('world_hero');
        } elseif ($provider === 'sv3') {
            $rate = verification_server_rate('world_sv3');
            $margin = verification_server_margin('world_sv3');
        } else {
            $setting = Setting::find(2);
            if (!$setting) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Settings not configured',
                ], 500);
            }
            $rate = (float) $setting->rate;
            $margin = (float) $setting->margin;
        }
        $required = ($rate * (float) $gcost) + $margin;
        $wallet = (float) Auth::user()->wallet;

        if ($wallet < $required) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient wallet balance',
                'wallet_balance' => $wallet,
                'required_amount' => $required
            ], 400);
        }

        if (in_array($provider, ['herosms', 'sv3'], true)) {
            Log::info('World alt-provider order attempt', [
                'provider' => $provider,
                'user_id' => Auth::id(),
                'country' => $request->country,
                'service' => $request->service,
                'gcost' => $gcost,
                'required' => $required,
            ]);
        }
        $serviceName = trim((string) $request->input('service_name', ''));

        $order = create_world_order(
            $request->country,
            $request->service,
            $provider,
            in_array($provider, ['herosms', 'sv3'], true) ? (float) $gcost : null,
            $serviceName !== '' ? $serviceName : null
        );

        if (in_array($provider, ['herosms', 'sv3'], true)) {
            Log::info('World alt-provider order result', [
                'provider' => $provider,
                'user_id' => Auth::id(),
                'country' => $request->country,
                'service' => $request->service,
                'result' => $order,
            ]);
        }


        if ($order == 3) {
            return response()->json([
                'status' => 'success',
                'message' => 'Order completed successfully'
            ]);
        }

        if ($order == 99) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient wallet balance'
            ], 400);
        }

        if ($order == 97) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid country',
            ], 400);
        }

        $providerMessage = trim((string) Cache::pull('world_order_last_error_user_' . (int) Auth::id(), ''));
        if ($providerMessage !== '') {
            world_order_force_log('World order failed with provider message', [
                'user_id' => Auth::id(),
                'provider' => $provider,
                'country' => $request->country,
                'service' => $request->service,
                'message' => $providerMessage,
            ]);
        } else {
            world_order_force_log('World order failed with empty provider message', [
                'user_id' => Auth::id(),
                'provider' => $provider,
                'country' => $request->country,
                'service' => $request->service,
                'request_payload' => $request->except(['_token']),
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $providerMessage !== '' ? $providerMessage : 'Unable to complete purchase',
        ], 400);
    }

    private function resolveProvider(Request $request): string
    {
        $provider = strtolower((string) $request->session()->get('world_provider', 'smspool'));
        return in_array($provider, ['smspool', 'herosms', 'sv3'], true) ? $provider : 'smspool';
    }

    private function getHeroCountries(): array
    {
        $resp = Http::timeout(50)->get(world_sms_handler_url(['action' => 'getCountries']));
        $json = $resp->json();
        if (!is_array($json)) {
            return [];
        }
        $mapped = [];
        foreach ($json as $row) {
            if (!is_array($row) || !isset($row['id'])) {
                continue;
            }
            $mapped[] = ['ID' => $row['id'], 'name' => ($row['eng'] ?? $row['name'] ?? 'Unknown')];
        }
        return $mapped;
    }

    private function getHeroServices(): array
    {
        $resp = Http::timeout(50)->get(world_sms_handler_url(['action' => 'getServicesList', 'lang' => 'en']));
        $json = $resp->json();
        $rows = is_array($json) ? ($json['services'] ?? []) : [];
        $mapped = [];
        foreach ($rows as $row) {
            if (!is_array($row) || !isset($row['code'])) {
                continue;
            }
            $mapped[] = ['ID' => $row['code'], 'name' => ($row['name'] ?? $row['code'])];
        }
        return $mapped;
    }

    private function getSv3Countries(): array
    {
        $resp = Http::timeout(50)->get(world_sms_sv3_handler_url(['action' => 'getCountries']));
        $json = $resp->json();
        if (!is_array($json)) {
            return [];
        }
        $mapped = [];
        foreach ($json as $row) {
            if (!is_array($row) || !isset($row['id'])) {
                continue;
            }
            $mapped[] = ['ID' => $row['id'], 'name' => ($row['eng'] ?? $row['name'] ?? 'Unknown')];
        }
        return $mapped;
    }

    private function getSv3Services(): array
    {
        $resp = Http::timeout(50)->get(world_sms_sv3_handler_url(['action' => 'getServicesList', 'lang' => 'en']));
        $json = $resp->json();
        $rows = is_array($json) ? ($json['services'] ?? []) : [];
        $mapped = [];
        foreach ($rows as $row) {
            if (!is_array($row) || !isset($row['code'])) {
                continue;
            }
            $mapped[] = ['ID' => $row['code'], 'name' => ($row['name'] ?? $row['code'])];
        }
        return $mapped;
    }
}
