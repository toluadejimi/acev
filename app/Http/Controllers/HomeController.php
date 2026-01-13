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

        $data['get_rate'] = Setting::where('id', 1)->first()->rate;
        $data['margin'] = Setting::where('id', 1)->first()->margin;
        $data['verification'] = Verification::where('user_id', Auth::id())->paginate('10');
        $data['order'] = 0;
        return view('welcome', $data);
    }


    public function check_more_sms(Request $request)
    {

        $get_id = Verification::where('phone', $request->num)->first()->id;
        $codes = VerificationSms::where('verification_id', $get_id)->get();
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

        $services = get_services();

        $allServices = [];
        foreach ($services as $provider => $items) {
            foreach ($items as $id => $service) {
                $allServices[] = (object)array_merge((array)$service, ['provider' => $provider]);
            }
        }

        $data['allServices'] = $allServices;

        $data['get_rate'] = Setting::where('id', 1)->first()->rate;
        $data['margin'] = Setting::where('id', 1)->first()->margin;
        $data['verification'] = Verification::latest()->where('user_id', Auth::id())->take(10)->get();
        $data['order'] = 0;
        $verification = Verification::where('user_id', Auth::id())->get();
        $data['pend'] = 0;
        $data['product'] = null;
        $data['orders'] = Verification::where('user_id', Auth::id())->get();

        $data['topMessage'] = "🎊 Welcome to Acesmsverify!!";
        $data['centerMessage'] = Notification::where('id', 1)->first()->message;


        return view('home', $data);
    }

    public function usaserver2(request $request)
    {


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

        $ver = Verification::where('id', $request->id)->first()->status;
        if ($ver == 1) {
            $secs = Verification::where('id', $request->id)->first()->expires_in;
            return response()->json([
                'seconds' => $secs
            ]);
        }


    }


    public function order_now(Request $request)
    {


        $wallet_check = WalletCheck::where('user_id', Auth::id())->first();
        if (!$wallet_check) {
            Auth::logout();
            return redirect('login');
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

        $data2['get_rate'] = Setting::where('id', 1)->first()->rate;
        $data2['margin'] = Setting::where('id', 1)->first()->margin;


        $service = $request->key;

        $gcost = get_d_price($service);


//        $costs = ($data2['get_rate'] * $gcost) + $data2['margin'];
//        if (Auth::user()->wallet < $costs) {
//            $data['status'] = false;
//            $data['message'] =  "Insufficient Funds";
//            return $data;
//        }


        $service = $request->provider;
        $price = $request->price;
        $cost = $request->cost;
        $service_name = $request->service;
        $area_code = $request->areaCode;
        $carrier = $request->carrier;


        $order = create_order($service, $price, $cost, $service_name, $gcost, $area_code, $carrier);

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


            return redirect('h1');

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


        $sms = Verification::where('phone', $request->num)->first()->sms ?? null;
        $order_id = Verification::where('phone', $request->num)->first()->order_id ?? null;


        $ck_order = check_sms($order_id);


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


    public
    function world_webhook(request $request)
    {

        $activationId = $request->orderid;
        $messageId = $request->messageId;
        $service = $request->service;
        $text = $request->text;
        $code = $request->sms;
        $country = $request->country;
        $receivedAt = $request->receivedAt;
        $orders = Verification::where('order_id', $activationId)->update(['sms' => $code, 'status' => 2]);

        $get_user_id = Verification::where('order_id', $activationId)->first()->user_id ?? null;
        $ver = Verification::where('order_id', $activationId)->first() ?? null;
        $get_webhook_url = User::where('id', $get_user_id)->first()->webhook_url ?? null;

        if ($get_webhook_url) {


            try {

                $url = $get_webhook_url;

                $body = [
                    "phone" => $ver->phone,
                    "code" => $code,
                    "service" => $service,
                    "order_id" => $ver->id,
                    "full_sms" => $ver->text,
                    "country" => $ver->country,
                ];


                $response = Http::withBody(json_encode($body), 'application/json')->post($url);

                if ($response->status() === 200) {
                    $data = $response->json();
                    $returnedCode = $data['code'] ?? null;
                    $fullContent = $response->body();

                    WebhookResponse::create([
                        'order_id' => $ver->id,
                        'response_code' => $returnedCode,
                        'response_body' => $fullContent,
                    ]);


                } else {


                    WebhookResponse::create([
                        'order_id' => $ver->id,
                        'response_code' => $response->json()['code'],
                        'response_body' => $response->body(),
                        'url' => $get_webhook_url,
                    ]);

                    Log::error("Webhook failed with status {$response->status()}", [
                        'body' => $response->body()
                    ]);
                }


            } catch (\Exception$th) {
                return $th->getMessage();
            }


        }

        try {

            $order = Verification::where('order_id', $activationId)->first() ?? null;
            $user_id = Verification::where('order_id', $activationId)->first()->user_id ?? null;


        } catch (\Exception $e) {


        }


    }


    public function diasy_webhook(Request $request)
    {
        $message = json_encode($request->all());
        Log::info($message);

        $activationId = $request->activationId;
        $code = $request->code;

        $verification = Verification::where('order_id', $activationId)->first();

        if ($verification) {
            $verification->update(['status' => 2]);
            $sms = new VerificationSms();
            $sms->verification_id = $verification->id;
            $sms->sms = $code;
            $sms->save();

        }
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


            $can_order = $order->type == 8
                ? cancel_world_order($order->order_id)
                : ($order->type == 1
                    ? cancel_order($order->order_id)
                    : null);

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
        $ip = $request->ip();

        if ($ip != "209.74.80.245") {
            return response()->json([
                'status' => false,
                'message' => "Wrong IP | $ip"
            ]);
        }

        $get_user = User::where('email', $request->email)->first();
        if (!$get_user) {
            return response()->json([
                'status' => false,
                'message' => 'No user found, please check email and try again',
            ]);
        }

        $old_balance = $get_user->wallet;
        $new_balance = $old_balance + $request->amount;

        $get_user->increment('wallet', $request->amount);

        $amount = number_format($request->amount, 2);

        $get_depo = Transaction::where('ref_id', $request->order_id)->first();

        if (!$get_depo) {
            $trx = new Transaction();
            $trx->ref_id = $request->order_id;
            $trx->user_id = $get_user->id;
            $trx->status = 2;
            $trx->amount = $request->amount;
            $trx->balance = $new_balance;
            $trx->old_balance = $old_balance;
            $trx->type = 2;
            $trx->save();

            WalletCheck::where('user_id', $get_user->id)->increment('total_funded', $request->amount);
            WalletCheck::where('user_id', $get_user->id)->increment('wallet_amount', $request->amount);

        } else {
            Transaction::where('ref_id', $request->order_id)->update([
                'status' => 2,
                'balance' => $new_balance,
                'old_balance' => $old_balance,
            ]);

            WalletCheck::where('user_id', $get_user->id)->increment('total_funded', $request->amount);
            WalletCheck::where('user_id', $get_user->id)->increment('wallet_amount', $request->amount);
        }

        return response()->json([
            'status' => true,
            'message' => "NGN $amount has been successfully added to your wallet",
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
            'note' => 'nullable|string'
        ]);

        $user = User::findOrFail($id);

        if ($request->action === 'add') {
            $user->wallet += $request->amount;


            $trx = new Transaction();
            $trx->ref_id = "ADMINFUNDING" . random_int(000000, 999999);
            $trx->user_id = $user->id;
            $trx->status = 2;
            $trx->amount = $request->amount;
            $trx->balance = $user->wallet + $request->amount;
            $trx->old_balance = $user->wallet;
            $trx->type = 2;
            $trx->save();

            WalletCheck::where('user_id', $user->id)->increment('total_funded', $request->amount);
            WalletCheck::where('user_id', $user->id)->increment('wallet_amount', $request->amount);


            return back()->with('message', 'Funds updated successfully!');


        } elseif ($request->action === 'remove') {

            if ($user->wallet < $request->amount) {
                return back()->with('error', 'Insufficient balance to remove.');
            }

            $user->wallet -= $request->amount;

            $trx = new Transaction();
            $trx->ref_id = "ADMINREMOVAL" . random_int(000000, 999999);
            $trx->user_id = $user->id;
            $trx->status = 2;
            $trx->amount = $request->amount;
            $trx->balance = $user->wallet - $request->amount;
            $trx->old_balance = $user->wallet;
            $trx->type = 1;
            $trx->save();

            WalletCheck::where('user_id', $user->id)->decrement('wallet_amount', $request->amount);

        }

        $user->save();

        return back()->with('message', 'Funds updated successfully!');
    }


}

