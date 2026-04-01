<?php

namespace App\Http\Controllers;

use App\Models\AccountDetail;
use App\Models\Deposit;
use App\Models\ManualPayment;
use App\Models\Notification;
use App\Models\PaymentMethod;
use App\Models\PaymentPoint;
use App\Models\Setting;
use App\Models\SoldLog;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Verification;
use App\Models\VerificationSms;
use App\Models\WalletCheck;
use App\Models\WebhookResponse;
use App\Services\VerificationPricingService;
use App\Services\WalletFundingService;
use App\Support\SprintPayWebhookAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use function Sodium\randombytes_random16;


class HomeController extends Controller
{
    public function api_docs(request $request)
    {

        $data['api_key'] = Auth::user()->api_key ?? null;
        $data['webhook_url'] = Auth::user()->webhook_url ?? null;
        $data['verification_servers'] = verification_server_flags();

        return view('api', $data);

    }

    public function set_webhook(request $request)
    {

        User::where('id', Auth::id())->update(['webhook_url' => $request->webhook]);
        return back()->with('message', 'Webhook Set successfully');


    }


    public function generate_token(request $request)
    {

        $token = Str::random(30) . date('mhis');

        User::where('id', Auth::id())->update(['api_key' => $token]);
        return back()->with('message', 'Api Key Set successfully');

    }


    public function generate_account(request $request)
    {



            if (Auth::user()->name == null &&  Auth::user()->phone = null) {

                $request->validate([
                    'name' => 'required',
                    'phone' => 'required|max:11|min:11',
                ]);

                User::where('id', Auth::id())->update(['name' => $request->name, 'phone' => $request->phone]);
            }




            $email = Auth::user()->email;
            $get_account = PaymentPoint::where('email', $email)->first() ?? null;

            if ($get_account != null) {
                $data2['account_no'] = $get_account->account_no;
                $data2['bank_name'] = $get_account->bank_name;
                $data2['account_name'] = $get_account->account_name;

                $data2['status'] ="off";
                $data2['transaction'] =Transaction::latest()->where('user_id', Auth::id())->paginate(100);


                return redirect('fund-wallet')->with('message', 'Account created');

            }

            $key = env('PALMPAYKEY');

            $databody = array(
                "email" => $email,
                "account_name" => $request->fullname ?? Auth::user()->name,
                "key" => $key,
            );


            $post_data = json_encode($databody);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://web.sprintpay.online/api/generate-virtual-account',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 20,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $post_data,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Accept: application/json',
                ),
            ));

            $var = curl_exec($curl);


        curl_close($curl);
            $var = json_decode($var);
            $status = $var->status ?? null;
            $error = $var->message ?? null;




        if ($status != false) {


                $pay = new PaymentPoint();
                $pay->account_no = $var->data->account_number;
                $pay->account_name = $var->data->account_name;
                $pay->bank_name = $var->data->bank_name;
                $pay->email = $email;
                $pay->save();




                $data2['account_no'] = $var->data->account_number;
                $data2['bank_name'] =  $var->data->bank_name;
                $data2['account_name'] =$var->data->account_name;
                $data2['status'] ="off";
                $data2['transaction'] =Transaction::latest()->where('user_id', Auth::id())->paginate(100);


              return redirect('fund-wallet')->with('message', 'Account created');


        }

            return back()->with('error', "$error");


    }


    public function index(request $request)
    {
        $data['services'] = get_services();

        $data['get_rate'] = Setting::where('id', 3)->first()->rate;
        $data['margin'] = Setting::where('id', 3)->first()->margin;
        $data['verification'] = Verification::where('user_id', Auth::id())->paginate('10');
        $data['order'] = 0;
        return view('welcome', $data);
    }


    public function check_more_sms(Request $request)
    {
        $request->validate([
            'num' => 'required|string',
        ]);

        $verification = Verification::query()
            ->where('phone', $request->input('num'))
            ->where('user_id', Auth::id())
            ->orderByDesc('id')
            ->first();

        if (! $verification) {
            return response()->json([]);
        }

        $codes = VerificationSms::where('verification_id', $verification->id)->get();

        return response()->json($codes);
    }


    public function h1(request $request)
    {
        $data['topMessage'] = "🎊 Welcome to Acesmsverify!!";
        $data['centerMessage'] = Notification::where('id', 1)->first()->message;

        return view('h1', $data);

    }

    public function home(request $request)
    {
        $data['order'] = 0;
        $data['pend'] = 0;
        $data['product'] = null;

        $data['topMessage'] = "🎊 Welcome to Acesmsverify!!";
        $data['centerMessage'] = Notification::where('id', 1)->first()->message;

        $data['vtuQuickLinks'] = $this->buildVtuQuickLinks();

        return view('home', $data);
    }

    public function verification_index(Request $request)
    {
        $flags = verification_server_flags();
        if (empty($flags['us1'])) {
            if (!empty($flags['us2'])) {
                return redirect('/usa2')->with('topMessage', 'USA Server 1 is no longer available. Use USA Server 2.');
            }
            if (!empty($flags['world'])) {
                return redirect('/world')->with('topMessage', 'USA Server 1 is no longer available. Use World or USA Server 2.');
            }
            return redirect('/home')->with('topMessage', 'Verification service is currently unavailable.');
        }

        $services = get_services();

        $allServices = [];
        if (is_array($services) || $services instanceof \Traversable) {
            foreach ($services as $provider => $items) {
                if (!(is_array($items) || $items instanceof \Traversable)) {
                    continue;
                }
                foreach ($items as $id => $service) {
                    $allServices[] = (object) array_merge((array) $service, ['provider' => (string) $provider]);
                }
            }
        } else {
            $request->session()->flash('topMessage', 'Server 1 services are temporarily unavailable. Please try again shortly.');
        }

        $data['allServices'] = $allServices;
        $data['get_rate'] = Setting::where('id', 1)->first()->rate;
        $data['margin'] = Setting::where('id', 1)->first()->margin;
        $data['verification'] = Verification::latest()->where('user_id', Auth::id())->take(10)->get();
        $data['verificationServers'] = $flags;

        return view('verification-index', $data);
    }

    /**
     * Quick links for VTU (airtime, data, cable, electricity) on dashboard.
     */
    private function buildVtuQuickLinks(): array
    {
        $defs = [
            ['key' => 'airtime', 'label' => 'Airtime', 'route' => 'vas.airtime'],
            ['key' => 'data', 'label' => 'Data', 'route' => 'vas.data'],
            ['key' => 'cable', 'label' => 'Cable TV', 'route' => 'vas.cable'],
            ['key' => 'electricity', 'label' => 'Electricity', 'route' => 'vas.electricity'],
        ];

        $links = [];
        foreach ($defs as $d) {
            $links[] = [
                'key' => $d['key'],
                'label' => $d['label'],
                'url' => route($d['route']),
                'active' => true,
            ];
        }

        return $links;
    }

    public function usaserver2(request $request)
    {
        $flags = verification_server_flags();
        if (empty($flags['us2'])) {
            if (!empty($flags['us1'])) {
                return redirect()->route('verification.index')->with('topMessage', 'USA Server 2 is currently unavailable.');
            }
            if (!empty($flags['world'])) {
                return redirect('/world')->with('topMessage', 'USA Server 2 is currently unavailable.');
            }
            return redirect('/home')->with('topMessage', 'Verification service is currently unavailable.');
        }


        $result = get_services_usa_server_2(); // Or dynamically pass ZIP

        $data['services'] = $result['availableServices'] ?? [];
        $data['zips'] = $result['availableZips'] ?? [];


        $data['get_rate'] = Setting::where('id', 1)->first()->rate;
        $data['margin'] = Setting::where('id', 1)->first()->margin;
        $data['rate'] = $data['margin'] + $data['get_rate'];

        $data['verification'] = Verification::latest()->where('user_id', Auth::id())->take(10)->get();
        $data['order'] = 0;
        $verification = Verification::where('user_id', Auth::id())->get();
        $data['pend'] = 0;
        $data['product'] = null;
        $data['orders'] = Verification::where('user_id', Auth::id())->get();
        $data['verificationServers'] = $flags;


        return view('usaserver2', $data);
    }


    public function pendng_sms(Request $request)
    {

        return view('receive-sms');

    }


    public function updatesec(request $request)
    {

        $ver = Verification::where('id', $request->id)->first();

        if ($ver) {

            if ($ver->status === 1) {
                $secs = Verification::where('id', $request->id)->update(['expires_in' => $request->secs]);

            }
        }


    }


    public function getInitialCountdown(request $request)
    {
        $row = Verification::where('id', $request->id)->first();
        if ($row === null) {
            return response()->json(['seconds' => 0]);
        }
        if ((int) $row->status !== 1) {
            return response()->json(['seconds' => 0]);
        }

        return response()->json([
            'seconds' => (int) $row->expires_in,
        ]);
    }


    public function order_now(Request $request)
    {
        if (empty(verification_server_flags()['us1'])) {
            return response()->json([
                'status' => false,
                'message' => 'USA Server 1 is no longer available. Use USA Server 2 (/usa2).',
            ]);
        }

        $wallet_check = WalletCheck::where('user_id', Auth::id())->first();
        if (!$wallet_check) {
            Auth::logout();
            return redirect('login');
        }


        $service = (string) $request->provider;
        $service_name = (string) $request->service;
        $area_code = $request->areaCode;
        $carrier = $request->carrier;

        $quote = VerificationPricingService::usaServer1Quote($service, $area_code, $carrier);
        if ($quote === null) {
            $data['status'] = false;
            $data['message'] = 'Invalid service or pricing unavailable. Please refresh and try again.';

            return $data;
        }

        $clientPrice = (float) $request->price;
        if (abs($clientPrice - $quote['final_ngn']) > 0.05) {
            $data['status'] = false;
            $data['message'] = 'Price has been updated, Please re-order number';

            return $data;
        }

        if (Auth::user()->wallet < $quote['final_ngn']) {
            $data['status'] = false;
            $data['message'] = "Insufficient Funds";

            return $data;
        }

        $order = create_order(
            $service,
            $quote['base_ngn'],
            $quote['api_cost'],
            $service_name,
            $quote['api_cost'],
            $area_code,
            $carrier
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


    public function receive_sms(Request $request)
    {

        $type = Verification::where('user_id', Auth::id())->where('id', $request->phone)->first()->type;
        if ($type == 2) {
            $data['sms_order'] = Verification::where('user_id', Auth::id())->where('id', $request->phone)->first();
            $data['order'] = 1;

            $data['verification'] = Verification::where('user_id', Auth::id())->paginate(10);

            return view('receivesmsworld', $data);

        }
        $data['sms_order'] = Verification::where('user_id', Auth::id())->where('id', $request->phone)->first();
        $data['order'] = 1;

        $data['verification'] = Verification::where('user_id', Auth::id())->paginate(10);

        return view('receivesms', $data);

    }


    public function admin_cancle_sms(Request $request)

    {

        $order = Verification::where('id', $request->id)->first() ?? null;
        $user_id = $order->user_id;


        if ($order == null) {
            return redirect()->back()->with('topMessage', '❌ Order not found!');
        }

        if ($order->status == 2) {
            return redirect()->back()->with('topMessage', '✅ Order Completed!');
        }

        if ($order->status == 1 && $order->type == 1) {

            $orderID = $order->order_id;
            $can_order = cancel_order($orderID);

            if ($request->delete == 1) {

                if ($order->status == 1) {

                    $amount = number_format($order->cost, 2);
                    Verification::where('id', $request->id)->delete();
                    User::where('id', $user_id)->increment('wallet', $order->cost);
                    $message = Auth::user()->email . " just been refunded | $order->cost | by admin";
                    send_notification($message);
                    send_notification2($message);


                    return redirect()->back()->with('topMessage', "✅ Order has been canceled, NGN$amount has been refunded");

                }


            }


            if ($can_order == 0) {

                return redirect()->back()->with('topMessage', '❌ Order has been removed');

            }


            if ($can_order == 1) {
                $amount = number_format($order->cost, 2);
                Verification::where('id', $request->id)->delete();
                User::where('id', $user_id)->increment('wallet', $order->cost);

                $message = Auth::user()->email . " just been refunded | $order->cost | by admin";
                send_notification($message);
                send_notification2($message);

                return redirect()->back()->with('topMessage', "✅ Order has been canceled, NGN$amount has been refunded");

            }


            if ($can_order == 3) {
                $order = Verification::where('id', $request->id)->first() ?? null;

                if ($order->status != 1 || $order == null) {
                    return back()->with('error', "Please try again later");
                }
                Verification::where('id', $request->id)->delete();
                $amount = number_format($order->cost, 2);
                User::where('id', $user_id)->increment('wallet', $order->cost);

                $message = Auth::user()->email . " just been refunded | $order->cost | by admin";
                send_notification($message);
                send_notification2($message);

                return redirect()->back()->with('topMessage', "✅ Order has been canceled, NGN$amount has been refunded");

            }
        }

        if ($order->status == 1 && $order->type == 2) {


            $orderID = $order->order_id;

            $can_order = cancel_world_order($orderID);

            if ($request->delete == 1) {


                if ($order->status == 1) {

                    $amount = number_format($order->cost, 2);
                    Verification::where('id', $request->id)->delete();

                    User::where('id', $user_id)->increment('wallet', $order->cost);
                    User::where('id', $user_id)->increment('wallet', $order->cost);


                    $message = Auth::user()->email . " just been refunded | $order->cost | by admin";
                    send_notification($message);
                    send_notification2($message);

                    return redirect()->back()->with('topMessage', "✅ Order has been canceled, NGN$amount has been refunded");


                }


            }


            if ($can_order == 0) {

                return redirect()->back()->with('topMessage', "❌ Your order cannot be cancelled yet, please try again later");

            }


            if ($can_order == 1) {
                $amount = number_format($order->cost, 2);
                Verification::where('id', $request->id)->delete();
                User::where('id', $user_id)->increment('wallet', $order->cost);
                $message = Auth::user()->email . " just been refunded | $order->cost | by admin";
                send_notification($message);
                send_notification2($message);

                return back()->with('message', "Order has been canceled, NGN$amount has been refunded");
            }


            if ($can_order == 3) {
                $order = Verification::where('id', $request->id)->first() ?? null;
                if ($order->status != 1 || $order == null) {
                    return back()->with('error', "Please try again later");
                }
                Verification::where('id', $request->id)->delete();

                $amount = number_format($order->cost, 2);
                User::where('id', $user_id)->increment('wallet', $order->cost);

                $message = Auth::user()->email . " just been refunded | $order->cost | by admin";
                send_notification($message);
                send_notification2($message);
                return redirect()->back()->with('topMessage', "✅ Order has been canceled, NGN$amount has been refunded");
            }
        }
    }


    public function fund_wallet(Request $request)
    {
        $data2['user'] = Auth::id() ?? null;
        $data2['pay'] = PaymentMethod::all();
        $data2['status'] = AccountDetail::where('id', 1)->first()->status;
        $data2['transaction'] = Transaction::query()
            ->orderByRaw('updated_at DESC')
            ->where('user_id', Auth::id())
            ->paginate(10);


        $email = Auth::user()->email;
        $get_account = PaymentPoint::where('email', $email)->first() ?? null;

        if ($get_account != null) {
            $data2['account_no'] = $get_account->account_no;
            $data2['bank_name'] = $get_account->bank_name;
            $data2['account_name'] = $get_account->account_name;

        }


        return view('fund-wallet', $data2);
    }


    public function fund_now(Request $request)
    {

        $request->validate([
            'amount' => 'required|numeric|gt:0',
        ]);


            Transaction::where('user_id', Auth::id())->where('status', 1)->delete() ?? null;


        if ($request->type == 1) {

            if ($request->amount < 1000) {
                return redirect()->back()->with('topMessage', "❌ You can not fund less than NGN 1000");

            }


            if ($request->amount > 100000) {

                return redirect()->back()->with('topMessage', "❌ You can not fund more than NGN 100,000");

            }


            $key = env('WEBKEY');
            $ref = "VERF" . random_int(000, 999) . date('ymdhis');
            $email = Auth::user()->email;

            $url = "https://web.sprintpay.online/pay?amount=$request->amount&key=$key&ref=$ref&email=$email";

            $get_balance = User::where('id', Auth::id())->first()->wallet;

            $data = new Transaction();
            $data->user_id = Auth::id();
            $data->amount = $request->amount;
            $data->balance = 0;
            $data->old_balance = $get_balance;
            $data->ref_id = $ref;
            $data->type = 2;
            $data->status = 1; //initiate
            $data->save();


            $message = Auth::user()->email . "| wants to fund |  NGN " . number_format($request->amount) . " | with ref | $ref |  on ACEVERIFY";
            send_notification2($message);


            return Redirect::to($url);
        }


        if ($request->type == 2) {

            if ($request->amount < 100) {
                return back()->with('error', 'You can not fund less than NGN 100');
            }


            if ($request->amount > 100000) {
                return back()->with('error', 'You can not fund more than NGN 100,000');
            }


            $ref = "VERFM" . random_int(000, 999) . date('ymdhis');
            $email = Auth::user()->email;

            $get_balance = User::where('id', Auth::id())->first()->wallet;


            $data = new Transaction();
            $data->user_id = Auth::id();
            $data->amount = $request->amount;
            $data->balance = 0;
            $data->old_balance = $get_balance;
            $data->ref_id = $ref;
            $data->type = 2; //manual funding
            $data->status = 1; //initiate
            $data->save();


            $message = Auth::user()->email . "| wants to fund Manually |  NGN " . number_format($request->amount) . " | with ref | $ref |  on ACEVERIFY";
            send_notification2($message);


            $data['account_details'] = AccountDetail::where('id', 1)->first();
            $data['amount'] = $request->amount;

            return view('manual-fund', $data);
        }


    }


    public function fund_manual_now(Request $request)
    {


        if ($request->receipt == null) {
            return back()->with('error', "Payment receipt is required");
        }


        $file = $request->file('receipt');
        $receipt_fileName = date("ymis") . $file->getClientOriginalName();
        $destinationPath = public_path() . 'upload/receipt';
        $request->receipt->move(public_path('upload/receipt'), $receipt_fileName);


        $pay = new ManualPayment();
        $pay->receipt = $receipt_fileName;
        $pay->user_id = Auth::id();
        $pay->amount = $request->amount;
        $pay->save();


        $message = Auth::user()->email . "| submitted payment receipt |  NGN " . number_format($request->amount) . " | on ACEVERIFY";
        send_notification2($message);

        return view('confirm-pay');
    }


    public function confirm_pay(Request $request)
    {
        return view('confirm-pay');
    }


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {


            if (Auth::user()->status == 9) {
                Auth::logout();
                return back()->with('error', "You have been Temporarily ban, please contact admin");

            }


            $get_user_balance = Auth::user()->wallet;
            $ck = WalletCheck::where('user_id', Auth::user())->first();
            //$total_funded = Transaction::where(['user_id' => Auth::id(), 'status' => 2])->where('type', 2)->sum('amount');
            if (!$ck) {
                $wal = new WalletCheck();
                $wal->user_id = Auth::id();
                $wal->total_funded = Auth::user()->wallet;
                $wal->wallet_amount = Auth::user()->wallet;
                $wal->save();
            }


            return redirect('/home');

        }

        return back()->with('error', "Email or Password Incorrect");
    }


    public function ban(Request $request)
    {
        return view('ban');
    }

    public function ban_user(Request $request)
    {
        User::where('id', $request->id)->update(['status' => 9]);
        return back()->with('message', "Account Banned Successfully");
    }


    public function destroy_user($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        Transaction::where('user_id', $id)->delete();
        Verification::where('user_id', $id)->delete();

        return redirect('users')->with('success', 'User deleted successfully!');
    }


    public function destroy(Request $request)
    {
        $user = Auth::user();
        $user->session_id = null; // Clear session ID
        $user->save();

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public
    function verify_account_now(request $request)
    {
        $storedExpiryTimestamp = $request->code;;
        if (time() >= $storedExpiryTimestamp) {

            $user = Auth::id() ?? null;
            $email = $request->email;
            return view('expired', compact('user', 'email'));

        } else {

            User::where('email', $request->email)->update(['verify' => 2]);
            return view('Auth.verify-account-now');
        }

    }

    public
    function verify_account_now_page(request $request)
    {
        return view('verify-account-now-success');

    }


    public
    function verify_password(request $request)
    {

        $code = User::where('email', $request->email)->first()->code;


        $storedExpiryTimestamp = $request->code;;

        if (time() >= $storedExpiryTimestamp) {

            $user = Auth::id() ?? null;
            $email = $request->email;
            return view('expired', compact('user', 'email'));
        } else {

            $user = Auth::id() ?? null;
            $email = $request->email;

            return view('verify-password', compact('user', 'email'));
        }
    }


    public
    function register_index(Request $request)
    {
        return view('Auth.register');
    }


    public
    function login_index(Request $request)
    {
        return view('Auth.login');
    }


    public
    function forget_password(Request $request)
    {
        return view('Auth.forgot-password');
    }


    public
    function register(Request $request)
    {
        // Validate the user input
        $validatedData = $request->validate([
            'username' => 'required||string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:4|confirmed',
        ]);

        // Create a new user
        $user = User::create([
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);


        auth()->login($user);


        $get_user_balance = Auth::user()->wallet;
        $ck = WalletCheck::where('user_id', Auth::user())->first();
        $total_funded = Transaction::where(['user_id' => Auth::id(), 'status' => 2])->where('type', 2)->sum('amount');
        if (!$ck) {
            $wal = new WalletCheck();
            $wal->user_id = Auth::id();
            $wal->total_funded = $total_funded;
            $wal->wallet_amount = 0;
            $wal->save();
        }


        // Redirect the user to a protected route or dashboard
        return redirect('home');
    }


    public
    function profile(request $request)
    {


        $user = Auth::id();
        $orders = SoldLog::latest()->where('user_id', Auth::id())->paginate(5);


        return view('profile', compact('user', 'orders'));
    }


    public
    function logout(Request $request)
    {

        Auth::logout();
        return redirect('/');
    }


    public
    function change_password(request $request)
    {

        $user = Auth::id();


        return view('change-password', compact('user'));
    }


    public
    function faq(request $request)
    {
        $user = Auth::id();
        return view('faq', compact('user'));
    }

    public
    function terms(request $request)
    {
        $user = Auth::id();
        return view('terms', compact('user'));
    }

    public
    function rules(request $request)
    {
        $user = Auth::id();
        return view('rules', compact('user'));
    }


    public
    function update_password_now(request $request)
    {
        // Validate the user input
        $validatedData = $request->validate([
            'password' => 'required|string|min:4|confirmed',
        ]);

        User::where('id', Auth::id())->update([
            'password' => Hash::make($validatedData['password']),
        ]);

        // Redirect the user to a protected route or dashboard
        return back()->with('message', 'Password Changed Successfully');
    }


    public
    function reset_password(request $request)
    {

        $email = $request->email;
        $expiryTimestamp = time() + 24 * 60 * 60; // 24 hours in seconds
        $url = url('') . "/verify-password?code=$expiryTimestamp&email=$request->email";

        $ck = User::where('email', $request->email)->first()->email ?? null;
        $username = User::where('email', $request->email)->first()->username ?? null;


        if ($ck == $request->email) {

            User::where('email', $email)->update([
                'code' => $expiryTimestamp
            ]);

            $data = array(
                'fromsender' => 'noreply@acesmsverify.com', 'ACEVERIFY',
                'subject' => "Reset Password",
                'toreceiver' => $email,
                'url' => $url,
                'user' => $username,
            );


            Mail::send('reset-password-mail', ["data1" => $data], function ($message) use ($data) {
                $message->from($data['fromsender']);
                $message->to($data['toreceiver']);
                $message->subject($data['subject']);
            });


            return redirect('/forgot-password')->with('message', "A reset password mail has been sent to $request->email, if not inside inbox check your spam folder");
        } else {
            return back()->with('error', 'Email can not be found on our system');
        }
    }


    public
    function expired(request $request)
    {
        $user = Auth::id() ?? null;
        return view('expired', compact('user'));
    }

    public
    function reset_password_now(request $request)
    {

        $validatedData = $request->validate([
            'password' => 'required|string|min:4|confirmed',
        ]);


        $password = Hash::make($validatedData['password']);

        User::where('email', $request->email)->update([

            'password' => $password

        ]);

        return redirect('/login')->with('message', 'Password reset successful, Please login to continue');
    }


    public
    function get_smscode(request $request)
    {
        $request->validate([
            'num' => 'required|string',
        ]);

        $q = Verification::query()
            ->where('phone', $request->input('num'))
            ->orderByDesc('id');
        if (Auth::check()) {
            $q->where('user_id', Auth::id());
        }
        $ver = $q->first();

        if (! $ver) {
            return response()->json([
                'message' => 'waiting for sms',
                'status' => null,
            ]);
        }

        if ($ver->order_id !== null && $ver->order_id !== '') {
            check_sms($ver->order_id);
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


    public
    function webhook(request $request)
    {

        $activationId = $request->activationId;
        $messageId = $request->messageId;
        $service = $request->service;
        $text = $request->text;
        $code = $request->code;
        $country = $request->country;
        $receivedAt = $request->receivedAt;
        $orders = Verification::where('order_id', $activationId)->update(['sms' => $code, 'status' => 2]);


        $message = json_encode($request->all());
        send_notification($message);


    }


    /**
     * HeroSMS (world) inbound webhook. Configure in Hero dashboard to POST JSON, e.g.:
     * { "activationId", "service", "text", "code", "country", "receivedAt" }
     * Legacy aliases: orderid, sms (same as code).
     */
    public function world_webhook(Request $request)
    {
        $activationId = $request->input('activationId') ?? $request->input('orderid');
        if ($activationId === null || $activationId === '') {
            return response()->json(['ok' => false, 'message' => 'activationId (or orderid) required'], 400);
        }

        $activationId = (string) $activationId;
        $code = $request->input('code') ?? $request->input('sms');
        $text = $request->input('text');
        $service = $request->input('service');
        $country = $request->input('country');

        $ver = Verification::where('order_id', $activationId)->first();
        if (! $ver) {
            Log::warning('world_webhook: activation not found', ['activation_id' => $activationId]);

            return response()->json(['ok' => false, 'message' => 'activation not found'], 404);
        }

        $smsCode = $code !== null && (string) $code !== '' ? (string) $code : '';
        $fullSms = ($text !== null && (string) $text !== '') ? (string) $text : $smsCode;

        Verification::where('id', $ver->id)->update([
            'sms' => $smsCode !== '' ? $smsCode : $ver->sms,
            'full_sms' => $fullSms,
            'status' => 2,
        ]);

        try {
            $row = new VerificationSms();
            $row->verification_id = $ver->id;
            $row->sms = $fullSms;
            $row->save();
        } catch (\Throwable $e) {
            Log::warning('world_webhook VerificationSms save failed', ['e' => $e->getMessage()]);
        }

        $ver = Verification::find($ver->id);
        $get_webhook_url = $ver ? User::where('id', $ver->user_id)->value('webhook_url') : null;

        if ($get_webhook_url && $ver) {
            try {
                $body = [
                    'phone' => $ver->phone,
                    'code' => $ver->sms,
                    'service' => $service ?? $ver->service,
                    'order_id' => $ver->id,
                    'full_sms' => $ver->full_sms ?? $fullSms,
                    'country' => $country ?? $ver->country,
                ];

                $response = Http::withBody(json_encode($body), 'application/json')->post($get_webhook_url);

                if ($response->status() === 200) {
                    $data = $response->json();
                    $returnedCode = is_array($data) ? ($data['code'] ?? null) : null;
                    $fullContent = $response->body();

                    WebhookResponse::create([
                        'order_id' => $ver->id,
                        'response_code' => $returnedCode,
                        'response_body' => $fullContent,
                    ]);
                } else {
                    $json = $response->json();
                    $errCode = is_array($json) ? ($json['code'] ?? null) : null;

                    WebhookResponse::create([
                        'order_id' => $ver->id,
                        'response_code' => $errCode,
                        'response_body' => $response->body(),
                        'url' => $get_webhook_url,
                    ]);

                    Log::error("world_webhook user callback failed: HTTP {$response->status()}", [
                        'body' => $response->body(),
                    ]);
                }
            } catch (\Throwable $th) {
                Log::error('world_webhook user callback exception', ['e' => $th->getMessage()]);
            }
        }

        return response()->json(['ok' => true]);
    }


    public
    function orders(request $request)
    {
        $verification = Verification::latest()->where('user_id', Auth::id())->get() ?? null;
        return view('orders', compact('verification'));
    }


    public
    function about_us(request $request)
    {

        return view('about-us');
    }


    public
    function policy(request $request)
    {

        return view('policy');
    }


    public function delete_order(Request $request)
    {
        DB::beginTransaction();

        try {
            $order = Verification::where('id', $request->id)->lockForUpdate()->first();

            if (!$order) {
                DB::rollBack();
                return redirect()->back()->with('topMessage', 'Order Not found');
            }

            if ($order->status != 1) {
                DB::rollBack();
                return redirect()->back()->with('topMessage', 'Order already processed or canceled');
            }


            $can_order = null;
            if ((int) $order->type === 8) {
                $can_order = cancel_world_order((string) $order->order_id, 'smspool');
                if ($can_order !== 1) {
                    $can_order = cancel_world_order((string) $order->order_id, 'herosms');
                }
            } elseif ((int) $order->type === 9) {
                $can_order = cancel_world_order((string) $order->order_id, 'herosms');
            } elseif ((int) $order->type === 10) {
                $can_order = cancel_world_order((string) $order->order_id, 'sv3');
            } elseif ((int) $order->type === 1) {
                $can_order = cancel_order($order->order_id);
            }

            if ($can_order == 5) {
                DB::rollBack();
                return back()->with('error', "Sms found");
            }

            if ($can_order !== 1) {
                DB::rollBack();
                return redirect()->back()->with('topMessage', 'Order cannot be canceled at this time');
            }

            $order->status = 99;
            $order->save();

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
            $trx->ref_id = "Order Cancel " . $order->id;
            $trx->user_id = $order->user_id;
            $trx->status = 2;
            $trx->amount = $order->cost;
            $trx->balance = $new_balance;
            $trx->old_balance = $old_balance;
            $trx->type = 3;
            $trx->save();

            $order->delete();

            DB::commit();

            return back()->with('topMessage', " ✅ Order canceled, NGN{$order->cost} refunded");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Error: " . $e->getMessage());
        }
    }



//    public function delete_w_order(request $request)
//    {
//
//        $order = Verification::where('id', $request->id)->first() ?? null;
//
//
//        if ($order == null) {
//            return redirect('home')->with('error', 'Order not found');
//        }
//
//        if ($order->status == 2) {
//            Verification::where('id', $request->id)->delete();
//            return back()->with('message', "Order has been successfully deleted");
//        }
//
//        if ($order->status == 1) {
//
//            $orderID = $order->order_id;
//            $can_order = cancel_order($orderID);
//
//            if ($can_order == 0) {
//                return back()->with('error', "Please wait and try again later");
//            }
//
//
//            if ($can_order == 1) {
//                $amount = number_format($order->cost, 2);
//                User::where('id', Auth::id())->increment('wallet', $order->cost);
//                Verification::where('id', $request->id)->delete();
//                return back()->with('message', "Order has been cancled, NGN$amount has been refunded");
//            }
//
//
//            if ($can_order == 3) {
//                $amount = number_format($order->cost, 2);
//                User::where('id', Auth::id())->increment('wallet', $order->cost);
//                Verification::where('id', $request->id)->delete();
//                return back()->with('message', "Order has been cancled, NGN$amount has been refunded");
//            }
//        }
//
//        if ($order->status == 1 && $order->type == 2 ) {
//
//
//
//            $orderID = $order->order_id;
//            $can_order = cancel_world_order($orderID);
//
//            if ($can_order == 0) {
//                return back()->with('error', "Please wait and try again later");
//            }
//
//
//            if ($can_order == 1) {
//                $amount = number_format($order->cost, 2);
//                User::where('id', Auth::id())->increment('wallet', $order->cost);
//                Verification::where('id', $request->id)->delete();
//                return back()->with('message', "Order has been cancled, NGN$amount has been refunded");
//            }
//
//
//            if ($can_order == 3) {
//                $amount = number_format($order->cost, 2);
//                User::where('id', Auth::id())->increment('wallet', $order->cost);
//                Verification::where('id', $request->id)->delete();
//                return back()->with('message', "Order has been cancled, NGN$amount has been refunded");
//            }
//        }
//    }


    public
    function e_check(request $request)
    {

        $get_user = User::where('email', $request->email)->first() ?? null;

        if ($get_user == null) {

            return response()->json([
                'status' => false,
                'message' => 'No user found, please check email and try again',
            ]);
        }


        return response()->json([
            'status' => true,
            'user' => $get_user->username,
        ]);
    }


    public function e_fund(Request $request)
    {
        $configuredSecret = config('services.sprintpay.webhook_secret');
        if (is_string($configuredSecret) && $configuredSecret !== '') {
            if (!SprintPayWebhookAuth::tokenValid($request, $configuredSecret)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }
        } else {
            $ip = $request->ip();
            if ($ip != "209.74.80.245") {
                return response()->json([
                    'status' => false,
                    'message' => "Wrong IP | $ip"
                ]);
            }
        }

        $amount = (float) $request->input('amount', 0);
        $result = WalletFundingService::creditFromExternalPayment(
            (string) $request->input('email', ''),
            $amount,
            (string) $request->input('order_id', '')
        );

        if (!$result['ok']) {
            return response()->json([
                'status' => false,
                'message' => $result['message'],
            ], $result['http'] ?? 400);
        }

        $formatted = number_format($amount, 2);

        if (!empty($result['duplicate'])) {
            return response()->json([
                'status' => true,
                'message' => 'Payment already applied to wallet',
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => "NGN {$formatted} has been successfully added to your wallet",
        ]);
    }


    public
    function user(request $request)
    {


    }


    public
    function verify_username(request $request)
    {

        $get_user = User::where('email', $request->email)->first() ?? null;

        if ($get_user == null) {

            return response()->json([
                'username' => "Not Found, Pleas try again"
            ]);

        }

        return response()->json([
            'username' => $get_user->username
        ]);


    }


    public function unban_users(request $request)
    {


        User::where('id', $request->id)->update(['status' => 2]);


        return back()->with('message', 'User Unban');

    }


    public function ban_users(request $request)
    {
        User::where('id', $request->id)->update(['status' => 9]);
        return back()->with('message', 'User Banned');
    }


    public function verify_email()
    {

        return view('verify-email');

    }

    public function updateFunds(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:add,remove',
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            /** @var \App\Models\User $user */
            $user = User::where('id', $id)->lockForUpdate()->firstOrFail();

            $amount = (float) $request->amount;
            $oldBalance = (float) $user->wallet;

            if ($request->action === 'add') {
                $newBalance = $oldBalance + $amount;
                $user->wallet = $newBalance;
                $user->save();

                $trx = new Transaction();
                $trx->ref_id = "ADMINFUNDING" . random_int(000000, 999999);
                $trx->user_id = $user->id;
                $trx->status = 2;
                $trx->amount = $amount;
                $trx->balance = $newBalance;
                $trx->old_balance = $oldBalance;
                $trx->type = 2;
                $trx->save();

                WalletCheck::where('user_id', $user->id)->increment('total_funded', $amount);
                WalletCheck::where('user_id', $user->id)->increment('wallet_amount', $amount);
            } elseif ($request->action === 'remove') {
                if ($oldBalance < $amount) {
                    DB::rollBack();

                    return back()->with('error', 'Insufficient balance to remove.');
                }

                $newBalance = $oldBalance - $amount;
                $user->wallet = $newBalance;
                $user->save();

                $trx = new Transaction();
                $trx->ref_id = "ADMINREMOVAL" . random_int(000000, 999999);
                $trx->user_id = $user->id;
                $trx->status = 2;
                $trx->amount = $amount;
                $trx->balance = $newBalance;
                $trx->old_balance = $oldBalance;
                $trx->type = 1;
                $trx->save();

                WalletCheck::where('user_id', $user->id)->decrement('wallet_amount', $amount);
            }

            DB::commit();

            return back()->with('message', 'Funds updated successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Could not update funds: ' . $e->getMessage());
        }
    }


}

