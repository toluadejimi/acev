<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\WalletCheck;
use Illuminate\Support\Facades\DB;

class WalletFundingService
{
    /**
     * Idempotent wallet credit for external payment gateways (e.g. SprintPay).
     * Credits at most once per $refId when status becomes success.
     *
     * @return array{ok: bool, duplicate?: bool, http?: int, message: string}
     */
    public static function creditFromExternalPayment(string $email, float $amount, string $refId): array
    {
        $email = trim($email);
        $refId = trim($refId);

        if ($email === '' || $refId === '' || $amount <= 0) {
            return ['ok' => false, 'http' => 422, 'message' => 'Invalid email, amount, or reference'];
        }

        return DB::transaction(function () use ($email, $amount, $refId) {
            $existing = Transaction::where('ref_id', $refId)->lockForUpdate()->first();

            if ($existing) {
                if ((int) $existing->status === 2 && abs((float) $existing->amount - $amount) < 0.01) {
                    return ['ok' => true, 'duplicate' => true, 'message' => 'Already credited'];
                }

                return ['ok' => false, 'http' => 409, 'message' => 'Payment reference already used'];
            }

            $user = User::where('email', $email)->lockForUpdate()->first();
            if (!$user) {
                return ['ok' => false, 'http' => 404, 'message' => 'No user found, please check email and try again'];
            }

            $old = (float) $user->wallet;
            $user->increment('wallet', $amount);
            $new = $old + $amount;

            $trx = new Transaction();
            $trx->ref_id = $refId;
            $trx->user_id = $user->id;
            $trx->status = 2;
            $trx->amount = $amount;
            $trx->balance = $new;
            $trx->old_balance = $old;
            $trx->type = 2;
            $trx->save();

            WalletCheck::firstOrCreate(
                ['user_id' => $user->id],
                ['total_funded' => $old, 'wallet_amount' => $old]
            );
            WalletCheck::where('user_id', $user->id)->increment('total_funded', $amount);
            WalletCheck::where('user_id', $user->id)->increment('wallet_amount', $amount);

            return ['ok' => true, 'duplicate' => false, 'message' => 'Wallet credited successfully'];
        });
    }
}
