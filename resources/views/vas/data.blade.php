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
                            <input id="vb-data-search" type="search" class="fw-input" placeholder="Search bundles..." autocomplete="off" {{ $vasConfigured ? '' : 'disabled' }}>
                            <div class="vb-chip-row" id="vb-data-filters">
                                <button type="button" class="vb-chip vb-chip--on" data-cat="all">All</button>
                                <button type="button" class="vb-chip" data-cat="daily">Daily</button>
                                <button type="button" class="vb-chip" data-cat="weekly">Weekly</button>
                                <button type="button" class="vb-chip" data-cat="monthly">Monthly</button>
                                <button type="button" class="vb-chip" data-cat="yearly">Yearly</button>
                            </div>
                            <div id="vb-data-bundle-list" class="vb-bundle-grid" aria-live="polite"></div>
                            <select id="vb-data-bundle" class="fw-select d-none" disabled>
                                <option value="">Load bundles first</option>
                            </select>
                            <input type="hidden" name="variation_code" id="vb-data-variation-code" value="" required>
                            <p id="vb-data-bundle-hint" class="vb-muted mb-0">Choose network to load bundles automatically.</p>
                        </div>
                    </div>
                    <div class="vb-actions">
                        <span class="vb-loading d-none" id="vb-data-loading" aria-live="polite">Loading bundles…</span>
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
                               placeholder="08012345678" minlength="11" maxlength="11" pattern="\d{11}" required {{ $vasConfigured ? '' : 'disabled' }}>
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
                const loading = document.getElementById('vb-data-loading');
                const hint = document.getElementById('vb-data-bundle-hint');
                const filters = document.getElementById('vb-data-filters');
                const searchEl = document.getElementById('vb-data-search');
                const grid = document.getElementById('vb-data-bundle-list');
                const phoneEl = document.getElementById('vb-data-phone');

                const logoMap = {
                    mtn: @json(url('') . '/public/images/operators/mtn.png'),
                    glo: @json(url('') . '/public/images/operators/glo.png'),
                    airtel: @json(url('') . '/public/images/operators/airtel.png'),
                    '9mobile': @json(url('') . '/public/images/operators/9mobile.png')
                };

                if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
                    jQuery(net).select2({
                        width: '100%',
                        templateResult: function (state) {
                            if (!state.id) return state.text;
                            var key = String(state.id || '').toLowerCase();
                            var src = logoMap[key] || null;
                            if (!src) return state.text;
                            var $row = jQuery('<span class="vb-net-opt"></span>');
                            $row.append(jQuery('<img class="vb-net-opt__img" alt="">').attr('src', src));
                            $row.append(jQuery('<span class="vb-net-opt__txt"></span>').text(state.text));
                            return $row;
                        },
                        templateSelection: function (state) {
                            return state.text || '';
                        },
                        minimumResultsForSearch: Infinity
                    });
                }

                var allRows = [];
                var activeCat = 'all';

                function classifyBundle(name, code) {
                    var txt = ((name || '') + ' ' + (code || '')).toLowerCase();
                    if (txt.indexOf('year') !== -1 || txt.indexOf('annual') !== -1 || txt.indexOf('365') !== -1) return 'yearly';
                    if (txt.indexOf('month') !== -1 || txt.indexOf('30 day') !== -1) return 'monthly';
                    if (txt.indexOf('week') !== -1 || txt.indexOf('7 day') !== -1) return 'weekly';
                    if (txt.indexOf('day') !== -1 || txt.indexOf('daily') !== -1 || txt.indexOf('24h') !== -1) return 'daily';
                    return 'all';
                }

                function pickBundle(code, amount, name) {
                    hidCode.value = code || '';
                    if (amtEl && amount > 0) {
                        amtEl.value = String(Math.round(amount));
                    }
                    Array.prototype.forEach.call(grid.querySelectorAll('.vb-bundle-card'), function (card) {
                        card.classList.toggle('vb-bundle-card--active', card.getAttribute('data-code') === code);
                    });
                    hint.textContent = 'Selected: ' + (name || code || 'bundle');
                }

                function renderBundles() {
                    var q = (searchEl.value || '').trim().toLowerCase();
                    var rows = allRows.filter(function (row) {
                        var byCat = activeCat === 'all' ? true : row.category === activeCat;
                        if (!byCat) return false;
                        if (!q) return true;
                        return (row.name + ' ' + row.code + ' ' + row.amount).toLowerCase().indexOf(q) !== -1;
                    });

                    grid.innerHTML = '';
                    if (!rows.length) {
                        grid.innerHTML = '<p class="vb-muted mb-0">No bundle matches this filter.</p>';
                        return;
                    }

                    rows.forEach(function (row) {
                        var btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'vb-bundle-card';
                        btn.setAttribute('data-code', row.code);
                        btn.innerHTML =
                            '<span class="vb-bundle-card__name">' + row.name + '</span>' +
                            '<span class="vb-bundle-card__meta">' + (row.category === 'all' ? 'Bundle' : row.category.charAt(0).toUpperCase() + row.category.slice(1)) + '</span>' +
                            '<span class="vb-bundle-card__amt">' + (row.amount ? ('₦' + Number(row.amount).toLocaleString()) : 'Set amount') + '</span>';
                        btn.addEventListener('click', function () {
                            pickBundle(row.code, row.amount, row.name);
                        });
                        grid.appendChild(btn);
                    });
                }

                function setLoading(on) {
                    loading.classList.toggle('d-none', !on);
                }

                function loadBundlesForNetwork(networkValue) {
                    const n = networkValue || net.value;
                    bundle.innerHTML = '<option value="">Loading…</option>';
                    bundle.disabled = true;
                    hidCode.value = '';
                    if (amtEl) amtEl.value = '';
                    allRows = [];
                    grid.innerHTML = '';
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
                        allRows = rows.map(function (row) {
                            return {
                                code: row.code,
                                name: row.name,
                                amount: row.amount || 0,
                                category: classifyBundle(row.name, row.code)
                            };
                        });
                        if (!rows.length) {
                            bundle.innerHTML = '<option value="">No bundles (check API response)</option>';
                            hint.textContent = 'SprintPay returned no bundles for this network. Try another or check service_id names.';
                        } else {
                            bundle.disabled = false;
                            hint.textContent = 'Select a bundle below; amount is set from the catalogue when available.';
                            renderBundles();
                            var first = allRows[0];
                            if (first) {
                                pickBundle(first.code, first.amount, first.name);
                            }
                        }
                    }).catch(function () {
                        bundle.innerHTML = '<option value="">Failed to load</option>';
                        hint.textContent = 'Network error loading bundles.';
                    }).finally(function () { setLoading(false); });
                }

                function onNetworkChanged() {
                    hidCode.value = '';
                    loadBundlesForNetwork(net.value);
                }

                if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
                    jQuery(net).on('select2:select', onNetworkChanged);
                } else {
                    net.addEventListener('change', onNetworkChanged);
                }

                filters.addEventListener('click', function (e) {
                    var btn = e.target.closest('.vb-chip[data-cat]');
                    if (!btn) return;
                    activeCat = btn.getAttribute('data-cat') || 'all';
                    Array.prototype.forEach.call(filters.querySelectorAll('.vb-chip'), function (x) {
                        x.classList.toggle('vb-chip--on', x === btn);
                    });
                    renderBundles();
                });

                searchEl.addEventListener('input', renderBundles);
                if (phoneEl) {
                    phoneEl.addEventListener('input', function () {
                        phoneEl.value = String(phoneEl.value || '').replace(/\D/g, '').slice(0, 11);
                    });
                }

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
                        alert('Choose a bundle before buying.');
                        return;
                    }
                    const submitBtn = document.getElementById('vb-data-submit');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('vb-submit-loading');
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...';
                    }
                });

                loadBundlesForNetwork(net.value);
            })();
        </script>
    @endpush
@endif
