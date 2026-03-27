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
                'reply' => "I can help with quick actions.\nTry:\n- order usa whatsapp\n- order usa telegram\n- balance",
            ]);
        }

        if (preg_match('/\b(balance|wallet)\b/', $msg)) {
            $bal = number_format((float) (Auth::user()->wallet ?? 0), 2);
            return response()->json([
                'ok' => true,
                'reply' => "Your wallet balance is NGN {$bal}.",
            ]);
        }

        if (preg_match('/\border\s+usa\s+(.+)/', $msg, $m)) {
            $serviceQuery = trim((string) ($m[1] ?? ''));
            if ($serviceQuery === '') {
                return response()->json(['ok' => false, 'reply' => 'Please specify a service, e.g. order usa whatsapp']);
            }

            $result = $unlimited->assistantOrderUsaByQuery($serviceQuery);
            return response()->json([
                'ok' => (bool) $result['ok'],
                'reply' => (string) $result['message'],
                'reload' => (bool) $result['ok'],
            ]);
        }

        return response()->json([
            'ok' => false,
            'reply' => "I didn't understand that yet. Try: order usa whatsapp",
        ]);
    }
}

