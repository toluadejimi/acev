<?php

namespace App\Http\Controllers;

use App\Models\AccountDetail;
use App\Models\Deposit;
use App\Models\ManualPayment;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\SoldLog;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Verification;
use App\Models\WalletCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use function Sodium\randombytes_random16;


class HomeController extends Controller
{
    public function index(request $request)
    {
        $data['services'] = get_services();


        $data['get_rate'] = Setting::where('id', 1)->first()->rate;
        $data['margin'] = Setting::where('id', 1)->first()->margin;
        $data['verification'] = Verification::where('user_id', Auth::id())->paginate('10');
        $data['order'] = 0;
        return view('welcome', $data);
    }


    public function home(request $request)
    {

        $data['services'] = get_services();
        $data['get_rate'] = Setting::where('id', 1)->first()->rate;
        $data['margin'] = Setting::where('id', 1)->first()->margin;
        $data['verification'] = Verification::latest()->where('user_id', Auth::id())->take(10)->get();
        $data['order'] = 0;
        $verification = Verification::where('user_id', Auth::id())->get();
        $data['pend'] = 0;
        $data['product'] = null;
        $data['orders'] = Verification::where('user_id', Auth::id())->get();



        return view('home', $data);
    }


    public function pendng_sms(Request $request)
    {

        return view('receive-sms');

    }


    public function updatesec(request $request)
    {

        $ver = Verification::where('id', $request->id)->first()->status;

        if ($ver == 1) {
            $secs = Verification::where('id', $request->id)->update(['expires_in' => $request->secs]);
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
        if(!$wallet_check){
            Auth::logout();
            return redirect('login');
        }




        if($request->price != $request->price2 && $request->price3 != $request->price4 ){

            return back()->with('error', "something went wrong");

        }


        if($request->price < 0 || $request->price == 0){
            return back()->with('error', "something went wrong");
        }

        if($request->price < 500 ){
            return back()->with('error', "something went wrong");
        }


        if (Auth::user()->wallet < 0) {
            return back()->with('error', "Insufficient Funds");
        }

        if (Auth::user()->wallet < $request->price) {
            return back()->with('error', "Insufficient Funds");
        }

        if (Auth::user()->wallet < $request->price) {
            return back()->with('error', "Insufficient Funds");
        }

        $data['get_rate'] = Setting::where('id', 1)->first()->rate;
        $data['margin'] = Setting::where('id', 1)->first()->margin;


        $service = $request->service;

        $gcost = get_d_price($service);

        $costs = ($data['get_rate'] * $gcost) + $data['margin'];
        if (Auth::user()->wallet < $costs) {
            return back()->with('error', "Insufficient Funds");
        }


        $service = $request->service;
        $price = $request->price;
        $cost = $request->cost;
        $service_name = $request->name;

        $order = create_order($service, $price, $cost, $service_name, $costs);
        if ($order == 8) {
            return back()->with('error', "Insufficient Funds");
        }

        if ($order == 7) {
            Auth::logout();
            return redirect('login')->with('error', "Please Contact admin");
        }

        if ($order == 8) {
            return back()->with('error', "Insufficient Funds");
        }

        if ($order == 8) {
            return back()->with('error', "Insufficient Funds");
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
            return redirect('home')->with('error', 'Number Currently out of stock, Please check back later');
        }


        if ($order == 1) {
            return redirect('orders');
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
            return back()->with('error', 'Order not found');
        }

        if ($order->status == 2) {
            return back()->with('message', "Order Completed");
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
                    return back()->with('message', "Order has been canceled, NGN$amount has been refunded");
                }


            }


            if ($can_order == 0) {
                return back()->with('error', "Order has been removed");
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

                return redirect()->with('message', "Order has been canceled, NGN$amount has been refunded");
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

                    return back()->with('message', "Order has been canceled, NGN$amount has been refunded");


                }


            }


            if ($can_order == 0) {
                return back()->with('message', "Your order cannot be cancelled yet, please try again later.");
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
                return back()->with('message', "Order has been canceled, NGN$amount has been refunded");
            }
        }
    }


    public function fund_wallet(Request $request)
    {
        $user = Auth::id() ?? null;
        $pay = PaymentMethod::all();
        $status = AccountDetail::where('id', 1)->first()->status;
        $transaction = Transaction::query()
            ->orderByRaw('updated_at DESC')
            ->where('user_id', Auth::id())
            ->paginate(10);

        return view('fund-wallet', compact('user', 'pay', 'status', 'transaction'));
    }


    public function fund_now(Request $request)
    {

        $request->validate([
            'amount' => 'required|numeric|gt:0',
        ]);


            Transaction::where('user_id', Auth::id())->where('status', 1)->delete() ?? null;


        if ($request->type == 1) {

            if ($request->amount < 1000) {
                return back()->with('error', 'You can not fund less than NGN 1000');
            }


            if ($request->amount > 100000) {
                return back()->with('error', 'You can not fund more than NGN 100,000');
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
            if(!$ck){
                $wal = new WalletCheck();
                $wal->user_id = Auth::id();
                $wal->total_funded = Auth::user()->wallet;
                $wal->wallet_amount = Auth::user()->wallet;
                $wal->save();
            }




            return redirect('us');

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
        if(!$ck){
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
        check_sms($order_id);


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

        try{

            $order = Verification::where('order_id', $activationId)->first() ?? null;
            $user_id = Verification::where('order_id', $activationId)->first()->user_id ?? null;
            User::where('id', $user_id)->decrement('hold_wallet', $order->cost);

        }catch (\Exception $e) {
            $message = $e->getMessage();
            send_notification($message);
            send_notification2($message);
        }


        $message = json_encode($request->all());
        send_notification($message);


    }


    public
    function diasy_webhook(request $request)
    {


        $message = json_encode($request->all());
        send_notification($message);
        send_notification2($message);


        $activationId = $request->activationId;
        $messageId = $request->messageId;
        $service = $request->service;
        $text = $request->text;
        $code = $request->sms;
        $country = $request->country;
        $receivedAt = $request->receivedAt;

        $orders = Verification::where('order_id', $activationId)->update(['sms' => $code, 'status' => 2]);


        try{

            $order = Verification::where('order_id', $activationId)->first() ?? null;
            $user_id = Verification::where('order_id', $activationId)->first()->user_id ?? null;
            User::where('id', $user_id)->decrement('hold_wallet', $order->cost);

        }catch (\Exception $e) {
            $message = $e->getMessage();
            send_notification($message);
            send_notification2($message);
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


    public
    function delete_order(request $request)
    {

        $order = Verification::where('id', $request->id)->first() ?? null;

        if($order == null){

            return back()->with('error', "Order not found");

        }

        if ($order->status == 1 && $order->type == 2) {

            $orderID = $order->order_id;
            $can_order = cancel_world_order($orderID);


            if ($can_order == 0) {
                return back()->with('error', "Please wait and try again later");
            }




            if ($can_order == 1) {

                sleep(5);

                $amount = number_format($order->cost, 2);
                Verification::where('id', $request->id)->delete();

                User::where('id', Auth::id())->increment('wallet', $order->cost);
                WalletCheck::where('user_id', Auth::id())->increment('wallet_amount', $order->cost);

                $get_balance = User::where('id', Auth::id())->first()->wallet;
                $balance = $get_balance + $order->cost;


                $trx = new Transaction();
                $trx->ref_id = "Order Cancel ".$request->id;
                $trx->user_id = Auth::id();
                $trx->status = 2;
                $trx->amount = $order->cost;
                $trx->balance = $balance;
                $trx->old_balance = $get_balance;
                $trx->type = 2;
                $trx->save();


                $email = User::where('id', $order->user_id)->first()->email ?? null;
                $balance = User::where('id', $order->user_id)->first()->wallet ?? null;




                $bb = number_format($balance, 2);
                $message = $email . "| just canceled | $order->service | type is $order->type | $amount is refunded | Balance is  $bb";
                send_notification($message);
                send_notification2($message);

                return back()->with('message', "Order has been canceled, NGN$amount has been refunded");
            }


            if ($can_order == 3) {
                $amount = number_format($order->cost, 2);
                Verification::where('id', $request->id)->delete();
                return back()->with('message', "Order has been canceled");
            }
        }

        if ($order->status == 1 && $order->type == 1) {



            $order = Verification::where('id', $request->id)->first() ?? null;

            if ($order == null) {
                return redirect('home')->with('error', 'Order not found');
            }

            if ($order->status == 2) {
                Verification::where('id', $request->id)->delete();
                return back()->with('message', "Order has been successfully deleted");
            }

            if ($order->status == 1) {

                $orderID = $order->order_id;
                $corder = cancel_order($orderID);



                if ($corder == 0) {
                    return back()->with('error', "Please wait and try again later");
                }



                if ($corder == 1) {

                    sleep(5);

                    $amount = number_format($order->cost, 2);

                    Verification::where('id', $request->id)->delete();
                    User::where('id', Auth::id())->increment('wallet', $order->cost);

                    WalletCheck::where('user_id', Auth::id())->increment('wallet_amount', $order->cost);

                    $get_balance = User::where('id', Auth::id())->first()->wallet;
                    $balance = $get_balance + $order->cost;

                    $trx = new Transaction();
                    $trx->ref_id = "Order Cancel ".$request->id;
                    $trx->user_id = Auth::id();
                    $trx->status = 2;
                    $trx->amount = $order->cost;
                    $trx->balance = $balance;
                    $trx->old_balance = $get_balance;
                    $trx->type = 2;
                    $trx->save();



                    $email = User::where('id', $order->user_id)->first()->email ?? null;
                    $balance = User::where('id', $order->user_id)->first()->wallet ?? null;
                    $bb = number_format($balance, 2);



                    $message = $email . "| just canceled | $order->service | type is $order->type | $amount is refunded | Balance is  $bb";
                    send_notification($message);
                    send_notification2($message);
                    return back()->with('message', "Order has been canceled, NGN$amount has been refunded");
                }


            }

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


    public
    function e_fund(request $request)
    {


        $ipb = env('IPA');
        $ipa = env('IPB');
        $ip = $request->ip();
        //$fund = $request->fund;

        if($ip != "209.74.80.245"){

                return response()->json([
                    'status' => false,
                    'message' =>  "Wrong IP | $ip"
                ]);

        }

            $get_user = User::where('email', $request->email)->first() ?? null;
            if ($get_user == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'No one user found, please check email and try again',
                ]);
            }

            $ip = $request->ip();
            $url = $request->url();
            $message = $request->email . "| just just funded wallet on ace verify | $ip | $request->order_id | NGN" . $request->amount;
            send_notification($message);
            send_notification2($message);


            User::where('email', $request->email)->increment('wallet', $request->amount) ?? null;
            $amount = number_format($request->amount, 2);

            $get_depo = Transaction::where('ref_id', $request->order_id)->first() ?? null;
            if ($get_depo == null) {

                $get_balance = User::where('email', $request->email)->first()->wallet;
                $balance = $get_balance + $request->amount;


                $trx = new Transaction();
                $trx->ref_id = $request->order_id;
                $trx->user_id = $get_user->id;
                $trx->status = 2;
                $trx->amount = $request->amount;
                $trx->balance = $balance;
                $trx->old_balance = $get_balance;
                $trx->type = 2;
                $trx->save();

                WalletCheck::where('user_id', $get_user->id)->increment('total_funded', $request->amount);
                WalletCheck::where('user_id', $get_user->id)->increment('wallet_amount', $request->amount);

                $message = $trx->id . "| $request->order_id |saved";
                send_notification($message);

            } else {


                WalletCheck::where('user_id', $get_user->id)->increment('total_funded', $request->amount);
                WalletCheck::where('user_id', $get_user->id)->increment('wallet_amount', $request->amount);

                $get_balance = User::where('email', $request->email)->first()->wallet;
                $balance = $get_balance + $request->amount;

                Transaction::where('ref_id', $request->order_id)->update(['status' => 2, 'balance' => $balance, 'old_balance' => $get_balance]);




            }


            $message = json_encode($get_depo);
            send_notification($message);


            return response()->json([
                'status' => true,
                'message' => "NGN $amount has been successfully added to your wallet",
            ]);





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

        $total_bought = verification::where('user_id', $request->id)->where('status', 2)->sum('cost');
        $total_funded = Transaction::where('user_id', $request->id)->where('status', 2)->sum('amount');
        $wallet = User::where('id', $request->id)->first()->wallet;


        User::where('id', $request->id)->update(['status' => 0]);



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


}

