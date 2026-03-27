<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AssistantController extends Controller
{
    public function handle(Request $request, UnlimitedPortalController $unlimited, VtuBillsController $vtu)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $raw = trim((string) $request->input('message'));
        $msg = strtolower($raw);

        if (in_array($msg, ['cancel', 'stop', 'reset'], true)) {
            $request->session()->forget('assistant_airtime');
            $request->session()->forget('assistant_world_retry_service');
            return response()->json(['ok' => true, 'reply' => 'Assistant flow reset.']);
        }

        // Continue interactive VTU airtime flow if active.
        $air = $request->session()->get('assistant_airtime');
        if (is_array($air) && !empty($air['step'])) {
            return $this->continueAirtimeFlow($request, $vtu, $raw, $air);
        }

        // Continue world retry choice flow after USA failure.
        $pendingWorldService = (string) $request->session()->get('assistant_world_retry_service', '');
        if ($pendingWorldService !== '' && preg_match('/\b(world\s*server\s*1|server\s*1|sv1|world1)\b/', $msg)) {
            $request->session()->forget('assistant_world_retry_service');
            $result = $this->assistantOrderWorldByQuery($pendingWorldService, 'smspool');
            return response()->json([
                'ok' => (bool) $result['ok'],
                'reply' => (string) $result['message'],
                'reload' => (bool) $result['ok'],
            ]);
        }
        if ($pendingWorldService !== '' && preg_match('/\b(world\s*server\s*2|server\s*2|sv2|world2)\b/', $msg)) {
            $request->session()->forget('assistant_world_retry_service');
            $result = $this->assistantOrderWorldByQuery($pendingWorldService, 'herosms');
            return response()->json([
                'ok' => (bool) $result['ok'],
                'reply' => (string) $result['message'],
                'reload' => (bool) $result['ok'],
            ]);
        }

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

        if (preg_match('/\b(i want to buy|buy)\s+airtime\b|\bvtu\s+airtime\b|\bairtime\b/', $msg)) {
            $request->session()->put('assistant_airtime', [
                'step' => 'network',
                'data' => [],
            ]);
            return response()->json([
                'ok' => true,
                'reply' => 'Airtime order started. Which network? (MTN, Glo, Airtel, 9mobile)',
                'actions' => [
                    ['label' => 'MTN', 'command' => 'mtn'],
                    ['label' => 'Glo', 'command' => 'glo'],
                    ['label' => 'Airtel', 'command' => 'airtel'],
                    ['label' => '9mobile', 'command' => '9mobile'],
                ],
            ]);
        }

        if (preg_match('/\bvtu\b|\bdata\b|\bcable\b|\belectricity\b|\bbills?\b/', $msg)) {
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
                $request->session()->put('assistant_world_retry_service', $serviceQuery);
                return response()->json([
                    'ok' => false,
                    'reply' => (string) $result['message'] . " Do you want to try World Server 1 or World Server 2?",
                    'actions' => [
                        ['label' => 'Try World Server 1', 'command' => 'world server 1'],
                        ['label' => 'Try World Server 2', 'command' => 'world server 2'],
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

    private function continueAirtimeFlow(Request $request, VtuBillsController $vtu, string $raw, array $state)
    {
        $step = (string) ($state['step'] ?? '');
        $data = (array) ($state['data'] ?? []);

        if ($step === 'network') {
            $n = strtolower(trim($raw));
            if (in_array($n, ['mtn', 'glo', 'airtel', '9mobile'], true) === false) {
                return response()->json(['ok' => false, 'reply' => 'Use one of: MTN, Glo, Airtel, 9mobile.']);
            }
            $data['network'] = $n;
            $request->session()->put('assistant_airtime', ['step' => 'phone', 'data' => $data]);
            return response()->json(['ok' => true, 'reply' => 'Great. Enter phone number (11 digits).']);
        }

        if ($step === 'phone') {
            $phone = preg_replace('/\D/', '', $raw);
            if (strlen($phone) !== 11) {
                return response()->json(['ok' => false, 'reply' => 'Phone must be exactly 11 digits.']);
            }
            $data['phone'] = $phone;
            $request->session()->put('assistant_airtime', ['step' => 'amount', 'data' => $data]);
            return response()->json(['ok' => true, 'reply' => 'Enter amount in NGN (e.g. 500).']);
        }

        if ($step === 'amount') {
            $amount = (float) preg_replace('/[^\d.]/', '', $raw);
            if ($amount <= 0) {
                return response()->json(['ok' => false, 'reply' => 'Enter a valid amount, e.g. 500.']);
            }
            $request->session()->forget('assistant_airtime');
            $res = $vtu->assistantBuyAirtime(
                (int) Auth::id(),
                (string) ($data['network'] ?? ''),
                (string) ($data['phone'] ?? ''),
                $amount
            );
            return response()->json([
                'ok' => (bool) ($res['ok'] ?? false),
                'reply' => (string) ($res['message'] ?? 'Done'),
                'reload' => (bool) ($res['ok'] ?? false),
            ]);
        }

        $request->session()->forget('assistant_airtime');
        return response()->json(['ok' => false, 'reply' => 'Flow expired. Say: buy airtime']);
    }

    /**
     * @return array{ok:bool,message:string}
     */
    private function assistantOrderWorldByQuery(string $serviceQuery, string $provider): array
    {
        $provider = strtolower($provider) === 'herosms' ? 'herosms' : 'smspool';
        $serviceQueryNorm = preg_replace('/[^a-z0-9]+/', '', strtolower($serviceQuery)) ?? '';
        if ($serviceQueryNorm === '') {
            return ['ok' => false, 'message' => 'No service specified for world order.'];
        }

        $serviceId = null;
        $serviceName = null;
        if ($provider === 'smspool') {
            $rows = get_world_services();
            foreach ((array) $rows as $row) {
                $r = (array) $row;
                $name = (string) ($r['name'] ?? '');
                $id = (string) ($r['ID'] ?? '');
                $n = preg_replace('/[^a-z0-9]+/', '', strtolower($name)) ?? '';
                if ($n !== '' && (str_contains($n, $serviceQueryNorm) || str_contains($serviceQueryNorm, $n))) {
                    $serviceId = $id;
                    $serviceName = $name;
                    break;
                }
            }
        } else {
            $resp = Http::timeout(20)->get(world_sms_handler_url(['action' => 'getServicesList', 'lang' => 'en']));
            $rows = is_array($resp->json()) ? ($resp->json()['services'] ?? []) : [];
            foreach ((array) $rows as $row) {
                $r = (array) $row;
                $name = (string) ($r['name'] ?? '');
                $id = (string) ($r['code'] ?? '');
                $n = preg_replace('/[^a-z0-9]+/', '', strtolower($name)) ?? '';
                if ($n !== '' && (str_contains($n, $serviceQueryNorm) || str_contains($serviceQueryNorm, $n))) {
                    $serviceId = $id;
                    $serviceName = $name;
                    break;
                }
            }
        }

        if (!$serviceId) {
            return ['ok' => false, 'message' => 'Service not found on selected world server.'];
        }

        if ($provider === 'smspool') {
            $country = Country::where('short_name', 'US')->value('country_id');
            if (!$country) {
                return ['ok' => false, 'message' => 'Unable to resolve US country for World Server 1.'];
            }
            $out = create_world_order((string) $country, (string) $serviceId, 'smspool', null, $serviceName);
        } else {
            $countryRows = Http::timeout(20)->get(world_sms_handler_url(['action' => 'getCountries']))->json();
            $countryId = null;
            if (is_array($countryRows)) {
                foreach ($countryRows as $c) {
                    $name = strtolower((string) (($c['eng'] ?? $c['name'] ?? '')));
                    if (str_contains($name, 'united states') || $name === 'usa') {
                        $countryId = (string) ($c['id'] ?? '');
                        break;
                    }
                }
            }
            if (!$countryId) {
                return ['ok' => false, 'message' => 'Unable to resolve US country for World Server 2.'];
            }
            $prices = hero_sms_get_price_options((string) $serviceId, (string) $countryId);
            if ($prices === []) {
                return ['ok' => false, 'message' => 'No available price tier on World Server 2 for this service.'];
            }
            $apiCost = (float) ($prices[0]['cost'] ?? 0);
            if ($apiCost <= 0) {
                return ['ok' => false, 'message' => 'Invalid world price tier.'];
            }
            $out = create_world_order((string) $countryId, (string) $serviceId, 'herosms', $apiCost, $serviceName);
        }

        if ((int) $out === 3) {
            return ['ok' => true, 'message' => "World order placed successfully on " . ($provider === 'herosms' ? 'Server 2' : 'Server 1') . "."];
        }
        if ((int) $out === 99) {
            return ['ok' => false, 'message' => 'Insufficient wallet balance for world order.'];
        }
        if ((int) $out === 5) {
            return ['ok' => false, 'message' => 'Service currently unavailable on selected world server.'];
        }

        return ['ok' => false, 'message' => 'World order failed. Please try again shortly.'];
    }
}

