<?php

namespace App\Http\Controllers;

use App\Services\WalletFundingService;
use App\Support\SprintPayWebhookAuth;
use Illuminate\Http\Request;

class SprintPayWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $secret = config('services.sprintpay.webhook_secret');
        if (!is_string($secret) || $secret === '') {
            return response()->json([
                'status' => false,
                'message' => 'Webhook funding is not configured',
            ], 503);
        }

        if (!SprintPayWebhookAuth::tokenValid($request, $secret)) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $email = (string) $request->input('email', '');
        $amount = (float) $request->input('amount', 0);
        $orderId = (string) $request->input('order_id', '');

        $result = WalletFundingService::creditFromExternalPayment($email, $amount, $orderId);

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
}
