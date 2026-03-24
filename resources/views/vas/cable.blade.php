@extends('layout.dashboard-modern')

@section('title', 'Cable TV')

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
                    <code class="vb-json-hint">SPRINTPAY_WEBHOOK_SECRET</code>. Calls use
                    <code class="vb-json-hint">merchant/vas/validate-cable</code> and
                    <code class="vb-json-hint">merchant/vas/buy-cable</code>.
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
                <h2 class="fw-card__title">Pay TV</h2>
                <p class="fw-card__sub">DSTV, GOtv, StarTimes, etc. Validate the smartcard, then pay.</p>
            </div>
            <div class="fw-card__body">
                <div class="vb-actions mb-3">
                    <button type="button" class="vb-btn-ghost" id="vb-cable-load" {{ $vasConfigured ? '' : 'disabled' }}>Reload catalogue</button>
                    <span class="vb-loading d-none" id="vb-cable-loading">Loading…</span>
                </div>
                <pre class="vb-json-hint mb-3 p-2 bg-light rounded d-none" id="vb-cable-debug" style="max-height:120px;overflow:auto;"></pre>

                <form action="{{ route('vas.cable.buy') }}" method="POST" class="vb-stack" id="vb-cable-form">
                    @csrf
                    <input type="hidden" name="service_id" id="vb-cable-sid-h" value="" required>
                    <div class="vb-stack vb-stack--2">
                        <div>
                            <label class="fw-label" for="vb-cable-service">Provider</label>
                            <select id="vb-cable-service" class="fw-select" {{ $vasConfigured ? '' : 'disabled' }}>
                                <option value="">Loading catalogue…</option>
                            </select>
                        </div>
                        <div>
                            <label class="fw-label" for="vb-cable-variation">Plan</label>
                            <select id="vb-cable-variation" class="fw-select" disabled>
                                <option value="">Load providers first</option>
                            </select>
                            <input type="hidden" name="variation_code" id="vb-cable-variation-hidden" value="" required>
                        </div>
                    </div>
                    <div>
                        <label class="fw-label" for="vb-cable-smart">Smartcard / IUC number</label>
                        <input id="vb-cable-smart" type="text" name="billersCode" class="fw-input" required
                               autocomplete="off" {{ $vasConfigured ? '' : 'disabled' }}>
                    </div>
                    <div class="vb-actions">
                        <button type="button" class="vb-btn-ghost" id="vb-cable-validate" {{ $vasConfigured ? '' : 'disabled' }}>Validate smartcard</button>
                    </div>
                    <div id="vb-cable-validate-out" class="alert alert-secondary d-none py-2 small" role="status"></div>
                    <div class="vb-stack vb-stack--2">
                        <div>
                            <label class="fw-label" for="vb-cable-amt">Amount to debit (₦)</label>
                            <input id="vb-cable-amt" type="number" name="amount" class="fw-input" min="1" max="500000" step="1" required
                                   {{ $vasConfigured ? '' : 'disabled' }}>
                        </div>
                        <div>
                            <label class="fw-label" for="vb-cable-phone">Phone (optional)</label>
                            <input id="vb-cable-phone" type="text" name="phone" class="fw-input" inputmode="tel" autocomplete="tel"
                                   placeholder="08012345678" {{ $vasConfigured ? '' : 'disabled' }}>
                        </div>
                    </div>
                    <button type="submit" class="fw-submit" id="vb-cable-submit" {{ $vasConfigured ? '' : 'disabled' }}>Pay cable bill</button>
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
                const plansUrl = @json(url('/vas/catalog/cable-plans'));
                const validateUrl = @json(url('/vas/cable/validate'));

                /** @type {{ id: string, label: string, variations: { code: string, name: string, amount: number|null }[] }[]} */
                let providerModels = [];

                function serviceIdOf(row) {
                    return String(row.serviceID || row.service_id || row.id || row.slug || '').toLowerCase().trim();
                }

                const CABLE_META_KEYS = new Set([
                    'status', 'success', 'message', 'msg', 'data', 'content', 'error', 'errors',
                    'response_code', 'raw', 'code', 'response_description'
                ]);

                function normalizeVariationList(raw) {
                    if (!Array.isArray(raw)) return [];
                    return raw.map(function (v) {
                        if (!v || typeof v !== 'object') return null;
                        const code = String(v.variation_code || v.code || v.product_code || v.billersCode || '').trim();
                        if (!code) return null;
                        const name = v.name || v.title || v.variation_name || code;
                        const amt = v.variation_amount ?? v.amount ?? v.price;
                        const amount = amt !== undefined && amt !== null && amt !== '' ? parseFloat(String(amt).replace(/,/g, '')) : null;
                        return {
                            code: code,
                            name: String(name),
                            amount: amount !== null && !isNaN(amount) && amount > 0 ? amount : null
                        };
                    }).filter(Boolean);
                }

                function cablePayloadRoots(payload) {
                    const roots = [payload];
                    if (payload.data && typeof payload.data === 'object' && !Array.isArray(payload.data)) {
                        roots.push(payload.data);
                    }
                    return roots;
                }

                function extractVariationsFromPayload(payload, serviceIdHint) {
                    if (!payload || typeof payload !== 'object') return [];
                    const buckets = [
                        payload.variations,
                        payload.content,
                        payload.data,
                        payload.data && payload.data.content,
                        payload.data && payload.data.variations,
                        payload.content && payload.content.variations
                    ];
                    for (let i = 0; i < buckets.length; i++) {
                        const b = buckets[i];
                        if (Array.isArray(b)) {
                            const v = normalizeVariationList(b);
                            if (v.length) return v;
                        }
                        if (b && typeof b === 'object' && Array.isArray(b.variations)) {
                            const v = normalizeVariationList(b.variations);
                            if (v.length) return v;
                        }
                    }

                    const roots = cablePayloadRoots(payload);
                    const hint = serviceIdHint ? String(serviceIdHint).toLowerCase().trim() : '';
                    if (hint) {
                        for (let ri = 0; ri < roots.length; ri++) {
                            const root = roots[ri];
                            for (const key of Object.keys(root)) {
                                if (CABLE_META_KEYS.has(key.toLowerCase())) continue;
                                if (key.toLowerCase() !== hint) continue;
                                const val = root[key];
                                if (Array.isArray(val)) {
                                    const v = normalizeVariationList(val);
                                    if (v.length) return v;
                                }
                            }
                        }
                    }

                    for (let ri = 0; ri < roots.length; ri++) {
                        const root = roots[ri];
                        for (const key of Object.keys(root)) {
                            if (CABLE_META_KEYS.has(key.toLowerCase())) continue;
                            const val = root[key];
                            if (Array.isArray(val)) {
                                const v = normalizeVariationList(val);
                                if (v.length) return v;
                            }
                        }
                    }
                    return [];
                }

                function buildProviderModels(payload) {
                    const out = [];
                    const seen = new Set();

                    function pushProvider(id, label, variations) {
                        id = String(id || '').toLowerCase().trim();
                        if (!id || seen.has(id)) return;
                        seen.add(id);
                        out.push({
                            id: id,
                            label: label || id,
                            variations: normalizeVariationList(variations || [])
                        });
                    }

                    if (!payload || typeof payload !== 'object') return out;

                    /* Cable catalogue shape: { "status": true, "dstv": [ { variation_code, name, ... } ], ... } */
                    function addProvidersFromRoot(obj) {
                        if (!obj || typeof obj !== 'object' || Array.isArray(obj)) return;
                        Object.keys(obj).forEach(function (key) {
                            if (CABLE_META_KEYS.has(key.toLowerCase())) return;
                            const val = obj[key];
                            if (!Array.isArray(val)) return;
                            const vars = normalizeVariationList(val);
                            if (!vars.length) return;
                            const id = key.toLowerCase();
                            const label = id.length <= 5 ? id.toUpperCase() : id.charAt(0).toUpperCase() + id.slice(1);
                            pushProvider(id, label, val);
                        });
                    }

                    addProvidersFromRoot(payload);
                    if (!out.length && payload.data && typeof payload.data === 'object') {
                        addProvidersFromRoot(payload.data);
                    }
                    if (out.length) return out;

                    const content = payload.content !== undefined ? payload.content : (payload.data && payload.data.content !== undefined ? payload.data.content : null);
                    const dataArr = Array.isArray(payload.data) ? payload.data : null;

                    if (Array.isArray(content)) {
                        content.forEach(function (row) {
                            if (!row || typeof row !== 'object') return;
                            const id = serviceIdOf(row);
                            if (!id) return;
                            const vars = row.variations || row.plans || row.options || row.products || [];
                            pushProvider(id, row.name || row.title || id, Array.isArray(vars) ? vars : []);
                        });
                        if (out.length) return out;
                    }

                    if (content && typeof content === 'object' && !Array.isArray(content)) {
                        Object.keys(content).forEach(function (key) {
                            const block = content[key];
                            if (Array.isArray(block)) {
                                block.forEach(function (row) {
                                    if (row && typeof row === 'object' && (row.variation_code || row.code)) return;
                                    const id = serviceIdOf(row) || key.toLowerCase();
                                    const vars = row && row.variations ? row.variations : [];
                                    pushProvider(id, (row && (row.name || row.title)) || key, Array.isArray(vars) ? vars : []);
                                });
                            } else if (block && typeof block === 'object') {
                                const id = serviceIdOf(block) || key.toLowerCase();
                                const vars = block.variations || block.plans || [];
                                pushProvider(id, block.name || block.title || key, Array.isArray(vars) ? vars : []);
                            }
                        });
                        if (out.length) return out;
                    }

                    const list = Array.isArray(content) ? content : (dataArr || []);
                    if (Array.isArray(list)) {
                        list.forEach(function (row) {
                            if (!row || typeof row !== 'object') return;
                            if (row.variation_code || row.code) return;
                            const id = serviceIdOf(row);
                            if (id) pushProvider(id, row.name || row.title || id, []);
                        });
                    }

                    return out;
                }

                function plansUrlForService(sid) {
                    return plansUrl + '?service_id=' + encodeURIComponent(sid) + '&serviceID=' + encodeURIComponent(sid);
                }

                function fetchVariationsForService(sid) {
                    return fetch(plansUrlForService(sid), { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } })
                        .then(function (r) { return r.json(); })
                        .then(function (j) { return extractVariationsFromPayload(j, sid); });
                }

                const svcSel = document.getElementById('vb-cable-service');
                const varSel = document.getElementById('vb-cable-variation');
                const varHidden = document.getElementById('vb-cable-variation-hidden');
                const amtInput = document.getElementById('vb-cable-amt');
                const loadBtn = document.getElementById('vb-cable-load');
                const loading = document.getElementById('vb-cable-loading');
                const debug = document.getElementById('vb-cable-debug');

                function setLoading(on) {
                    loadBtn.disabled = on;
                    loading.classList.toggle('d-none', !on);
                }

                function syncVariationHidden() {
                    const opt = varSel.selectedOptions[0];
                    varHidden.value = opt && opt.dataset.code ? opt.dataset.code : '';
                    if (opt && opt.dataset.amount && amtInput && !amtInput.dataset.userEdited) {
                        const a = parseFloat(opt.dataset.amount);
                        if (a > 0) amtInput.value = String(Math.round(a * 100) / 100);
                    }
                }

                function fillProviderSelect() {
                    svcSel.innerHTML = '<option value="">Select provider</option>';
                    providerModels.forEach(function (p) {
                        const opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = p.label;
                        svcSel.appendChild(opt);
                    });
                }

                function fillVariationSelect(variations) {
                    varSel.innerHTML = '<option value="">Select plan</option>';
                    varHidden.value = '';
                    variations.forEach(function (v) {
                        const opt = document.createElement('option');
                        opt.value = v.code;
                        opt.dataset.code = v.code;
                        if (v.amount) opt.dataset.amount = String(v.amount);
                        opt.textContent = v.name + (v.amount ? ' — ₦' + v.amount.toLocaleString() : '');
                        varSel.appendChild(opt);
                    });
                    varSel.disabled = variations.length === 0;
                }

                function autoPickFirstPlan() {
                    if (varSel.options.length > 1) {
                        varSel.selectedIndex = 1;
                        syncVariationHidden();
                    }
                }

                function applyProviderById(sid) {
                    const p = providerModels.find(function (x) { return x.id === sid; });
                    syncServiceId();
                    if (!p) {
                        varSel.innerHTML = '<option value="">Unknown provider</option>';
                        varSel.disabled = true;
                        return Promise.resolve();
                    }
                    if (p.variations.length) {
                        fillVariationSelect(p.variations);
                        autoPickFirstPlan();
                        return Promise.resolve();
                    }
                    varSel.innerHTML = '<option value="">Loading plans…</option>';
                    varSel.disabled = true;
                    return fetchVariationsForService(p.id).then(function (vars) {
                        p.variations = vars;
                        fillVariationSelect(vars);
                        autoPickFirstPlan();
                    }).catch(function () {
                        varSel.innerHTML = '<option value="">Could not load plans</option>';
                        varSel.disabled = true;
                    });
                }

                function loadCatalog() {
                    if (amtInput) delete amtInput.dataset.userEdited;
                    setLoading(true);
                    debug.classList.add('d-none');
                    return fetch(plansUrl, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } })
                        .then(function (r) { return r.json(); })
                        .then(function (j) {
                            providerModels = buildProviderModels(j);
                            if (!providerModels.length) {
                                console.warn('Cable catalogue: could not parse providers', j);
                                debug.textContent = 'Could not parse provider list from the API. Check the browser console (F12) for the full JSON.';
                                debug.classList.remove('d-none');
                                svcSel.innerHTML = '<option value="">No providers found</option>';
                                varSel.innerHTML = '<option value="">—</option>';
                                varSel.disabled = true;
                                return;
                            }
                            debug.classList.add('d-none');
                            debug.textContent = '';
                            fillProviderSelect();
                            svcSel.selectedIndex = 1;
                            return applyProviderById(svcSel.value);
                        })
                        .finally(function () { setLoading(false); });
                }

                function syncServiceId() {
                    document.getElementById('vb-cable-sid-h').value = svcSel.value || '';
                }

                if (amtInput) {
                    amtInput.addEventListener('input', function () { amtInput.dataset.userEdited = '1'; });
                }

                loadBtn.addEventListener('click', function () {
                    loadCatalog();
                });

                svcSel.addEventListener('change', function () {
                    if (amtInput) delete amtInput.dataset.userEdited;
                    applyProviderById(svcSel.value);
                });

                varSel.addEventListener('change', syncVariationHidden);

                loadCatalog();

                document.getElementById('vb-cable-validate').addEventListener('click', function () {
                    const sid = svcSel.value;
                    const smart = document.getElementById('vb-cable-smart').value.trim();
                    const out = document.getElementById('vb-cable-validate-out');
                    if (!sid || !smart) {
                        alert('Select a provider and enter the smartcard number.');
                        return;
                    }
                    out.classList.remove('d-none', 'alert-danger', 'alert-success');
                    out.classList.add('alert-secondary');
                    out.textContent = 'Validating…';
                    fetch(validateUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({ service_id: sid, billersCode: smart })
                    }).then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
                        .then(function (x) {
                            out.textContent = typeof x.j === 'object' ? JSON.stringify(x.j, null, 2) : String(x.j);
                            out.classList.toggle('alert-danger', !x.ok);
                            out.classList.toggle('alert-success', x.ok);
                            out.classList.toggle('alert-secondary', false);
                        })
                        .catch(function () {
                            out.textContent = 'Request failed.';
                            out.classList.add('alert-danger');
                        });
                });

                document.getElementById('vb-cable-form').addEventListener('submit', function () {
                    syncServiceId();
                    syncVariationHidden();
                    var submitBtn = document.getElementById('vb-cable-submit');
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
