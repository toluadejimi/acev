@extends('layout.dashboard-modern')

@section('title', 'Electricity')

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
                    <code class="vb-json-hint">SPRINTPAY_WEBHOOK_SECRET</code>. Uses
                    <code class="vb-json-hint">merchant/vas/validate-electricity-meter</code> and
                    <code class="vb-json-hint">merchant/vas/buy-electricity</code>.
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
                <h2 class="fw-card__title">Electricity (prepaid / postpaid)</h2>
                <p class="fw-card__sub">Select DISCO, load tariff options, validate meter, then pay.</p>
            </div>
            <div class="fw-card__body">
                <form action="{{ route('vas.electricity.buy') }}" method="POST" class="vb-stack" id="vb-el-form">
                    @csrf
                    <input type="hidden" name="service_id" id="vb-el-sid-h" value="" required>
                    <input type="hidden" name="variation_code" id="vb-el-var-h" value="" required>

                    <div class="vb-stack vb-stack--2">
                        <div>
                            <label class="fw-label" for="vb-el-disco">Distribution company</label>
                            <select id="vb-el-disco" class="fw-select" {{ $vasConfigured ? '' : 'disabled' }}>
                                <option value="">Select DISCO</option>
                                @foreach ([
                                    'ikeja-electric' => 'Ikeja Electric',
                                    'eko-electric' => 'Eko Electric',
                                    'kano-electric' => 'Kano Electric',
                                    'portharcourt-electric' => 'Port Harcourt Electric',
                                    'enugu-electric' => 'Enugu Electric',
                                    'ibadan-electric' => 'Ibadan Electric',
                                    'kaduna-electric' => 'Kaduna Electric',
                                    'abuja-electric' => 'Abuja Electric',
                                    'jos-electric' => 'Jos Electric',
                                    'benin-electric' => 'Benin Electric',
                                    'yola-electric' => 'Yola Electric',
                                    'maiduguri-electric' => 'Maiduguri Electric',
                                ] as $sid => $label)
                                    <option value="{{ $sid }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="fw-label" for="vb-el-type">Meter type</label>
                            <select id="vb-el-type" class="fw-select" {{ $vasConfigured ? '' : 'disabled' }}>
                                <option value="prepaid">Prepaid</option>
                                <option value="postpaid">Postpaid</option>
                            </select>
                        </div>
                    </div>

                    <div class="vb-actions">
                        <button type="button" class="vb-btn-ghost" id="vb-el-load-var" {{ $vasConfigured ? '' : 'disabled' }}>Load meter types / amounts</button>
                        <span class="vb-loading d-none" id="vb-el-loading">Loading…</span>
                    </div>

                    <div>
                        <label class="fw-label" for="vb-el-variation">Tariff / bundle</label>
                        <select id="vb-el-variation" class="fw-select" disabled>
                            <option value="">Load options first</option>
                        </select>
                    </div>

                    <div>
                        <label class="fw-label" for="vb-el-meter">Meter number</label>
                        <input id="vb-el-meter" type="text" name="billersCode" class="fw-input" required autocomplete="off"
                               {{ $vasConfigured ? '' : 'disabled' }}>
                    </div>

                    <div class="vb-actions">
                        <button type="button" class="vb-btn-ghost" id="vb-el-validate" {{ $vasConfigured ? '' : 'disabled' }}>Validate meter</button>
                    </div>
                    <div id="vb-el-validate-out" class="alert alert-secondary d-none py-2 small" role="status"></div>

                    <div class="vb-stack vb-stack--2">
                        <div>
                            <label class="fw-label" for="vb-el-amt">Amount to debit (₦)</label>
                            <input id="vb-el-amt" type="number" name="amount" class="fw-input" min="100" max="500000" step="1" required
                                   {{ $vasConfigured ? '' : 'disabled' }}>
                        </div>
                        <div>
                            <label class="fw-label" for="vb-el-phone">Phone</label>
                            <input id="vb-el-phone" type="text" name="phone" class="fw-input" inputmode="tel" autocomplete="tel"
                                   placeholder="08012345678" required {{ $vasConfigured ? '' : 'disabled' }}>
                        </div>
                    </div>

                    <button type="submit" class="fw-submit" id="vb-el-submit" {{ $vasConfigured ? '' : 'disabled' }}>Buy electricity</button>
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
                const varUrl = @json(url('/vas/catalog/electricity-variations'));
                const validateUrl = @json(url('/vas/electricity/validate'));

                function pickElVariations(payload) {
                    if (!payload || typeof payload !== 'object') return [];
                    const d = payload.data ?? payload.content ?? payload;
                    let arr = d.variations ?? d.content ?? (Array.isArray(d) ? d : []);
                    if (!Array.isArray(arr)) return [];
                    return arr.map(function (v) {
                        const code = v.variation_code || v.code || '';
                        const name = v.name || v.title || code;
                        return { code: String(code), name: String(name) };
                    }).filter(function (x) { return x.code; });
                }

                const disco = document.getElementById('vb-el-disco');
                const sidH = document.getElementById('vb-el-sid-h');
                const typeSel = document.getElementById('vb-el-type');
                const varSel = document.getElementById('vb-el-variation');
                const varH = document.getElementById('vb-el-var-h');

                function syncServiceId() {
                    sidH.value = disco.value || '';
                }

                function syncVariation() {
                    const opt = varSel.selectedOptions[0];
                    varH.value = opt && opt.dataset.code ? opt.dataset.code : '';
                }

                disco.addEventListener('change', syncServiceId);

                varSel.addEventListener('change', syncVariation);

                document.getElementById('vb-el-load-var').addEventListener('click', function () {
                    syncServiceId();
                    const sid = sidH.value;
                    const loadEl = document.getElementById('vb-el-loading');
                    if (!sid) {
                        alert('Select a distribution company.');
                        return;
                    }
                    loadEl.classList.remove('d-none');
                    varSel.innerHTML = '<option value="">Loading…</option>';
                    varSel.disabled = true;
                    fetch(varUrl + '?serviceID=' + encodeURIComponent(sid), {
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }
                    }).then(function (r) { return r.json(); }).then(function (j) {
                        const rows = pickElVariations(j);
                        varSel.innerHTML = '<option value="">Select option</option>';
                        rows.forEach(function (row) {
                            const opt = document.createElement('option');
                            opt.value = row.code;
                            opt.dataset.code = row.code;
                            opt.textContent = row.name;
                            varSel.appendChild(opt);
                        });
                        varSel.disabled = rows.length === 0;
                        if (!rows.length) {
                            varSel.innerHTML = '<option value="">No options available</option>';
                        }
                    }).finally(function () { loadEl.classList.add('d-none'); });
                });

                document.getElementById('vb-el-validate').addEventListener('click', function () {
                    syncServiceId();
                    const sid = sidH.value;
                    const meter = document.getElementById('vb-el-meter').value.trim();
                    const out = document.getElementById('vb-el-validate-out');
                    if (!sid || !meter) {
                        alert('DISCO and meter number required.');
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
                        body: JSON.stringify({
                            service_id: sid,
                            billersCode: meter,
                            type: typeSel.value
                        })
                    }).then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
                        .then(function (x) {
                            out.textContent = typeof x.j === 'object' ? JSON.stringify(x.j, null, 2) : String(x.j);
                            out.classList.toggle('alert-danger', !x.ok);
                            out.classList.toggle('alert-success', x.ok);
                        })
                        .catch(function () {
                            out.textContent = 'Request failed.';
                            out.classList.add('alert-danger');
                        });
                });

                document.getElementById('vb-el-form').addEventListener('submit', function () {
                    syncServiceId();
                    syncVariation();
                    var submitBtn = document.getElementById('vb-el-submit');
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
