@extends('layout.dashboard-modern')

@section('title', 'Data bundles')

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
                    <strong>Setup required.</strong> Configure <code class="vb-json-hint">WEBKEY</code> and
                    <code class="vb-json-hint">SPRINTPAY_WEBHOOK_SECRET</code>. Bundle list loads from SprintPay
                    <code class="vb-json-hint">GET /api/get-data-variations</code>.
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
                <h2 class="fw-card__title">Data bundle</h2>
                <p class="fw-card__sub">Pick network, load bundles, then enter the receiving line</p>
            </div>
            <div class="fw-card__body">
                <form action="{{ route('vas.data.buy') }}" method="POST" class="vb-stack" id="vb-data-form">
                    @csrf
                    <div class="vb-stack vb-stack--2">
                        <div>
                            <label class="fw-label" for="vb-data-network">Network</label>
                            <select id="vb-data-network" name="service_id" class="fw-select" required {{ $vasConfigured ? '' : 'disabled' }}>
                                @foreach ($networks as $n)
                                    <option value="{{ $n['value'] }}">{{ $n['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="fw-label" for="vb-data-bundle">Bundle</label>
                            <select id="vb-data-bundle" class="fw-select" required disabled>
                                <option value="">Load bundles first</option>
                            </select>
                            <input type="hidden" name="variation_code" id="vb-data-variation-code" value="" required>
                            <p id="vb-data-bundle-hint" class="vb-muted mb-0">Choose network, then click “Load bundles”.</p>
                        </div>
                    </div>
                    <div class="vb-actions">
                        <button type="button" class="vb-btn-ghost" id="vb-data-load" {{ $vasConfigured ? '' : 'disabled' }}>Load bundles</button>
                        <span class="vb-loading d-none" id="vb-data-loading" aria-live="polite">Loading…</span>
                    </div>
                    <div>
                        <label class="fw-label" for="vb-data-amount">Amount to debit (₦)</label>
                        <input id="vb-data-amount" type="number" name="amount" class="fw-input" min="1" max="500000" step="1"
                               placeholder="e.g. 500" required {{ $vasConfigured ? '' : 'disabled' }}>
                        <p class="vb-muted mb-0">Prefilled from the bundle when the catalogue includes a price; you can adjust if your provider uses a different shape.</p>
                    </div>
                    <div>
                        <label class="fw-label" for="vb-data-phone">Phone</label>
                        <input id="vb-data-phone" type="text" name="phone" class="fw-input" inputmode="tel" autocomplete="tel"
                               placeholder="08012345678" required {{ $vasConfigured ? '' : 'disabled' }}>
                    </div>
                    <button type="submit" class="fw-submit" id="vb-data-submit" {{ $vasConfigured ? '' : 'disabled' }}>Buy data</button>
                </form>
            </div>
        </article>
    </div>
@endsection

@if ($vasConfigured)
    @push('scripts')
        <script>
            (function () {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const urlTpl = @json(url('/vas/catalog/data-variations')) + '?network=';

                function pickVariations(payload) {
                    if (!payload || typeof payload !== 'object') return [];
                    const d = payload.data ?? payload.content ?? payload;
                    let arr = d.variations ?? d.content ?? (Array.isArray(d) ? d : []);
                    if (!Array.isArray(arr)) return [];
                    return arr.map(function (v) {
                        const code = v.variation_code || v.code || v.billersCode || '';
                        const name = v.name || v.title || v.variation_name || code;
                        const amt = parseFloat(v.variation_amount || v.amount || v.fixedPrice || v.price || 0) || 0;
                        return { code: String(code), name: String(name), amount: amt };
                    }).filter(function (x) { return x.code; });
                }

                const net = document.getElementById('vb-data-network');
                const bundle = document.getElementById('vb-data-bundle');
                const hidCode = document.getElementById('vb-data-variation-code');
                const amtEl = document.getElementById('vb-data-amount');
                const loadBtn = document.getElementById('vb-data-load');
                const loading = document.getElementById('vb-data-loading');
                const hint = document.getElementById('vb-data-bundle-hint');

                function setLoading(on) {
                    loadBtn.disabled = on;
                    loading.classList.toggle('d-none', !on);
                }

                loadBtn.addEventListener('click', function () {
                    const n = net.value;
                    bundle.innerHTML = '<option value="">Loading…</option>';
                    bundle.disabled = true;
                    hidCode.value = '';
                    if (amtEl) amtEl.value = '';
                    hint.textContent = 'Fetching catalogue…';
                    setLoading(true);
                    fetch(urlTpl + encodeURIComponent(n), {
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }
                    }).then(function (r) { return r.json(); }).then(function (j) {
                        const rows = pickVariations(j);
                        bundle.innerHTML = '<option value="">Select a bundle</option>';
                        rows.forEach(function (row, idx) {
                            const opt = document.createElement('option');
                            opt.value = String(idx);
                            opt.textContent = row.name + (row.amount ? ' — ₦' + row.amount.toLocaleString() : '');
                            opt.dataset.code = row.code;
                            opt.dataset.amount = row.amount || '';
                            bundle.appendChild(opt);
                        });
                        if (!rows.length) {
                            bundle.innerHTML = '<option value="">No bundles (check API response)</option>';
                            hint.textContent = 'SprintPay returned no bundles for this network. Try another or check service_id names.';
                        } else {
                            bundle.disabled = false;
                            hint.textContent = 'Select a bundle; amount is set from the catalogue when available.';
                        }
                    }).catch(function () {
                        bundle.innerHTML = '<option value="">Failed to load</option>';
                        hint.textContent = 'Network error loading bundles.';
                    }).finally(function () { setLoading(false); });
                });

                bundle.addEventListener('change', function () {
                    const opt = bundle.selectedOptions[0];
                    if (!opt || !opt.dataset.code) {
                        hidCode.value = '';
                        return;
                    }
                    hidCode.value = opt.dataset.code;
                    const a = parseFloat(opt.dataset.amount || '0');
                    if (amtEl && a > 0) {
                        amtEl.value = String(Math.round(a));
                    }
                    if (!(a > 0)) {
                        hint.textContent = 'Amount not in catalogue — enter the debit amount manually.';
                    } else {
                        hint.textContent = 'Debit amount: ₦' + a.toLocaleString() + ' (editable).';
                    }
                });

                document.getElementById('vb-data-form').addEventListener('submit', function (e) {
                    if (!hidCode.value) {
                        e.preventDefault();
                        alert('Choose a bundle (load bundles and select one).');
                        return;
                    }
                    const submitBtn = document.getElementById('vb-data-submit');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('vb-submit-loading');
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...';
                    }
                });
            })();
        </script>
    @endpush
@endif
