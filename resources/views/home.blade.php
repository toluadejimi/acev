@extends('layout.dashboard-modern')
@section('title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ url('') }}/public/css/home-dashboard.css?v=6">
@endpush

@section('content')

@php
    $h = (int) date('G');
    $hmGreet = $h < 12 ? 'Good morning' : ($h < 17 ? 'Good afternoon' : 'Good evening');
@endphp

<div class="hm-shell">

    <header class="hm-hero">
        <div class="hm-hero__meta">
            <p class="hm-hero__kicker">Dashboard</p>
            <h1 class="hm-hero__greet">{{ $hmGreet }}, <span>{{ Auth::user()->username }}</span></h1>
            <p class="hm-hero__date"><i class="bi bi-calendar3 hm-hero__date-ico" aria-hidden="true"></i>{{ now()->format('l · j F Y') }}</p>
        </div>
        <div class="hm-hero__balance-block">
            <p class="hm-hero__bal-label">Available balance</p>
            <p class="hm-hero__bal">₦{{ number_format(Auth::user()->wallet ?? 0, 2) }}</p>
        </div>
        <div class="hm-hero__actions">
            <a href="{{ url('/fund-wallet') }}" class="hm-btn hm-btn--primary">
                <i class="bi bi-plus-lg"></i> Fund wallet
            </a>
            <a href="{{ route('verification.index') }}" class="hm-btn hm-btn--ghost">
                <i class="bi bi-shield-check"></i> SMS verification
            </a>
        </div>
    </header>

    <div class="hm-alerts">
        @if ($errors->any())
            <div class="hm-alert hm-alert--danger" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session()->has('message'))
            <div class="hm-alert hm-alert--success" role="status">{{ session()->get('message') }}</div>
        @endif
        @if (session()->has('error'))
            <div class="hm-alert hm-alert--danger" role="alert">{{ session()->get('error') }}</div>
        @endif
    </div>

    <div class="hm-bento">
        <section class="hm-panel hm-panel--wide" aria-labelledby="hm-vtu-title">
            <div class="hm-panel__head">
                <p class="hm-panel__eyebrow">VTU &amp; bills</p>
                <h2 id="hm-vtu-title" class="hm-panel__title">Quick top-ups</h2>
                <p class="hm-panel__sub">Airtime, data, TV, and electricity — paid from your wallet.</p>
            </div>
            <div class="hm-tiles">
                @foreach($vtuQuickLinks ?? [] as $vtu)
                    <a href="{{ $vtu['url'] }}" class="hm-tile hm-tile--{{ $vtu['key'] }}{{ empty($vtu['active']) ? ' hm-tile--fallback' : '' }}">
                        <span class="hm-tile__icon" aria-hidden="true">
                            @if($vtu['key'] === 'airtime')
                                <i class="bi bi-phone"></i>
                            @elseif($vtu['key'] === 'data')
                                <i class="bi bi-wifi"></i>
                            @elseif($vtu['key'] === 'cable')
                                <i class="bi bi-tv"></i>
                            @else
                                <i class="bi bi-lightning-charge-fill"></i>
                            @endif
                        </span>
                        <span class="hm-tile__label">{{ $vtu['label'] }}</span>
                        <span class="hm-tile__hint">{{ !empty($vtu['active']) ? 'Buy' : 'Wallet' }}</span>
                        <span class="hm-tile__go">Continue <i class="bi bi-arrow-right"></i></span>
                    </a>
                @endforeach
            </div>
        </section>

        <aside class="hm-panel hm-spotlight" aria-labelledby="hm-sms-title">
            <div class="hm-spotlight__inner">
                <span class="hm-spotlight__badge">OTP &amp; SMS</span>
                <h2 id="hm-sms-title" class="hm-spotlight__title">Verification numbers</h2>
                <p class="hm-spotlight__text">US servers or worldwide coverage. Rent a line, receive codes in your dashboard — no extra SIM.</p>
                <ul class="hm-spotlight__list">
                    <li>USA SV1 &amp; SV2 pools</li>
                    <li>Global numbers</li>
                    <li>Live code polling</li>
                </ul>
                <a href="{{ route('verification.index') }}" class="hm-spotlight__btn">
                    Open verification hub <i class="bi bi-arrow-up-right ms-1"></i>
                </a>
            </div>
        </aside>
    </div>

    <div class="hm-promos">
        <a href="https://aceboosts.com" target="_blank" rel="noopener" class="hm-promo hm-promo--b">
            <span class="hm-promo__tag" aria-hidden="true"><i class="bi bi-graph-up-arrow"></i></span>
            <div>
                <p class="hm-promo__title">Grow engagement</p>
                <p class="hm-promo__sub">Followers &amp; likes</p>
                <span class="hm-promo__cta">Boost <i class="bi bi-arrow-up-right"></i></span>
            </div>
        </a>
    </div>

</div>

@endsection

@push('scripts')
<script>
    $.ajaxSetup({
        headers: {
            'Authorization': 'Bearer ' + (localStorage.getItem('access_token') || '')
        }
    });
    $.get('/api/user', function (response) {
        console.log(response);
    });
</script>
@endpush
