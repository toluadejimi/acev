<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssistantController extends Controller
{
    public function handle(Request $request, UnlimitedPortalController $unlimited)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $raw = trim((string) $request->input('message'));
        $msg = strtolower($raw);

        if ($msg === '' || in_array($msg, ['help', 'menu', 'start'], true)) {
            return response()->json([
                'ok' => true,
                'reply' => "I can help with orders, VTU, and support.\nTry:\n- order usa whatsapp\n- vtu airtime\n- vtu data\n- balance\n- contact support",
                'links' => [
                    ['label' => 'VTU Airtime', 'url' => url('/vas/airtime')],
                    ['label' => 'VTU Data', 'url' => url('/vas/data')],
                    ['label' => 'Contact Support', 'url' => 'https://t.me/acesmsverify'],
                ],
            ]);
        }

        if (preg_match('/\b(balance|wallet)\b/', $msg)) {
            $bal = number_format((float) (Auth::user()->wallet ?? 0), 2);
            return response()->json([
                'ok' => true,
                'reply' => "Your wallet balance is NGN {$bal}.",
            ]);
        }

        if (preg_match('/\b(contact|support|help me|telegram)\b/', $msg)) {
            return response()->json([
                'ok' => true,
                'reply' => 'Support is available on Telegram. Tap below to chat with support.',
                'links' => [
                    ['label' => 'Contact Support', 'url' => 'https://t.me/acesmsverify'],
                ],
            ]);
        }

        if (preg_match('/\bvtu\b|\bairtime\b|\bdata\b|\bcable\b|\belectricity\b|\bbills?\b/', $msg)) {
            $target = '/vas/airtime';
            $label = 'VTU Airtime';
            if (preg_match('/\bdata\b/', $msg)) {
                $target = '/vas/data';
                $label = 'VTU Data';
            } elseif (preg_match('/\bcable\b/', $msg)) {
                $target = '/vas/cable';
                $label = 'VTU Cable TV';
            } elseif (preg_match('/\belectricity\b|\bpower\b|\bnepa\b/', $msg)) {
                $target = '/vas/electricity';
                $label = 'VTU Electricity';
            }

            return response()->json([
                'ok' => true,
                'reply' => "Opening {$label}. Choose your provider and complete the order.",
                'links' => [
                    ['label' => $label, 'url' => url($target)],
                ],
            ]);
        }

        if (preg_match('/\border\s+usa\s+(.+)/', $msg, $m)) {
            $serviceQuery = trim((string) ($m[1] ?? ''));
            if ($serviceQuery === '') {
                return response()->json(['ok' => false, 'reply' => 'Please specify a service, e.g. order usa whatsapp']);
            }

            $result = $unlimited->assistantOrderUsaByQuery($serviceQuery);
            if (!(bool) $result['ok']) {
                return response()->json([
                    'ok' => false,
                    'reply' => (string) $result['message'] . " Do you want to try World Server 1 or World Server 2?",
                    'links' => [
                        ['label' => 'World Server 1', 'url' => url('/world')],
                        ['label' => 'World Server 2', 'url' => url('/world-sv2')],
                    ],
                ]);
            }

            return response()->json([
                'ok' => (bool) $result['ok'],
                'reply' => (string) $result['message'],
                'reload' => (bool) $result['ok'],
            ]);
        }

        return response()->json([
            'ok' => false,
            'reply' => "I didn't understand that yet. Try: order usa whatsapp, vtu data, or contact support.",
            'links' => [
                ['label' => 'VTU Data', 'url' => url('/vas/data')],
                ['label' => 'Contact Support', 'url' => 'https://t.me/acesmsverify'],
            ],
        ]);
    }
}

