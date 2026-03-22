<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Models\WalletCheck;
use App\Services\SprintPayVasClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class VtuBillsController extends Controller
{
    private function vasContext(): array
    {
        return [
            'vasConfigured' => SprintPayVasClient::configured(),
        ];
    }

    public function airtime(): View
    {
        $networks = $this->loadAirtimeNetworks();

        return view('vas.airtime', array_merge($this->vasContext(), [
            'networks' => $networks,
        ]));
    }

    public function data(): View
    {
        return view('vas.data', array_merge($this->vasContext(), [
            'networks' => $this->defaultNetworks(),
        ]));
    }

    public function cable(): View
    {
        return view('vas.cable', $this->vasContext());
    }

    public function electricity(): View
    {
        return view('vas.electricity', $this->vasContext());
    }

    public function catalogDataVariations(Request $request): JsonResponse
    {
        if (!SprintPayVasClient::configured()) {
            return response()->json(['status' => false, 'message' => 'Not configured'], 503);
        }

        $network = strtolower((string) $request->query('network', ''));
        if ($network === '') {
            return response()->json(['status' => false, 'message' => 'network required'], 422);
        }

        $resp = SprintPayVasClient::getPublic('get-data-variations', ['network' => $network]);

        return response()->json(
            $resp->json() ?? ['raw' => $resp->body()],
            $resp->status()
        );
    }

    public function catalogCablePlans(Request $request): JsonResponse
    {
        if (!SprintPayVasClient::configured()) {
            return response()->json(['status' => false, 'message' => 'Not configured'], 503);
        }

        $query = [];
        $sid = (string) $request->query('service_id', $request->query('serviceID', ''));
        if ($sid !== '') {
            $query['service_id'] = $sid;
            $query['serviceID'] = $sid;
        }

        $resp = SprintPayVasClient::getPublic('cable-plan', $query);

        return response()->json(
            $resp->json() ?? ['raw' => $resp->body()],
            $resp->status()
        );
    }

    public function catalogElectricityVariations(Request $request): JsonResponse
    {
        if (!SprintPayVasClient::configured()) {
            return response()->json(['status' => false, 'message' => 'Not configured'], 503);
        }

        $serviceId = (string) $request->query('serviceID', $request->query('service_id', ''));
        if ($serviceId === '') {
            return response()->json(['status' => false, 'message' => 'serviceID required'], 422);
        }

        $resp = SprintPayVasClient::getPublic('get-electricity-variations', ['serviceID' => $serviceId]);

        return response()->json(
            $resp->json() ?? ['raw' => $resp->body()],
            $resp->status()
        );
    }

    public function validateCable(Request $request): JsonResponse
    {
        if (!SprintPayVasClient::configured()) {
            return response()->json(['status' => false, 'message' => 'Not configured'], 503);
        }

        $request->validate([
            'service_id' => 'required|string|max:64',
            'billersCode' => 'required|string|max:40',
        ]);

        $resp = SprintPayVasClient::getMerchantVas('merchant/vas/validate-cable', [
            'service_id' => strtolower($request->input('service_id')),
            'billersCode' => preg_replace('/\s+/', '', $request->input('billersCode')),
        ]);

        return response()->json(
            $resp->json() ?? ['message' => SprintPayVasClient::extractMessage($resp)],
            $resp->successful() ? 200 : $resp->status()
        );
    }

    public function validateElectricity(Request $request): JsonResponse
    {
        if (!SprintPayVasClient::configured()) {
            return response()->json(['status' => false, 'message' => 'Not configured'], 503);
        }

        $request->validate([
            'service_id' => 'required|string|max:64',
            'billersCode' => 'required|string|max:40',
            'type' => 'nullable|string|max:32',
        ]);

        $query = [
            'service_id' => strtolower($request->input('service_id')),
            'billersCode' => preg_replace('/\s+/', '', $request->input('billersCode')),
        ];
        if ($request->filled('type')) {
            $query['type'] = $request->input('type');
        }

        $resp = SprintPayVasClient::getMerchantVas('merchant/vas/validate-electricity-meter', $query);

        return response()->json(
            $resp->json() ?? ['message' => SprintPayVasClient::extractMessage($resp)],
            $resp->successful() ? 200 : $resp->status()
        );
    }

    public function buyAirtime(Request $request): RedirectResponse
    {
        if (!SprintPayVasClient::configured()) {
            return back()->with('error', 'VTU billing is not configured. Set WEBKEY and SPRINTPAY_WEBHOOK_SECRET.');
        }

        $request->validate([
            'service_id' => 'required|string|max:64',
            'phone' => 'required|string|max:20',
            'amount' => 'required|numeric|min:50|max:100000',
        ]);

        $amount = round((float) $request->input('amount'), 2);
        $userId = (int) Auth::id();
        $phone = preg_replace('/\D/', '', (string) $request->input('phone'));
        if (strlen($phone) < 10) {
            return back()->with('error', 'Enter a valid phone number.');
        }

        $debit = $this->tryDebitForVas($userId, $amount);
        if ($debit === null) {
            return back()->with('error', 'Insufficient wallet balance. Fund your wallet first.');
        }

        $resp = SprintPayVasClient::postMerchantVas('merchant/vas/buy-ng-airtime', [
            'service_id' => strtolower($request->input('service_id')),
            'amount' => $amount,
            'phone' => $phone,
        ]);

        if (!SprintPayVasClient::responseIndicatesSuccess($resp)) {
            $this->refundVas($userId, $amount);
            Log::warning('SprintPay buy-ng-airtime failed', ['status' => $resp->status(), 'body' => $resp->body()]);

            return back()->with('error', SprintPayVasClient::extractMessage($resp));
        }

        $this->recordVasTransaction($userId, $amount, $debit['old_balance'], $debit['new_balance'], 'AIR');

        return back()->with('message', 'Airtime request completed. If debited, your line should receive the top-up shortly.');
    }

    public function buyData(Request $request): RedirectResponse
    {
        if (!SprintPayVasClient::configured()) {
            return back()->with('error', 'VTU billing is not configured. Set WEBKEY and SPRINTPAY_WEBHOOK_SECRET.');
        }

        $request->validate([
            'service_id' => 'required|string|max:64',
            'phone' => 'required|string|max:20',
            'variation_code' => 'required|string|max:160',
            'amount' => 'required|numeric|min:1|max:500000',
        ]);

        $amount = round((float) $request->input('amount'), 2);
        $userId = (int) Auth::id();
        $phone = preg_replace('/\D/', '', (string) $request->input('phone'));
        if (strlen($phone) < 10) {
            return back()->with('error', 'Enter a valid phone number.');
        }

        $debit = $this->tryDebitForVas($userId, $amount);
        if ($debit === null) {
            return back()->with('error', 'Insufficient wallet balance. Fund your wallet first.');
        }

        $body = [
            'service_id' => strtolower($request->input('service_id')),
            'phone' => $phone,
            'variation_code' => $request->input('variation_code'),
            'amount' => $amount,
        ];

        $resp = SprintPayVasClient::postMerchantVas('merchant/vas/buy-data', $body);

        if (!SprintPayVasClient::responseIndicatesSuccess($resp)) {
            $this->refundVas($userId, $amount);
            Log::warning('SprintPay buy-data failed', ['status' => $resp->status(), 'body' => $resp->body()]);

            return back()->with('error', SprintPayVasClient::extractMessage($resp));
        }

        $this->recordVasTransaction($userId, $amount, $debit['old_balance'], $debit['new_balance'], 'DATA');

        return back()->with('message', 'Data bundle purchase submitted. You should receive a confirmation SMS shortly.');
    }

    public function buyCable(Request $request): RedirectResponse
    {
        if (!SprintPayVasClient::configured()) {
            return back()->with('error', 'VTU billing is not configured. Set WEBKEY and SPRINTPAY_WEBHOOK_SECRET.');
        }

        $request->validate([
            'service_id' => 'required|string|max:64',
            'billersCode' => 'required|string|max:40',
            'variation_code' => 'required|string|max:160',
            'amount' => 'required|numeric|min:1|max:500000',
            'phone' => 'nullable|string|max:20',
        ]);

        $amount = round((float) $request->input('amount'), 2);
        $userId = (int) Auth::id();

        $debit = $this->tryDebitForVas($userId, $amount);
        if ($debit === null) {
            return back()->with('error', 'Insufficient wallet balance. Fund your wallet first.');
        }

        $body = [
            'service_id' => strtolower($request->input('service_id')),
            'billersCode' => preg_replace('/\s+/', '', $request->input('billersCode')),
            'variation_code' => $request->input('variation_code'),
            'amount' => $amount,
        ];
        if ($request->filled('phone')) {
            $body['phone'] = preg_replace('/\D/', '', (string) $request->input('phone'));
        }

        $resp = SprintPayVasClient::postMerchantVas('merchant/vas/buy-cable', $body);

        if (!SprintPayVasClient::responseIndicatesSuccess($resp)) {
            $this->refundVas($userId, $amount);
            Log::warning('SprintPay buy-cable failed', ['status' => $resp->status(), 'body' => $resp->body()]);

            return back()->with('error', SprintPayVasClient::extractMessage($resp));
        }

        $this->recordVasTransaction($userId, $amount, $debit['old_balance'], $debit['new_balance'], 'TV');

        return back()->with('message', 'Cable TV payment submitted. Check your decoder or provider SMS for status.');
    }

    public function buyElectricity(Request $request): RedirectResponse
    {
        if (!SprintPayVasClient::configured()) {
            return back()->with('error', 'VTU billing is not configured. Set WEBKEY and SPRINTPAY_WEBHOOK_SECRET.');
        }

        $request->validate([
            'service_id' => 'required|string|max:64',
            'billersCode' => 'required|string|max:40',
            'variation_code' => 'required|string|max:160',
            'amount' => 'required|numeric|min:100|max:500000',
            'phone' => 'required|string|max:20',
        ]);

        $amount = round((float) $request->input('amount'), 2);
        $userId = (int) Auth::id();
        $phone = preg_replace('/\D/', '', (string) $request->input('phone'));
        if (strlen($phone) < 10) {
            return back()->with('error', 'Enter a valid phone number for the receipt.');
        }

        $debit = $this->tryDebitForVas($userId, $amount);
        if ($debit === null) {
            return back()->with('error', 'Insufficient wallet balance. Fund your wallet first.');
        }

        $body = [
            'service_id' => strtolower($request->input('service_id')),
            'billersCode' => preg_replace('/\s+/', '', $request->input('billersCode')),
            'variation_code' => $request->input('variation_code'),
            'amount' => $amount,
            'phone' => $phone,
        ];

        $resp = SprintPayVasClient::postMerchantVas('merchant/vas/buy-electricity', $body);

        if (!SprintPayVasClient::responseIndicatesSuccess($resp)) {
            $this->refundVas($userId, $amount);
            Log::warning('SprintPay buy-electricity failed', ['status' => $resp->status(), 'body' => $resp->body()]);

            return back()->with('error', SprintPayVasClient::extractMessage($resp));
        }

        $this->recordVasTransaction($userId, $amount, $debit['old_balance'], $debit['new_balance'], 'PWR');

        $payload = $resp->json();
        $token = null;
        if (is_array($payload)) {
            $token = data_get($payload, 'token') ?? data_get($payload, 'data.token')
                ?? data_get($payload, 'data.purchased_code') ?? data_get($payload, 'purchased_code');
        }
        $msg = $token ? 'Electricity token: ' . $token : 'Electricity purchase submitted. Check SMS or receipt for your token.';

        return back()->with('message', $msg);
    }

    /**
     * @return array{old_balance: float, new_balance: float}|null
     */
    private function tryDebitForVas(int $userId, float $amount): ?array
    {
        return DB::transaction(function () use ($userId, $amount) {
            $user = User::where('id', $userId)->lockForUpdate()->first();
            if (!$user || (float) $user->wallet < $amount) {
                return null;
            }

            $old = (float) $user->wallet;
            $new = $old - $amount;

            User::where('id', $userId)->decrement('wallet', $amount);

            WalletCheck::firstOrCreate(
                ['user_id' => $userId],
                ['total_funded' => $old, 'wallet_amount' => $old]
            );
            WalletCheck::where('user_id', $userId)->increment('total_bought', $amount);
            WalletCheck::where('user_id', $userId)->decrement('wallet_amount', $amount);

            return ['old_balance' => $old, 'new_balance' => $new];
        });
    }

    private function refundVas(int $userId, float $amount): void
    {
        User::where('id', $userId)->increment('wallet', $amount);
        WalletCheck::where('user_id', $userId)->decrement('total_bought', $amount);
        WalletCheck::where('user_id', $userId)->increment('wallet_amount', $amount);
    }

    private function recordVasTransaction(
        int $userId,
        float $amount,
        float $oldBalance,
        float $newBalance,
        string $prefix
    ): void {
        $trx = new Transaction();
        $trx->ref_id = $prefix . '-' . strtoupper(bin2hex(random_bytes(6)));
        $trx->user_id = $userId;
        $trx->status = 2;
        $trx->amount = $amount;
        $trx->balance = $newBalance;
        $trx->old_balance = $oldBalance;
        $trx->type = 4;
        $trx->save();
    }

    /**
     * @return list<array{value:string,label:string}>
     */
    private function defaultNetworks(): array
    {
        return [
            ['value' => 'mtn', 'label' => 'MTN'],
            ['value' => 'glo', 'label' => 'Glo'],
            ['value' => 'airtel', 'label' => 'Airtel'],
            ['value' => '9mobile', 'label' => '9mobile'],
        ];
    }

    /**
     * @return list<array{value:string,label:string}>
     */
    private function loadAirtimeNetworks(): array
    {
        $fallback = $this->defaultNetworks();
        if (!SprintPayVasClient::configured()) {
            return $fallback;
        }

        $resp = SprintPayVasClient::getPublic('get-service');
        if (!$resp->successful()) {
            return $fallback;
        }

        $json = $resp->json();
        $out = [];
        if (is_array($json)) {
            $candidates = $json['data'] ?? $json['services'] ?? $json['content'] ?? $json;
            if (is_array($candidates)) {
                foreach ($candidates as $row) {
                    if (!is_array($row)) {
                        continue;
                    }
                    $id = $row['serviceID'] ?? $row['service_id'] ?? $row['id'] ?? null;
                    $name = $row['name'] ?? $row['title'] ?? $id;
                    if ($id !== null && is_string($id)) {
                        $out[] = ['value' => strtolower($id), 'label' => (string) $name];
                    }
                }
            }
        }

        return $out !== [] ? $out : $fallback;
    }
}
