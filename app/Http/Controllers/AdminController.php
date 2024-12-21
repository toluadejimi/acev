<?php

namespace App\Http\Controllers;

use App\Models\AccountDetail;
use App\Models\Item;
use App\Models\MainItem;
use App\Models\ManualPayment;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{


    public function index(request $request)
    {


        return view('admin-login');


    }

    public function admin_login(request $request)
    {


        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {

            $role = User::where('username', $request->username)->first()->role_id;

            $user = Auth::user();
            if ($user->session_id && $user->session_id !== session()->getId()) {
                session()->getHandler()->destroy($user->session_id);
            }
            $user->session_id = session()->getId();
            $user->save();


            if ($role == 5) {

                return redirect('admin-dashboard');


            } else {
                Auth::logout();
                return redirect('/admin')->with('error', "You do not have permission");

            }


        }

        return back()->with('error', "Email or Password Incorrect");


    }


    public function edit_front_product(request $request)
    {


        Item::where('id', $request->id)->first()->update([

            'amount' => $request->amount,
            'title' => $request->title,
            'qty' => $request->qty


        ]);


        return back()->with('message', 'Front Item successfully Updated');

    }

    public function admin_dashboard(request $request)
    {

        $role = User::where('id', Auth::id())->first()->role_id ?? null;
        if ($role != 5) {

            Auth::logout();
            return redirect('/admin')->with('error', "You do not have permission");

        }


        $data['user'] = User::all()->count();
        $data['total_in'] = Transaction::where('type', 2)->where('status', 2)->sum('amount');
        $data['transaction'] = Transaction::latest()->paginate(10);
        $data['total_in_d'] = Transaction::where(['type' => 2, 'status' => 2])->whereday('created_at', Carbon::today())->sum('amount');
        $data['total_out'] = Verification::where('status', 2)->sum('cost');
        $data['total_verified_message'] = Verification::where('status', 2)->count();
        $data['user_wallet'] = User::where('role_id', 2)->sum('wallet');
        $data['usdtongn'] = Setting::where('id', 1)->first()->rate;
        $data['margin'] = Setting::where('id', 1)->first()->margin;
        $data['verification'] = Verification::latest()->paginate(50);

        $users = User::all();
        DB::beginTransaction();

//        try {
//            $users = User::where('wallet', '>', 0)->get();
//
//
//            foreach ($users as $user) {
//                Transaction::create([
//                    'user_id' => $user->id,
//                    'amount' => $user->wallet,
//                    'ref_id' => "VER".date('his'),
//                    'type' => 2,
//                    'status' => 2,
//                ]);
//
//                $user->wallet = 0;
//                $user->save();
//            }
//
//            DB::commit();
//
//            return view('admin-dashboard', $data);
//
//
//        } catch (\Exception $e) {
//            DB::rollBack();
//
//            return response()->json([
//                'status' => 'error',
//                'message' => 'An error occurred: ' . $e->getMessage(),
//            ], 500);
//        }


        try {
            DB::beginTransaction();

            $transactions = Transaction::where('type', 2)->where('status', 2)->get();

            foreach ($transactions as $transaction) {
                $user = User::find($transaction->user_id);

                if ($user) {
                    $user->wallet += $transaction->amount;
                    $user->save();
                    $transaction->delete();
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Wallet balances have been restored.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while restoring wallet balances: ' . $e->getMessage(),
            ], 500);
        }



    }


    public
    function update_rate(request $request)
    {
        Setting::where('id', 1)->update(['rate' => $request->rate]);

        return back()->with('message', "Rate Update Successfully");

    }


    public
    function update_cost(request $request)
    {
        Setting::where('id', 1)->update(['margin' => $request->cost]);

        return back()->with('message', "Cost Update Successfully");

    }


    public
    function index_user(request $request)
    {

        $role = User::where('id', Auth::id())->first()->role_id ?? null;
        if ($role != 5) {

            Auth::logout();
            return redirect('/admin')->with('error', "You do not have permission");

        }

        $user = User::all()->count();
        $users = User::orderBy('wallet', 'desc')->paginate(10);

        return view('user', compact('user', 'users'));


    }


    public
    function ban_user(request $request)
    {

        $role = User::where('id', Auth::id())->first()->role_id ?? null;
        if ($role != 5) {

            Auth::logout();
            return redirect('/admin')->with('error', "You do not have permission");

        }

        $users = User::where('status', 9)->orderBy('wallet', 'desc')->paginate(10);
        $user = User::where('status', 9)->count();


        return view('banned', compact('users', 'user'));


    }


    public
    function remove_user(request $request)
    {

        $role = User::where('id', Auth::id())->first()->role_id ?? null;
        if ($role != 5) {

            Auth::logout();
            return redirect('/admin')->with('error', "You do not have permission");

        }


        User::where('id', $request->id)->delete();
        Verification::where('user_id', $request->id)->delete();
        Transaction::where('user_id', $request->id)->delete();

        return redirect('users')->with('message', 'User deleted successfully');


    }

    public
    function update_user(request $request)
    {

        $role = User::where('id', Auth::id())->first()->role_id ?? null;
        if ($role != 5) {

            Auth::logout();
            return redirect('/admin')->with('error', "You do not have permission");

        }

        if ($request->trade == 'credit') {


            User::where('id', $request->id)->increment('wallet', $request->amount);

            $ref = "MANUAL" . random_int(000000, 999999);
            $data = new Transaction();
            $data->user_id = $request->id;
            $data->amount = $request->amount;
            $data->ref_id = $ref;
            $data->type = 2;
            $data->status = 2; //initiate
            $data->save();

            $email = User::where('id', $request->id)->first()->email;
            $balance = User::where('id', $request->id)->first()->wallet;


            $message = "Wallet has been credited by admin | $email | $request->amount | Bal - $balance |on Ace Verify";
            send_notification($message);
            send_notification2($message);


        } else {

            User::where('id', $request->id)->decrement('wallet', $request->amount);
            $email = User::where('id', $request->id)->first()->email;
            $message = "Wallet has been debited by admin | $email | $request->amount | on Ace Verify";
            send_notification($message);
            send_notification2($message);
            return back()->with('error', 'Wallet Debited Successfully');

        }


        return back()->with('message', 'Wallet Credited Successfully');


    }


    public
    function view_user(request $request)
    {

        $role = User::where('id', Auth::id())->first()->role_id ?? null;

        if ($role != 5) {

            Auth::logout();
            return redirect('/admin')->with('error', "You do not have permission");

        }


        $data['user'] = User::where('id', $request->id)->first();
        $data['transaction'] = Transaction::latest()->where('user_id', $request->id)->paginate(50);
        $data['verification'] = verification::latest()->where('user_id', $request->id)->paginate(50);

        $data['total_funded'] = Transaction::where('user_id', $request->id)->where('status', 2)->sum('amount');
        $data['total_bought'] = verification::where('user_id', $request->id)->where('status', 2)->sum('cost');
        $data['total_balance'] = $data['total_funded'] - $data['total_bought'];

        return view('view-user', $data);

    }


    public
    function delete_main(request $request)
    {

        MainItem::where('id', $request->id)->delete();

        return back()->with('message', "Item Deleted Successfully");


    }


    public
    function manual_payment_view(request $request)
    {


        $role = User::where('id', Auth::id())->first()->role_id ?? null;
        if ($role != 5) {

            Auth::logout();
            return redirect('/admin')->with('error', "You do not have permission");

        }


        $account_status = AccountDetail::where('id', 1)->first()->status;

        $payment = ManualPayment::latest()->paginate(20);
        $acc = AccountDetail::where('id', 1)->first();

        return view('manual-payment', compact('payment', 'account_status', 'acc'));


    }


    public
    function update_acct_name(request $request)
    {


        $acc = AccountDetail::where('id', 1)->update([

            'bank_name' => $request->bank_name,
            'bank_account' => $request->bank_account,
            'account_name' => $request->account_name,
            'status' => $request->status,


        ]);

        return back()->with('message', 'Bank info updated successfully');


    }


    public
    function approve_payment(request $request)
    {


        $pay = ManualPayment::where('id', $request->id)->first()->status ?? null;

        if ($pay == 1) {
            return back()->with('error', 'Transaction already approved');
        }


        ManualPayment::where('id', $request->id)->update(['status' => 1]);

        User::where('id', $request->user_id)->increment('wallet', $request->amount);

        $email = User::where('id', $request->user_id)->first()->email;

        $ref = "LOG-" . random_int(000, 999) . date('ymdhis');


        $data = new Transaction();
        $data->user_id = $request->user_id;
        $data->amount = $request->amount;
        $data->ref_id = $ref;
        $data->type = 2;
        $data->status = 2;
        $data->save();


        $message = $email . "| Manual Payment  Approved |  NGN " . number_format($request->amount) . " | on ACEVERIFY";
        send_notification2($message);


        return back()->with('message', 'Transaction added successfully');


    }


    public
    function delete_payment(request $request)
    {


        ManualPayment::where('id', $request->id)->delete();
        return back()->with('error', 'Transaction deleted successfully');


    }


    public
    function search_user(request $request)
    {

        $get_id = User::where('email', $request->email)->first()->id ?? null;

        if ($get_id == null) {
            return back()->with('error', 'No user Found');
        }

        $user = User::where('id', $get_id)->count();
        $users = User::where('id', $get_id)->paginate(10);


        return view('user', compact('user', 'users'));


    }


    public
    function search_username(request $request)
    {

        $get_id = User::where('username', $request->username)->first()->id ?? null;

        if ($get_id == null) {
            return back()->with('error', 'No user Found');
        }

        $user = User::where('id', $get_id)->count();
        $users = User::where('id', $get_id)->paginate(10);


        return view('user', compact('user', 'users'));


    }


}
