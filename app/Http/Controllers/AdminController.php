<?php

namespace App\Http\Controllers;

use App\Models\AccountDetail;
use App\Models\AppConfig;
use App\Models\Item;
use App\Models\MainItem;
use App\Models\ManualPayment;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Verification;
use App\Models\WalletCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;

class AdminController extends Controller
{


    public function index(request $request)
    {


        return view('admin-login');


    }


    public function price_setting_view(request $request)
    {
        $data['set1'] = Setting::where('id', 1)->first();
        $data['set2'] = Setting::where('id', 2)->first();
        $data['set3'] = Setting::where('id', 3)->first();
        $data['verificationServerFlags'] = verification_server_flags();
        $data['verificationServerKeys'] = [
            'us2' => verification_server_api_key('us2'),
            'world' => verification_server_api_key('world'),
            'world_hero' => verification_server_api_key('world_hero'),
            'world_sv3' => verification_server_api_key('world_sv3'),
        ];
        $data['verificationServerRates'] = [
            'world_hero' => verification_server_rate('world_hero'),
            'world_sv3' => verification_server_rate('world_sv3'),
        ];
        $data['verificationServerMargins'] = [
            'world_hero' => verification_server_margin('world_hero'),
            'world_sv3' => verification_server_margin('world_sv3'),
        ];
        return view('admin-price-setting', $data);

    }

    public function save_sms_server_config(Request $request)
    {
        $request->validate([
            'sms_server_name' => 'required|string|in:herosms,custom',
            'sms_server_base_url' => 'required|url',
            'sms_server_api_key' => 'required|string|max:255',
        ]);

        AppConfig::updateOrCreate(
            ['config_key' => 'sms_server_name'],
            ['config_value' => strtolower($request->input('sms_server_name'))]
        );
        AppConfig::updateOrCreate(
            ['config_key' => 'sms_server_base_url'],
            ['config_value' => rtrim((string) $request->input('sms_server_base_url'), '/')]
        );
        AppConfig::updateOrCreate(
            ['config_key' => 'sms_server_api_key'],
            ['config_value' => trim((string) $request->input('sms_server_api_key'))]
        );

        Cache::forget('app_config:sms_server_name');
        Cache::forget('app_config:sms_server_base_url');
        Cache::forget('app_config:sms_server_api_key');

        return back()->with('message', 'SMS server configuration updated successfully');
    }

    public function save_verification_servers_config(Request $request)
    {
        AppConfig::updateOrCreate(
            ['config_key' => 'verification_server_us1_enabled'],
            ['config_value' => '0']
        );
        Cache::forget('app_config:verification_server_us1_enabled');
        AppConfig::updateOrCreate(
            ['config_key' => 'verification_server_us2_enabled'],
            ['config_value' => $request->has('verification_server_us2_enabled') ? '1' : '0']
        );
        AppConfig::updateOrCreate(
            ['config_key' => 'verification_server_world_enabled'],
            ['config_value' => $request->has('verification_server_world_enabled') ? '1' : '0']
        );
        AppConfig::updateOrCreate(
            ['config_key' => 'verification_server_world_sv3_enabled'],
            ['config_value' => $request->has('verification_server_world_sv3_enabled') ? '1' : '0']
        );

        Cache::forget('app_config:verification_server_us2_enabled');
        Cache::forget('app_config:verification_server_world_enabled');
        Cache::forget('app_config:verification_server_world_sv3_enabled');

        return back()->with('message', 'Verification server visibility updated successfully');
    }

    public function save_verification_server_card_config(Request $request, string $server)
    {
        $server = strtolower($server);
        $map = [
            'us2' => ['setting_id' => 3, 'enabled_key' => 'verification_server_us2_enabled', 'api_key' => 'verification_server_us2_api_key'],
            'world' => ['setting_id' => 2, 'enabled_key' => 'verification_server_world_enabled', 'api_key' => 'verification_server_world_api_key'],
            'world_hero' => ['setting_id' => null, 'enabled_key' => 'verification_server_world_hero_enabled', 'api_key' => 'verification_server_world_hero_api_key'],
            'world_sv3' => ['setting_id' => null, 'enabled_key' => 'verification_server_world_sv3_enabled', 'api_key' => 'verification_server_world_sv3_api_key'],
        ];

        if (!isset($map[$server])) {
            return back()->with('error', 'Invalid server selected');
        }

        $request->validate([
            'rate' => 'required|numeric|min:0',
            'margin' => 'required|numeric|min:0',
            'api_key' => 'nullable|string|max:255',
            'world_hero_rate' => 'nullable|numeric|min:0',
            'world_hero_margin' => 'nullable|numeric|min:0',
            'world_sv3_rate' => 'nullable|numeric|min:0',
            'world_sv3_margin' => 'nullable|numeric|min:0',
        ]);

        $cfg = $map[$server];

        if (!empty($cfg['setting_id'])) {
            Setting::where('id', $cfg['setting_id'])->update([
                'rate' => $request->input('rate'),
                'margin' => $request->input('margin'),
            ]);
        } elseif ($server === 'world_hero') {
            AppConfig::updateOrCreate(
                ['config_key' => 'verification_server_world_hero_rate'],
                ['config_value' => (string) $request->input('world_hero_rate', $request->input('rate', 0))]
            );
            AppConfig::updateOrCreate(
                ['config_key' => 'verification_server_world_hero_margin'],
                ['config_value' => (string) $request->input('world_hero_margin', $request->input('margin', 0))]
            );
            Cache::forget('app_config:verification_server_world_hero_rate');
            Cache::forget('app_config:verification_server_world_hero_margin');
        } else {
            AppConfig::updateOrCreate(
                ['config_key' => 'verification_server_world_sv3_rate'],
                ['config_value' => (string) $request->input('world_sv3_rate', $request->input('rate', 0))]
            );
            AppConfig::updateOrCreate(
                ['config_key' => 'verification_server_world_sv3_margin'],
                ['config_value' => (string) $request->input('world_sv3_margin', $request->input('margin', 0))]
            );
            Cache::forget('app_config:verification_server_world_sv3_rate');
            Cache::forget('app_config:verification_server_world_sv3_margin');
        }

        AppConfig::updateOrCreate(
            ['config_key' => $cfg['enabled_key']],
            ['config_value' => $request->has('enabled') ? '1' : '0']
        );
        AppConfig::updateOrCreate(
            ['config_key' => $cfg['api_key']],
            ['config_value' => trim((string) $request->input('api_key', ''))]
        );
        Cache::forget('app_config:' . $cfg['enabled_key']);
        Cache::forget('app_config:' . $cfg['api_key']);

        return back()->with('message', 'Server settings updated successfully');
    }


    public function set_rate_1(request $request)
    {
        Setting::where('id', 1)->update(['rate' => $request->rate]);

        return back()->with('message', 'Rate Updated successfully');

    }

    public function set_rate_2(request $request)
    {
        Setting::where('id', 2)->update(['rate' => $request->rate]);

        return back()->with('message', 'Rate Updated successfully');

    }

    public function set_rate_3(request $request)
    {
        Setting::where('id', 3)->update(['rate' => $request->rate]);

        return back()->with('message', 'Rate Updated successfully');

    }
    public function set_margin_3(request $request)
    {
        Setting::where('id', 3)->update(['margin' => $request->margin]);

        return back()->with('message', 'Rate Updated successfully');

    }

    public function set_margin_1(request $request)
    {
        Setting::where('id', 1)->update(['margin' => $request->margin]);

        return back()->with('message', 'Rate Updated successfully');

    }

    public function set_margin_2(request $request)
    {
        Setting::where('id', 2)->update(['margin' => $request->margin]);

        return back()->with('message', 'Margin Updated successfully');

    }




    public function transactions(Request $request)
    {
        $role = User::where('id', Auth::id())->first()->role_id ?? null;
        if ($role != 5) {
            Auth::logout();
            return redirect('/admin')->with('error', "You do not have permission");
        }

        $filter = $request->input('kind'); // e.g. verification, funding, api_order, vtu_airtime, etc.

        $query = Transaction::query()
            ->latest()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);

        // Optional filter by logical transaction kind
        if ($filter) {
            $this->applyTransactionKindFilter($query, $filter);
        }

        $data['transaction'] = $query->paginate(100);

        $baseCreditQuery = Transaction::where('type', 2)
            ->where('status', 2)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);

        $baseDebitQuery = Transaction::where('type', 1)
            ->where('status', 2)
            ->where('ref_id', 'LIKE', '%Verification%')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);

        if ($filter) {
            $this->applyTransactionKindFilter($baseCreditQuery, $filter);
            $this->applyTransactionKindFilter($baseDebitQuery, $filter);
        }

        $data['credit'] = $baseCreditQuery->sum('amount');
        $data['debit'] = $baseDebitQuery->sum('amount');
        $data['kind'] = $filter;

        return view('transactions', $data);
    }

    /**
     * Apply a high-level "kind" filter to a Transaction query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $kind
     */
    private function applyTransactionKindFilter($query, string $kind): void
    {
        $kind = strtolower(trim($kind));

        switch ($kind) {
            case 'funding':
                // Wallet funding / manual payments
                $query->where('type', 2);
                break;
            case 'verification':
                // Normal verification debits
                $query->where('type', 1)
                    ->where('ref_id', 'LIKE', '%Verification%');
                break;
            case 'order_cancel':
                $query->where('type', 3)
                    ->where('ref_id', 'LIKE', '%Order Cancel%');
                break;
            case 'api_order':
                $query->where('type', 1)
                    ->where(function ($q) {
                        $q->where('ref_id', 'LIKE', 'APIVerification%')
                          ->orWhere('ref_id', 'LIKE', 'API_VERIFICATION%');
                    });
                break;
            case 'api_order_cancel':
                $query->where('type', 3)
                    ->where(function ($q) {
                        $q->where('ref_id', 'LIKE', 'Order API Cancel%')
                          ->orWhere('ref_id', 'LIKE', 'API_ORDER_CANCEL_%');
                    });
                break;
            case 'vtu':
                // All VTU (airtime, data, cable, electricity) share type = 4
                $query->where('type', 4);
                break;
            default:
                // no-op, show all kinds
                break;
        }
    }

    public
    function search_trx(request $request)
    {


        $data['credit'] = Transaction::where(['type' => 2])
            ->where('status', 2)
            ->when($request->from && $request->to, function ($query) use ($request) {
                $query->whereBetween('created_at', [
                    Carbon::parse($request->from)->startOfDay(),
                    Carbon::parse($request->to)->endOfDay(),
                ]);
            })
            ->sum('amount');


            $data['debit'] = Transaction::where('type', 1)
            ->where('ref_id', 'LIKE', '%Verification%')
            ->where('status', 2)
            ->when($request->from && $request->to, function ($query) use ($request) {
                $query->whereBetween('created_at', [
                    Carbon::parse($request->from)->startOfDay(),
                    Carbon::parse($request->to)->endOfDay(),
                ]);
            })
            ->sum('amount');

        $data['transaction'] = Transaction::latest()->when($request->from && $request->to, function ($query) use ($request) {
                $query->whereBetween('created_at', [
                    Carbon::parse($request->from)->startOfDay(),
                    Carbon::parse($request->to)->endOfDay(),
                ]);
            })
            ->paginate(100);

        return view('transactions', $data);




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

    public function notify(request $request)
    {
        $row = Notification::query()->find(1);
        if ($row === null) {
            $row = new Notification();
            $row->id = 1;
            $row->message = '';
            $row->title = null;
            $row->is_active = false;
            $row->save();
        }

        $data['notify'] = $row->message ?? '';
        $data['notifyTitle'] = $row->title ?? '';
        $data['notifyActive'] = (bool) ($row->is_active ?? false);

        return view('notify', $data);
    }

    public function save_notification(request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:20000',
            'is_active' => 'nullable|boolean',
        ]);

        $row = Notification::firstOrNew(['id' => 1]);
        $row->title = $request->input('title');
        $row->message = $request->input('message', '');
        $row->is_active = $request->boolean('is_active');
        $row->save();

        return redirect()->back()->with('message', 'Announcement saved.');
    }



public function admin_dashboard(request $request)
    {

        $role = User::where('id', Auth::id())->first()->role_id ?? null;
        if ($role != 5) {

            Auth::logout();
            return redirect('/admin')->with('error', "You do not have permission");

        }


        $data['user'] = User::where('status', 2)->count();
        $data['total_in'] = Transaction::where('type', 2)->where('status', 2)->sum('amount');
        $data['transaction'] = Transaction::latest()->paginate(10);
        $data['total_in_d'] = Transaction::where(['type' => 2, 'status' => 2])
            ->whereBetween('created_at', [
                Carbon::today()->startOfDay(),
                Carbon::today()->endOfDay()
            ])
            ->sum('amount');
        $data['total_out'] = Verification::where('status', 2)->sum('cost');
        $data['total_verified_message'] = Verification::where('status', 2)->count();
        $data['manual_payment'] = ManualPayment::where('status', 0)->count();
        $data['manual_payment'] = ManualPayment::where('status', 0)->count();
        $data['user_total'] = User::where('status', 2)->sum('wallet');
        $data['usdtongn'] = Setting::where('id', 1)->first()->rate;
        $data['margin'] = Setting::where('id', 1)->first()->margin;
        $data['verification'] = Verification::latest()->paginate(50);
        $data['today_order'] = Verification::whereBetween('created_at', [
            Carbon::today()->startOfDay(),
            Carbon::today()->endOfDay()
        ])->where('status', 2)->count();
        $data['new_user_today'] = User::whereBetween('created_at', [
            Carbon::today()->startOfDay(),
            Carbon::today()->endOfDay()
        ])->where('status', 2)->count();


        //        try {
//
//            DB::beginTransaction();
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
//                $user->save();
//
//
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


//        try {
//            DB::beginTransaction();
//
//            $transactions = Transaction::where('type', 2)->where('status', 2)->get();
//
//            foreach ($transactions as $transaction) {
//                $user = User::find($transaction->user_id);
//
//                if ($user) {
//                    $user->wallet += $transaction->amount;
//                    $user->save();
//                    $transaction->delete();
//                }
//            }
//
//            DB::commit();
//
//            return response()->json([
//                'status' => 'success',
//                'message' => 'Wallet balances have been restored.',
//            ]);
//        } catch (\Exception $e) {
//            DB::rollBack();
//
//            return response()->json([
//                'status' => 'error',
//                'message' => 'An error occurred while restoring wallet balances: ' . $e->getMessage(),
//            ], 500);
//        }


        return view('admin-dashboard', $data);

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

            WalletCheck::where('user_id', $request->id)->increment('total_funded', $request->amount);
            WalletCheck::where('user_id', $request->id)->increment('wallet_amount', $request->amount);

            $get_balance = User::where('id', $request->id)->first()->wallet;
            $balance = $get_balance + $request->amount;

            $ref = "MANUAL" . random_int(000000, 999999);
            $data = new Transaction();
            $data->user_id = $request->id;
            $data->amount = $request->amount;
            $data->balance = $balance;
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

            WalletCheck::where('user_id', $request->id)->decrement('wallet_amount', $request->amount);


            $email = User::where('id', $request->id)->first()->email;
            $message = "Wallet has been debited by admin | $email | $request->amount | on Ace Verify";
            send_notification($message);
            send_notification2($message);
            return back()->with('error', 'Wallet Debited Successfully');

        }


        return back()->with('message', 'Wallet Credited Successfully');


    }


    public
    function view_verification(request $request)
    {

        $role = User::where('id', Auth::id())->first()->role_id ?? null;

        if ($role != 5) {

            Auth::logout();
            return redirect('/admin')->with('error', "You do not have permission");

        }


        $data['user'] = User::where('id', $request->user_id)->first();
        $data['verification'] = Verification::latest()->where('user_id', $request->user_id)->paginate(50);


        return view('user-verification', $data);

    }public
    function view_transaction(request $request)
    {

        $role = User::where('id', Auth::id())->first()->role_id ?? null;

        if ($role != 5) {

            Auth::logout();
            return redirect('/admin')->with('error', "You do not have permission");

        }


        $data['user'] = User::where('id', $request->user_id)->first();
        $data['trasnaction'] = Transaction::latest()->where('user_id', $request->user_id)->paginate(100);


        return view('user-trx', $data);

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


        WalletCheck::where('user_id', $request->user_id)->increment('total_funded', $request->amount);
        WalletCheck::where('user_id', $request->user_id)->increment('wallet_amount', $request->amount);

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


    public function view_users(Request $request)
    {
        $users = User::where('id', $request->id)->paginate(10);
        return view('user', compact( 'users'));
    }

    public
    function search_user(request $request)
    {

        $get_user = User::where(function ($query) use ($request) {
            $query->where('email', $request->search)
                ->orWhere('username', $request->search);
        })->first();

        if (!$get_user) {
            return back()->with('error', 'No user found');
        }

        $user = User::where('id', $get_user->id)->count();
        $users = User::where('id', $get_user->id)->paginate(10);

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

    public function login_as_user(Request $request, int $id)
    {
        $role = User::where('id', Auth::id())->value('role_id') ?? null;
        if ($role != 5) {
            Auth::logout();
            return redirect('/admin')->with('error', "You do not have permission");
        }

        $target = User::find($id);
        if (!$target) {
            return back()->with('error', 'User not found');
        }

        if ((int) $target->id === (int) Auth::id()) {
            return back()->with('error', 'You are already logged in as this user');
        }

        $request->session()->put('admin_impersonator_id', (int) Auth::id());
        Auth::login($target);
        $request->session()->regenerate();

        return redirect('/home')->with('topMessage', "You are now logged in as {$target->username}");
    }


}
