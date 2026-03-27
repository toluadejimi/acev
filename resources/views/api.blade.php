@extends('layout.dashboard-modern')

@section('title', 'API reference')

@php
    $b = rtrim(url(''), '/');
    $key = $api_key ?? '';
    $vf = $verification_servers ?? ['us1' => false, 'us2' => true, 'world' => true, 'world_hero' => true];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ url('') }}/public/css/api-docs.css?v=1">
@endpush

@section('content')
<div class="api-shell">
    <header class="api-hero">
        <p class="api-hero__eyebrow">Developers</p>
        <h1 class="api-hero__title">REST API reference</h1>
        <p class="api-hero__lead">
            Integrate wallet balance, USA numbers, and international numbers using your account API key.
            All endpoints accept <code style="opacity:.85">api_key</code> and <code style="opacity:.85">action</code> as query parameters unless noted.
        </p>
        <div class="api-base">
            <span class="api-base__label">Base URL</span>
            <code class="api-base__url" id="api-base-url">{{ $b }}/api</code>
            <button type="button" class="api-btn api-btn--dark api-copy" style="position:static" data-copy="{{ $b }}/api">Copy</button>
        </div>
    </header>

    <div class="api-badges" aria-label="Verification servers available on the website">
        <span class="api-badge {{ !empty($vf['us1']) ? 'api-badge--on' : 'api-badge--off' }}">
            <i class="bi bi-{{ !empty($vf['us1']) ? 'check-circle-fill' : 'dash-circle' }}" aria-hidden="true"></i>
            USA · Server 1
        </span>
        <span class="api-badge {{ !empty($vf['us2']) ? 'api-badge--on' : 'api-badge--off' }}">
            <i class="bi bi-{{ !empty($vf['us2']) ? 'check-circle-fill' : 'dash-circle' }}" aria-hidden="true"></i>
            USA · Server 1
        </span>
        <span class="api-badge {{ !empty($vf['world']) ? 'api-badge--on' : 'api-badge--off' }}">
            <i class="bi bi-{{ !empty($vf['world']) ? 'check-circle-fill' : 'dash-circle' }}" aria-hidden="true"></i>
            World · Server 1
        </span>
        <span class="api-badge {{ !empty($vf['world_hero']) ? 'api-badge--on' : 'api-badge--off' }}">
            <i class="bi bi-{{ !empty($vf['world_hero']) ? 'check-circle-fill' : 'dash-circle' }}" aria-hidden="true"></i>
            World · Server 2
        </span>
    </div>

    <nav class="api-toc" aria-label="On this page">
        <a href="#api-credentials">Credentials</a>
        <a href="#api-balance">Balance</a>
        @if(!empty($vf['us1']))
            <a href="#api-usa">USA · Server 1</a>
        @endif
        @if(!empty($vf['world']))
            <a href="#api-world">World · Server 1</a>
        @endif
        <a href="#api-webhooks">Webhooks</a>
    </nav>

    <section id="api-credentials" class="api-card api-section">
        <h2 class="api-card__title">Credentials</h2>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @if (session('message'))
            <div class="alert alert-success" role="alert">{{ session('message') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        @endif

        <label class="api-field-label" for="api-key-field">API key</label>
        <div class="api-key-row">
            <input id="api-key-field" type="text" value="{{ $key }}" disabled readonly aria-label="Your API key">
            <a href="{{ url('/generate-token') }}" class="api-btn api-btn--dark">Generate new key</a>
        </div>

        <form action="{{ url('/set-webhook') }}" method="POST">
            @csrf
            <label class="api-field-label" for="api-webhook-in">Your webhook URL (optional)</label>
            <div class="api-key-row">
                <input id="api-webhook-in" type="url" name="webhook" class="form-control" style="flex:1 1 240px;border-radius:12px;padding:0.65rem 0.85rem;"
                       value="{{ $webhook_url }}" placeholder="https://yourdomain.com/your-webhook" autocomplete="url">
                <button type="submit" class="api-btn api-btn--primary">Save webhook</button>
            </div>
            <p class="api-callout mb-0">
                <strong>Outbound:</strong> When an SMS is received on a supported pool, we may POST a JSON payload to this URL (see <a href="#api-webhooks">Webhooks</a>).
            </p>
        </form>
    </section>

    <section id="api-balance" class="api-section">
        <div class="api-section__head">
            <h2 class="api-section__title">Wallet balance</h2>
        </div>
        <p class="api-section__note">Always available. <code>order_id</code> values returned from rent endpoints are your <strong>internal</strong> verification row IDs.</p>

        <div class="api-endpoint">
            <h3 class="api-endpoint__name"><span class="api-method">GET</span> Balance</h3>
            <div class="api-pre-wrap">
                <button type="button" class="api-copy" data-copy="{{ $b }}/api/balance?api_key={{ urlencode($key) }}&amp;action=balance">Copy</button>
                <pre><code>GET {{ $b }}/api/balance?api_key=YOUR_KEY&amp;action=balance</code></pre>
            </div>
            <div class="api-pre-wrap" style="margin-top:0.75rem;background:#1e293b;border-color:#334155">
                <pre><code>{
  "status": true,
  "main_balance": 12500.5
}</code></pre>
            </div>
        </div>
    </section>

    @if(!empty($vf['us1']))
    <section id="api-usa" class="api-section">
        <div class="api-section__head">
            <h2 class="api-section__title">USA · Server 1</h2>
        </div>
        <p class="api-section__note">USA Server 1 pool (retired). Use USA Server 2 or World endpoints instead.</p>

        <div class="api-endpoint">
            <h3 class="api-endpoint__name">1. List services</h3>
            <div class="api-pre-wrap">
                <button type="button" class="api-copy" data-copy="{{ $b }}/api/usa-services?api_key={{ urlencode($key) }}&amp;action=get-usa-services">Copy</button>
                <pre><code>GET {{ $b }}/api/usa-services?api_key=YOUR_KEY&amp;action=get-usa-services</code></pre>
            </div>
            <div class="api-pre-wrap" style="margin-top:0.75rem;background:#1e293b;border-color:#334155">
                <pre><code>{
  "status": true,
  "data": {
    "whatsapp": {
      "name": "WhatsApp",
      "count": 100,
      "repeatable": true,
      "service_key": "wa",
      "cost_ngn": 3950
    }
  }
}</code></pre>
            </div>
        </div>

        <div class="api-endpoint">
            <h3 class="api-endpoint__name">2. Rent number</h3>
            <div class="api-pre-wrap">
                <button type="button" class="api-copy" data-copy="{{ $b }}/api/rent-usa-number?api_key={{ urlencode($key) }}&amp;action=rent-usa-number&amp;service=WhatsApp&amp;service_key=wa">Copy</button>
                <pre><code>GET {{ $b }}/api/rent-usa-number?api_key=YOUR_KEY&amp;action=rent-usa-number&amp;service=WhatsApp&amp;service_key=wa</code></pre>
            </div>
            <div class="api-pre-wrap" style="margin-top:0.75rem;background:#1e293b;border-color:#334155">
                <pre><code>{
  "status": true,
  "order_id": 34941,
  "phone_no": "12178062080",
  "country": "USA",
  "service": "WhatsApp",
  "expires": 300
}</code></pre>
            </div>
        </div>

        <div class="api-endpoint">
            <h3 class="api-endpoint__name">3. Poll SMS</h3>
            <div class="api-pre-wrap">
                <pre><code>GET {{ $b }}/api/get-usa-sms?api_key=YOUR_KEY&amp;action=get-usa-sms&amp;order_id=INTERNAL_ID</code></pre>
            </div>
            <div class="api-pre-wrap" style="margin-top:0.75rem;background:#1e293b;border-color:#334155">
                <pre><code>{
  "status": true,
  "sms_status": "COMPLETED",
  "full_sms": "Your code is 123456",
  "code": "123456",
  "country": "USA",
  "service": "WhatsApp",
  "phone": "+12178062080"
}</code></pre>
            </div>
        </div>

        <div class="api-endpoint">
            <h3 class="api-endpoint__name">4. Cancel</h3>
            <div class="api-pre-wrap">
                <pre><code>GET {{ $b }}/api/cancel-usa-sms?api_key=YOUR_KEY&amp;action=cancel-usa-sms&amp;order_id=INTERNAL_ID</code></pre>
            </div>
            <div class="api-pre-wrap" style="margin-top:0.75rem;background:#1e293b;border-color:#334155">
                <pre><code>{ "status": true, "message": "ORDER CANCELLED AND REFUNDED" }</code></pre>
            </div>
        </div>
    </section>
    @else
    <section class="api-section">
        <div class="api-callout api-callout--amber">
            <strong>USA · Server 1</strong> is disabled for your installation. These endpoints are documented for when the server is enabled in admin settings.
        </div>
    </section>
    @endif

    @if(!empty($vf['us2']))
    <section class="api-section">
        <div class="api-section__head">
            <h2 class="api-section__title">USA · Server 1</h2>
        </div>
        <p class="api-section__note">
            There are <strong>no public REST routes</strong> for USA · Server 1 in this API. Use the web panel at <a href="{{ url('/usa2') }}">/usa2</a> to order numbers and receive SMS.
        </p>
    </section>
    @endif

    @if(!empty($vf['world']))
    <section id="api-world" class="api-section">
        <div class="api-section__head">
            <h2 class="api-section__title">World numbers · Server 1</h2>
        </div>
        <p class="api-section__note">
            These routes use the <strong>world server 1</strong> backend. Country is the short code (e.g. <code>US</code>); <code>service</code> is the service ID from the list.
        </p>

        <div class="api-endpoint">
            <h3 class="api-endpoint__name">1. Countries</h3>
            <div class="api-pre-wrap">
                <pre><code>GET {{ $b }}/api/get-world-countries?api_key=YOUR_KEY&amp;action=get-world-countries</code></pre>
            </div>
        </div>

        <div class="api-endpoint">
            <h3 class="api-endpoint__name">2. Services</h3>
            <div class="api-pre-wrap">
                <pre><code>GET {{ $b }}/api/get-world-services?api_key=YOUR_KEY&amp;action=get-world-services</code></pre>
            </div>
        </div>

        <div class="api-endpoint">
            <h3 class="api-endpoint__name">3. Check availability &amp; price</h3>
            <div class="api-pre-wrap">
                <pre><code>GET {{ $b }}/api/check-world-number-availability?api_key=YOUR_KEY&amp;action=check-availability&amp;country=US&amp;service=1012</code></pre>
            </div>
            <div class="api-pre-wrap" style="margin-top:0.75rem;background:#1e293b;border-color:#334155">
                <pre><code>{
  "status": true,
  "cost": 500,
  "stock": 8444,
  "country": "US",
  "service": "1012"
}</code></pre>
            </div>
        </div>

        <div class="api-endpoint">
            <h3 class="api-endpoint__name">4. Rent number</h3>
            <div class="api-pre-wrap">
                <pre><code>GET {{ $b }}/api/rent-world-number?api_key=YOUR_KEY&amp;action=rent-world-number&amp;country=US&amp;service=1012</code></pre>
            </div>
            <div class="api-pre-wrap" style="margin-top:0.75rem;background:#1e293b;border-color:#334155">
                <pre><code>{
  "status": true,
  "order_id": 389,
  "phone_no": "19362441517",
  "country": "US",
  "service": "1012",
  "expires": 300
}</code></pre>
            </div>
        </div>

        <div class="api-endpoint">
            <h3 class="api-endpoint__name">5. Poll SMS</h3>
            <div class="api-pre-wrap">
                <pre><code>GET {{ $b }}/api/get-world-sms?api_key=YOUR_KEY&amp;action=get-world-sms&amp;order_id=INTERNAL_ID</code></pre>
            </div>
        </div>

        <div class="api-endpoint">
            <h3 class="api-endpoint__name">6. Cancel</h3>
            <div class="api-pre-wrap">
                <pre><code>GET {{ $b }}/api/cancel-world-sms?api_key=YOUR_KEY&amp;action=cancel-world-sms&amp;order_id=INTERNAL_ID</code></pre>
            </div>
        </div>
    </section>
    @else
    <section class="api-section">
        <div class="api-callout api-callout--amber">
            <strong>World · Server 1</strong> is disabled. Enable it in admin settings to use these endpoints.
        </div>
    </section>
    @endif

    @if(!empty($vf['world_hero']))
    <section class="api-section">
        <div class="api-section__head">
            <h2 class="api-section__title">World · Server 2</h2>
        </div>
        <p class="api-section__note">
            World server 2 numbers are managed in the dashboard (<a href="{{ url('/world-sv2') }}">/world-sv2</a>). There is <strong>no separate REST bundle</strong> here yet.
            Configure your provider to POST incoming SMS to our inbound webhook (below); we update your order and optional outbound webhook.
        </p>
    </section>
    @endif

    <section id="api-webhooks" class="api-section">
        <div class="api-section__head">
            <h2 class="api-section__title">Webhooks</h2>
        </div>

        <div class="api-endpoint">
            <h3 class="api-endpoint__name">Inbound webhook → your app</h3>
            <p class="api-section__note mb-2">Set this URL in your provider dashboard so we receive OTP payloads.</p>
            <div class="api-pre-wrap">
                <button type="button" class="api-copy" data-copy="{{ $b }}/api/world-sms-webhook">Copy</button>
                <pre><code>POST {{ $b }}/api/world-sms-webhook
Content-Type: application/json</code></pre>
            </div>
            <div class="api-pre-wrap" style="margin-top:0.75rem;background:#1e293b;border-color:#334155">
                <pre><code>{
  "activationId": "123456",
  "service": "tg",
  "text": "Your code is 12345",
  "code": "12345",
  "country": 2,
  "receivedAt": "2025-12-16T10:30:00.000000Z"
}</code></pre>
            </div>
            <p class="api-callout"><code>activationId</code> must match the provider activation id stored as <code>order_id</code> on the verification row.</p>
        </div>

        <div class="api-endpoint">
            <h3 class="api-endpoint__name">Outbound — us → your server</h3>
            <p class="api-section__note mb-2">When you save a webhook URL above, we may notify you with:</p>
            <div class="api-pre-wrap" style="background:#1e293b;border-color:#334155">
                <pre><code>{
  "phone": "+1234567890",
  "code": "12345",
  "service": "WhatsApp",
  "order_id": 2217,
  "full_sms": "Your code is 12345",
  "country": "US"
}</code></pre>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.querySelector('.api-shell')?.addEventListener('click', function (e) {
    var btn = e.target.closest('.api-copy[data-copy]');
    if (!btn) return;
    var t = btn.getAttribute('data-copy');
    if (!t) return;
    e.preventDefault();
    navigator.clipboard.writeText(t).then(function () {
        var prev = btn.textContent;
        btn.textContent = 'Copied';
        setTimeout(function () { btn.textContent = prev; }, 1600);
    });
});
</script>
@endpush
