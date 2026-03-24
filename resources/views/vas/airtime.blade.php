@extends('layout.dashboard-modern')

@section('title', 'Airtime')

@push('styles')
    <link rel="stylesheet" href="{{ url('') }}/public/css/fund-wallet.css">
    <link rel="stylesheet" href="{{ url('') }}/public/css/vtu-bills.css">
@endpush

@section('content')
    <div class="vb-page fw-page">
        <header class="fw-hero">
            <div>
                <p class="fw-hero__user">{{ Auth::user()->username }}</p>
                <p class="fw-hero__bal">₦{{ number_format(Auth::user()->wallet ?? 0, 2) }}</p>
                <p class="fw-hero__label">Wallet (debited on purchase)</p>
            </div>
            <a href="{{ url('/home') }}" class="fw-hero__back">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Dashboard
            </a>
        </header>

        @include('vas.partials.subnav')

        @if (!$vasConfigured)
            <div class="vb-note" role="note">
                <span class="vb-note__icon"><i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i></span>
                <div>
                    <strong>Setup required.</strong> Add <code class="vb-json-hint">WEBKEY</code> and
                    <code class="vb-json-hint">SPRINTPAY_WEBHOOK_SECRET</code> to your environment. Purchases call SprintPay
                    <code class="vb-json-hint">POST /api/merchant/vas/buy-ng-airtime</code> from this server only.
                </div>
            </div>
        @endif

        <div class="fw-alerts">
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session()->has('message'))
                <div class="alert alert-success" role="alert">{{ session()->get('message') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger" role="alert">{{ session()->get('error') }}</div>
            @endif
        </div>

        <article class="fw-card">
            <div class="fw-card__head">
                <h2 class="fw-card__title">Nigeria airtime (VTU)</h2>
                <p class="fw-card__sub">Network, phone number, and amount in NGN</p>
            </div>
            <div class="fw-card__body">
                <form action="{{ route('vas.airtime.buy') }}" method="POST" class="vb-stack" id="vb-airtime-form">
                    @csrf
                    <div>
                        <label class="fw-label" for="vb-air-network">Network</label>
                        <select id="vb-air-network" name="service_id" class="fw-select" required {{ $vasConfigured ? '' : 'disabled' }}>
                            @foreach ($networks as $n)
                                <option value="{{ $n['value'] }}">{{ $n['label'] }}</option>
                            @endforeach
                        </select>
                        <div class="vb-operators" aria-label="Supported mobile operators">
                            <span class="vb-operator"><img src="{{ url('') }}/public/images/operators/mtn.png" alt="MTN logo" loading="lazy"></span>
                            <span class="vb-operator"><img src="{{ url('') }}/public/images/operators/glo.png" alt="Glo logo" loading="lazy"></span>
                            <span class="vb-operator"><img src="{{ url('') }}/public/images/operators/airtel.png" alt="Airtel logo" loading="lazy"></span>
                            <span class="vb-operator"><img src="{{ url('') }}/public/images/operators/9mobile.png" alt="9mobile logo" loading="lazy"></span>
                        </div>
                    </div>
                    <div>
                        <label class="fw-label" for="vb-air-phone">Phone</label>
                        <input id="vb-air-phone" type="text" name="phone" class="fw-input" inputmode="tel" autocomplete="tel"
                               placeholder="08012345678" required {{ $vasConfigured ? '' : 'disabled' }}>
                    </div>
                    <div>
                        <label class="fw-label" for="vb-air-amt">Amount (₦)</label>
                        <input id="vb-air-amt" type="number" name="amount" class="fw-input" min="50" max="100000" step="1"
                               placeholder="500" required {{ $vasConfigured ? '' : 'disabled' }}>
                        <p class="vb-muted">Minimum ₦50. Must not exceed your available wallet balance.</p>
                    </div>
                    <button type="submit" class="fw-submit" id="vb-airtime-submit" {{ $vasConfigured ? '' : 'disabled' }}>Buy airtime</button>
                </form>
            </div>
        </article>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var form = document.getElementById('vb-airtime-form');
            var btn = document.getElementById('vb-airtime-submit');
            if (!form || !btn) return;
            form.addEventListener('submit', function () {
                btn.disabled = true;
                btn.classList.add('vb-submit-loading');
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...';
            });
        })();
    </script>
@endpush
