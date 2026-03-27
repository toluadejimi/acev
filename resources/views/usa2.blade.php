@extends('layout.dashboard-modern')
@section('title', 'USA Server 1 · SMS')

@push('styles')
    <link rel="stylesheet" href="{{ url('') }}/public/css/verification-page.css?v=9">
@endpush

@section('content')

<div class="vf-shell">

    <header class="vf-hero vf-hero--usa2">
        <div class="vf-hero__row">
            <div class="vf-hero__lead">
                <span class="vf-hero__badge"><i class="bi bi-telephone" aria-hidden="true"></i> USA pool</span>
                <h1 class="vf-hero__title">USA Server 1</h1>
                <p class="vf-hero__text">US number pool powered by Unlimited.</p>
            </div>
            <div class="vf-hero__stats">
                <p class="vf-hero__user">{{ Auth::user()->username }}</p>
                <p class="vf-hero__balance">₦{{ number_format(Auth::user()->wallet ?? 0, 2) }}</p>
                <p class="vf-hero__bal-label">Wallet balance</p>
                <a href="{{ url('/fund-wallet') }}" class="vf-hero__cta"><i class="bi bi-plus-lg" aria-hidden="true"></i> Fund wallet</a>
            </div>
        </div>
    </header>

    @php
        $vfServers = $verificationServers ?? ['us1' => false, 'us2' => true, 'world' => true, 'world_hero' => true, 'world_sv3' => true];
    @endphp
    <nav class="vf-servers" aria-label="Number pools">
        @if(!empty($vfServers['us1']))
            <a href="{{ route('verification.index') }}" class="vf-server">
                <span class="vf-server__flag" aria-hidden="true">🇺🇸</span>
                <span class="vf-server__name">USA · Server 1</span>
            </a>
        @endif
        @if(!empty($vfServers['us2']))
            <a href="{{ url('/usa2') }}" class="vf-server vf-server--active">
                <span class="vf-server__flag" aria-hidden="true">🇺🇸</span>
                <span class="vf-server__name">USA · Server 1</span>
                <span class="vf-server__hint">Current panel</span>
            </a>
        @endif
        @if(!empty($vfServers['world']))
            <a href="{{ url('/world') }}" class="vf-server">
                <span class="vf-server__flag" aria-hidden="true">🌎</span>
                <span class="vf-server__name">All countries · SV1</span>
            </a>
        @endif
        @if(!empty($vfServers['world_hero']))
            <a href="{{ url('/world-sv2') }}" class="vf-server">
                <span class="vf-server__flag" aria-hidden="true">🌍</span>
                <span class="vf-server__name">All countries · SV2</span>
                <span class="vf-server__tag-recommended">Recommended</span>
            </a>
        @endif
        @if(!empty($vfServers['world_sv3']))
            <a href="{{ url('/world-sv3') }}" class="vf-server">
                <span class="vf-server__flag" aria-hidden="true">🌐</span>
                <span class="vf-server__name">All countries · SV3</span>
            </a>
        @endif
    </nav>
    <p class="vf-servers-mobile-note"><i class="bi bi-arrow-right-short" aria-hidden="true"></i> Swipe right to see more servers</p>

            <div class="vf-alerts">
            @if ($errors->any())
                <div class="vf-alert vf-alert--danger" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session()->has('message'))
                <div class="vf-alert vf-alert--success" role="status">
                    {{ session()->get('message') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="vf-alert vf-alert--danger" role="alert">
                    {{ session()->get('error') }}
                </div>
            @endif
            </div>

            <div class="vf-grid">
                <article class="vf-panel vf-panel--order">
                    <div class="vf-panel__head">
                        <h2 class="vf-panel__title">Order a number</h2>
                        <p class="vf-panel__sub">Search for a service, then open settings to pick a US area code if needed (may add about 20% to the price).</p>
                    </div>
                    <div class="vf-panel__body">

                            <p class="vf-order-hint">Ordering on <strong>USA Server 1</strong></p>

                            <div class="position-relative">
                                <div class="vf-search-row">
                                    <input type="text" id="searchInput" class="vf-field-input" placeholder="Search for a service…" autocomplete="off">
                                    <button class="vf-icon-btn" id="toggleSettings" type="button" aria-label="Area code settings">
                                        <i class="bi bi-sliders"></i>
                                    </button>
                                </div>

                                <div id="servicesDropdown" class="list-group vf-dropdown mt-2 position-absolute w-100 bg-white shadow-sm"
                                     style="max-height: 750px; overflow-y: auto; display:none; z-index:1000;">
                                    @foreach ($allServices as $service)
                                        @php $cost = $get_rate * $service->price + $margin; @endphp
                                        <a href="javascript:void(0);"
                                           class="list-group-item list-group-item-action service-option"
                                            data-service="{{ $service->name }}"
                                            data-provider="{{ $service->name }}"
                                            data-cost="{{ $service->price }}"
                                            data-price="{{ number_format($cost, 2, '.', '') }}">
                                            <span class="vf-service-name">{{ $service->name }}</span>
                                            <span class="vf-service-price">₦{{ number_format($cost, 2) }}</span>
                                        </a>
                                    @endforeach
                                </div>

                                <div id="extraFields" class="vf-extra mt-2" style="display: none;">
                                    <div class="mb-2">
                                        <label class="vf-label" for="areaCode">Area codes</label>
                                        <select id="areaCode" class="form-control" style="width: 100%;">
                                            <option value=" ">Select Code</option>
                                            <option value="205">Alabama (205)</option>
                                            <option value="907">Alaska (907)</option>
                                            <option value="480">Arizona (480)</option>
                                            <option value="479">Arkansas (479)</option>
                                            <option value="209">California (209)</option>
                                            <option value="213">California (213)</option>
                                            <option value="310">California (310)</option>
                                            <option value="415">California (415)</option>
                                            <option value="619">California (619)</option>
                                            <option value="650">California (650)</option>
                                            <option value="707">California (707)</option>
                                            <option value="714">California (714)</option>
                                            <option value="818">California (818)</option>
                                            <option value="303">Colorado (303)</option>
                                            <option value="970">Colorado (970)</option>
                                            <option value="203">Connecticut (203)</option>
                                            <option value="302">Delaware (302)</option>
                                            <option value="305">Florida (305)</option>
                                            <option value="321">Florida (321)</option>
                                            <option value="352">Florida (352)</option>
                                            <option value="407">Florida (407)</option>
                                            <option value="561">Florida (561)</option>
                                            <option value="727">Florida (727)</option>
                                            <option value="813">Florida (813)</option>
                                            <option value="850">Florida (850)</option>
                                            <option value="904">Florida (904)</option>
                                            <option value="229">Georgia (229)</option>
                                            <option value="404">Georgia (404)</option>
                                            <option value="470">Georgia (470)</option>
                                            <option value="478">Georgia (478)</option>
                                            <option value="678">Georgia (678)</option>
                                            <option value="912">Georgia (912)</option>
                                            <option value="808">Hawaii (808)</option>
                                            <option value="208">Idaho (208)</option>
                                            <option value="217">Illinois (217)</option>
                                            <option value="312">Illinois (312)</option>
                                            <option value="618">Illinois (618)</option>
                                            <option value="708">Illinois (708)</option>
                                            <option value="872">Illinois (872)</option>
                                            <option value="219">Indiana (219)</option>
                                            <option value="317">Indiana (317)</option>
                                            <option value="574">Indiana (574)</option>
                                            <option value="515">Iowa (515)</option>
                                            <option value="316">Kansas (316)</option>
                                            <option value="606">Kentucky (606)</option>
                                            <option value="225">Louisiana (225)</option>
                                            <option value="207">Maine (207)</option>
                                            <option value="301">Maryland (301)</option>
                                            <option value="410">Maryland (410)</option>
                                            <option value="781">Massachusetts (781)</option>
                                            <option value="857">Massachusetts (857)</option>
                                            <option value="231">Michigan (231)</option>
                                            <option value="248">Michigan (248)</option>
                                            <option value="313">Michigan (313)</option>
                                            <option value="517">Michigan (517)</option>
                                            <option value="616">Michigan (616)</option>
                                            <option value="734">Michigan (734)</option>
                                            <option value="810">Michigan (810)</option>
                                            <option value="952">Minnesota (952)</option>
                                            <option value="228">Mississippi (228)</option>
                                            <option value="314">Missouri (314)</option>
                                            <option value="417">Missouri (417)</option>
                                            <option value="406">Montana (406)</option>
                                            <option value="308">Nebraska (308)</option>
                                            <option value="702">Nevada (702)</option>
                                            <option value="603">New Hampshire (603)</option>
                                            <option value="201">New Jersey (201)</option>
                                            <option value="609">New Jersey (609)</option>
                                            <option value="732">New Jersey (732)</option>
                                            <option value="848">New Jersey (848)</option>
                                            <option value="505">New Mexico (505)</option>
                                            <option value="212">New York (212)</option>
                                            <option value="315">New York (315)</option>
                                            <option value="347">New York (347)</option>
                                            <option value="516">New York (516)</option>
                                            <option value="585">New York (585)</option>
                                            <option value="607">New York (607)</option>
                                            <option value="646">New York (646)</option>
                                            <option value="716">New York (716)</option>
                                            <option value="718">New York (718)</option>
                                            <option value="845">New York (845)</option>
                                            <option value="914">New York (914)</option>
                                            <option value="919">North Carolina (919)</option>
                                            <option value="701">North Dakota (701)</option>
                                            <option value="216">Ohio (216)</option>
                                            <option value="330">Ohio (330)</option>
                                            <option value="419">Ohio (419)</option>
                                            <option value="440">Ohio (440)</option>
                                            <option value="513">Ohio (513)</option>
                                            <option value="614">Ohio (614)</option>
                                            <option value="740">Ohio (740)</option>
                                            <option value="918">Oklahoma (918)</option>
                                            <option value="503">Oregon (503)</option>
                                            <option value="215">Pennsylvania (215)</option>
                                            <option value="267">Pennsylvania (267)</option>
                                            <option value="412">Pennsylvania (412)</option>
                                            <option value="570">Pennsylvania (570)</option>
                                            <option value="717">Pennsylvania (717)</option>
                                            <option value="787">Puerto Rico (787)</option>
                                            <option value="401">Rhode Island (401)</option>
                                            <option value="803">South Carolina (803)</option>
                                            <option value="605">South Dakota (605)</option>
                                            <option value="423">Tennessee (423)</option>
                                            <option value="615">Tennessee (615)</option>
                                            <option value="731">Tennessee (731)</option>
                                            <option value="865">Tennessee (865)</option>
                                            <option value="901">Tennessee (901)</option>
                                            <option value="214">Texas (214)</option>
                                            <option value="254">Texas (254)</option>
                                            <option value="281">Texas (281)</option>
                                            <option value="325">Texas (325)</option>
                                            <option value="409">Texas (409)</option>
                                            <option value="512">Texas (512)</option>
                                            <option value="713">Texas (713)</option>
                                            <option value="806">Texas (806)</option>
                                            <option value="817">Texas (817)</option>
                                            <option value="830">Texas (830)</option>
                                            <option value="903">Texas (903)</option>
                                            <option value="915">Texas (915)</option>
                                            <option value="936">Texas (936)</option>
                                            <option value="972">Texas (972)</option>
                                            <option value="979">Texas (979)</option>
                                            <option value="435">Utah (435)</option>
                                            <option value="802">Vermont (802)</option>
                                            <option value="276">Virginia (276)</option>
                                            <option value="434">Virginia (434)</option>
                                            <option value="540">Virginia (540)</option>
                                            <option value="703">Virginia (703)</option>
                                            <option value="757">Virginia (757)</option>
                                            <option value="804">Virginia (804)</option>
                                            <option value="206">Washington (206)</option>
                                            <option value="253">Washington (253)</option>
                                            <option value="360">Washington (360)</option>
                                            <option value="425">Washington (425)</option>
                                            <option value="509">Washington (509)</option>
                                            <option value="304">West Virginia (304)</option>
                                            <option value="414">Wisconsin (414)</option>
                                            <option value="608">Wisconsin (608)</option>
                                            <option value="715">Wisconsin (715)</option>
                                            <option value="920">Wisconsin (920)</option>
                                            <option value="307">Wyoming (307)</option>
                                        </select>
                                        <script>
                                            $(document).ready(function () {
                                                $('#areaCode').select2({
                                                    placeholder: "Search or select an area code...",
                                                    allowClear: true,
                                                    width: '100%'
                                                });
                                            });
                                        </script>

                                    </div>
                                    <p class="vf-extra-note">Optional area code may add about 20% to the price.</p>
                                </div>
                            </div>

                            <div class="vf-rent-wrap">
                                <button type="button" id="rentNumberBtn" class="vf-btn-rent" disabled>
                                    <i class="bi bi-telephone"></i> Rent number
                                </button>
                            </div>

                            <script>
                                const searchInput = document.getElementById("searchInput");
                                const servicesDropdown = document.getElementById("servicesDropdown");
                                const extraFields = document.getElementById("extraFields");
                                const rentButton = document.getElementById("rentNumberBtn");

                                let selectedService = null;
                                let selectedCost = null;
                                let selectedPrice = null;
                                let selectedProvider = null;

                                // Show dropdown on focus
                                searchInput.addEventListener("focus", () => {
                                    servicesDropdown.style.display = "block";
                                });

                                // Filter services dynamically
                                searchInput.addEventListener("keyup", () => {
                                    const filter = searchInput.value.toLowerCase();
                                    let visibleCount = 0;

                                    document.querySelectorAll("#servicesDropdown .service-option").forEach(option => {
                                        const text = option.dataset.service.toLowerCase();
                                        if (text.includes(filter)) {
                                            option.style.display = "block";
                                            visibleCount++;
                                        } else {
                                            option.style.display = "none";
                                        }
                                    });

                                    servicesDropdown.style.display = visibleCount > 0 ? "block" : "none";
                                });

                                // Event delegation for service selection
                                servicesDropdown.addEventListener("click", function(e) {
                                    const option = e.target.closest(".service-option");
                                    if (!option) return;

                                    selectedService = option.dataset.service;
                                    selectedCost = option.dataset.cost;
                                    selectedProvider = option.dataset.provider;
                                    selectedPrice = option.dataset.price;

                                    searchInput.value = `${selectedService}`;
                                    servicesDropdown.style.display = "none";

                                    rentButton.disabled = false;
                                });

                                // Hide dropdown if click outside
                                document.addEventListener("click", (event) => {
                                    if (!searchInput.contains(event.target) && !servicesDropdown.contains(event.target) && !extraFields.contains(event.target)) {
                                        servicesDropdown.style.display = "none";
                                    }
                                });

                                // Toggle extra fields
                                document.getElementById("toggleSettings").addEventListener("click", () => {
                                    extraFields.style.display = extraFields.style.display === "none" ? "block" : "none";
                                });

                                // Rent number button
                                rentButton.addEventListener("click", () => {
                                    if (!selectedService) return;

                                    rentButton.disabled = true;
                                    rentButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing...';

                                    const areaCode = document.getElementById("areaCode").value || null;

                                    const payload = {
                                        provider: selectedProvider,
                                        service: selectedService,
                                        cost: selectedCost,
                                        price: selectedPrice,
                                        areaCode: areaCode,
                                    };

                                    fetch("{{ url('/order-usa2') }}", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/json",
                                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                        },
                                        body: JSON.stringify(payload)
                                    })
                                        .then(response => response.json())
                                        .then(res => {
                                            rentButton.disabled = false;
                                            rentButton.innerHTML = '<i class="bi bi-telephone"></i> Rent number';

                                            if (res.status && res.reload) {
                                                window.location.reload();
                                            } else if (res.status === true) {
                                                Swal.fire({
                                                    title: "Success 🎉",
                                                    text: res.message || "Your purchase was successful!",
                                                    icon: "success",
                                                    timer: 3000,
                                                    showConfirmButton: false
                                                });
                                            } else {
                                                Swal.fire("Error ❌", res.message || "Purchase failed", "error");
                                            }
                                        });
                                });
                            </script>

                    </div>
                </article>

                @auth
                    <article class="vf-panel vf-panel--requests">
                        <div class="vf-panel__head vf-panel__head--split">
                            <div>
                                <h2 class="vf-panel__title">Verification requests</h2>
                                <p class="vf-panel__sub">Codes update automatically. Tap a code to copy. Use the filter for this table only — it does not change the service search above.</p>
                            </div>
                            <div>
                                <label class="visually-hidden" for="vf-requests-filter">Filter requests</label>
                                <input type="search" id="vf-requests-filter" class="vf-filter-input" placeholder="Filter table…" autocomplete="off">
                            </div>
                        </div>
                        <p class="vf-panel__note"><strong>Active numbers</strong> — you do not need to refresh the page to receive codes.</p>
                        <div id="smsTableContainer" class="vf-table-scroll">
                                                <table class="vf-table" id="vf-requests-table">
                                                    <thead>
                                                    <tr>
                                                        <th>Service</th>
                                                        <th>Phone</th>
                                                        <th>Code</th>
                                                        <th>Price</th>
                                                        <th>Status</th>
                                                        <th>Date</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @forelse($verification as $data)
                                                        @php
                                                            $vfDigits = preg_replace('/\D/', '', (string) $data->phone);
                                                            if (strlen($vfDigits) === 11 && str_starts_with($vfDigits, '1')) {
                                                                $vfDigits = substr($vfDigits, 1);
                                                            }
                                                            $vfPhoneDisplay = (string) $data->phone;
                                                            if (strlen($vfDigits) === 10) {
                                                                $vfPhoneDisplay = '(' . substr($vfDigits, 0, 3) . ') ' . substr($vfDigits, 3, 3) . '-' . substr($vfDigits, 6);
                                                            }
                                                            $cancelCooldownEndMs = null;
                                                            if (in_array((int) ($data->type ?? 0), [3, 9], true) && (int) ($data->status ?? 0) === 1 && $data->created_at) {
                                                                $coolEnd = $data->created_at->copy()->addSeconds(120);
                                                                if ($coolEnd->isFuture()) {
                                                                    $cancelCooldownEndMs = (int) round($coolEnd->timestamp * 1000);
                                                                }
                                                            }
                                                        @endphp
                                                        <tr class="vf-req-row">
                                                            <td data-label="Service">{{ $data->service }}</td>
                                                            <td class="vf-phone-cell" data-label="Phone">
                                                                <div class="vf-copy-row">
                                                                    <span class="vf-mono vf-phone-display">{{ $vfPhoneDisplay }}</span>
                                                                    <button type="button" class="vf-btn-copy" data-copy="{{ e($data->phone) }}" title="Copy full number" aria-label="Copy phone number">
                                                                        <i class="bi bi-clipboard" aria-hidden="true"></i>
                                                                    </button>
                                                                </div>
                                                            </td>

                                                            <td data-label="Code">
                                                                <div id="smsContainer{{ $data->id }}" class="vf-sms-cell">
                                                                    <div class="vf-code-loader" id="loader{{ $data->id }}">
                                                                        <div class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></div>
                                                                        <span class="vf-code-loading-text">Waiting for SMS…</span>
                                                                    </div>
                                                                    <div id="vf-sms-wrap{{ $data->id }}" class="vf-sms-wrap d-none">
                                                                        <span id="data-sm{{ $data->id }}" class="vf-sms-code" title="Tap to copy"></span>
                                                                        <button type="button" class="vf-btn-copy vf-btn-copy--sms" id="vf-copy-sms{{ $data->id }}" hidden aria-label="Copy SMS">
                                                                            <i class="bi bi-clipboard" aria-hidden="true"></i>
                                                                        </button>
                                                                    </div>

                                                                    <div id="extraSmsList{{ $data->id }}" class="vf-extra-codes d-none"></div>
                                                                </div>

                                                                <script>
                                                                    document.addEventListener('DOMContentLoaded', () => {
                                                                        const id = {{ $data->id }};
                                                                        const status = {{ $data->status }};
                                                                        const phone = `{{ $data->phone }}`;
                                                                        const type = {{ $data->type }};
                                                                        function vfIsWaitingSmsMessage(msg) {
                                                                            return /waiting\s*for\s*sms/i.test(String(msg || ''));
                                                                        }
                                                                        function vfExtractOtp(msg) {
                                                                            const s = String(msg || '');
                                                                            const m = s.match(/\b(\d{4,8})\b/);
                                                                            return m ? m[1] : '';
                                                                        }
                                                                        function vfMarkUsa2RowCompleted() {
                                                                            const cell = document.getElementById('vf-status-cell-' + id);
                                                                            if (cell) {
                                                                                cell.innerHTML = '<span class="vf-status vf-status--done">Completed</span>';
                                                                            }
                                                                        }
                                                                        const smsSpan = document.getElementById(`data-sm${id}`);
                                                                        const loader = document.getElementById(`loader${id}`);
                                                                        const wrap = document.getElementById(`vf-sms-wrap${id}`);
                                                                        const copySmsBtn = document.getElementById(`vf-copy-sms${id}`);
                                                                        const extraList = document.getElementById(`extraSmsList${id}`);
                                                                        let countdownTimer = null;
                                                                        let lastCodes = [];

                                                                        const mainUrl = type === 3
                                                                            ? `{{ url('get-smscode-usa2') }}?num=${phone}`
                                                                            : `{{ url('get-smscode') }}?num=${phone}`;
                                                                        const fetchUrl = `{{ url('check-more-sms') }}?num=${phone}`;

                                                                        async function startCountdown() {
                                                                            try {
                                                                                const res = await fetch('{{ url('getInitialCountdown') }}?id={{ $data->id }}');
                                                                                const data = await res.json();
                                                                                let secs = data.seconds || 0;
                                                                                const countdownDisplay = document.getElementById('secondsDisplay' + id);

                                                                                countdownTimer = setInterval(() => {
                                                                                    secs--;
                                                                                    if (countdownDisplay) countdownDisplay.textContent = secs;
                                                                                    if (secs <= 0) {
                                                                                        clearInterval(countdownTimer);
                                                                                        fetch('{{ url('api/delete-order') }}', {
                                                                                            method: 'POST',
                                                                                            headers: {'Content-Type': 'application/json'},
                                                                                            body: JSON.stringify({id})
                                                                                        }).then(() => location.reload());
                                                                                    }
                                                                                }, 1000);
                                                                            } catch (e) {
                                                                                console.error('[COUNTDOWN ERROR]', e);
                                                                            }
                                                                        }

                                                                        async function fetchMainSMS() {
                                                                            try {
                                                                                const res = await fetch(mainUrl);
                                                                                const data = await res.json();
                                                                                const msg = data?.message != null ? String(data.message).trim() : '';
                                                                                const st = (data && data.status !== undefined && data.status !== null)
                                                                                    ? parseInt(data.status, 10) : NaN;

                                                                                const otp = vfExtractOtp(msg);
                                                                                if (otp && !vfIsWaitingSmsMessage(msg)) {
                                                                                    loader.classList.add('d-none');
                                                                                    if (wrap) wrap.classList.remove('d-none');
                                                                                    smsSpan.textContent = otp;
                                                                                    if (copySmsBtn) {
                                                                                        copySmsBtn.hidden = false;
                                                                                        copySmsBtn.setAttribute('data-copy', otp);
                                                                                    }
                                                                                    smsSpan.addEventListener('click', () => {
                                                                                        navigator.clipboard.writeText(otp).then(() => {
                                                                                            smsSpan.innerHTML = otp + ' <i class="bi bi-check2 text-success"></i>';
                                                                                            setTimeout(() => smsSpan.textContent = otp, 500);
                                                                                        });
                                                                                    });

                                                                                    if (countdownTimer) clearInterval(countdownTimer);
                                                                                    vfMarkUsa2RowCompleted();
                                                                                } else if (st === 2 && otp) {
                                                                                    vfMarkUsa2RowCompleted();
                                                                                }
                                                                            } catch (err) {
                                                                                console.error('[MAIN FETCH ERROR]', err);
                                                                            }
                                                                        }

                                                                        async function fetchExtraCodes() {
                                                                            try {
                                                                                const res = await fetch(fetchUrl);
                                                                                const result = await res.json();
                                                                                const messages = Array.isArray(result) ? result : result.codes || [];

                                                                                const valid = messages
                                                                                    .map(m => (m && typeof m === 'object') ? (m.sms ?? m.full_sms ?? '') : m)
                                                                                    .map(v => vfExtractOtp(String(v || '')))
                                                                                    .filter(Boolean);

                                                                                if (valid.length > 0) {
                                                                                    loader.classList.add('d-none');
                                                                                    if (wrap) wrap.classList.remove('d-none');
                                                                                    extraList.classList.remove('d-none');
                                                                                    if (countdownTimer) clearInterval(countdownTimer);
                                                                                    vfMarkUsa2RowCompleted();

                                                                                    if (JSON.stringify(valid) !== JSON.stringify(lastCodes)) {
                                                                                        extraList.innerHTML = '';
                                                                                        valid.forEach(code => {
                                                                                            const div = document.createElement('div');
                                                                                            div.className = 'vf-code-line';
                                                                                            div.innerHTML = `<span>${code}</span>`;
                                                                                            extraList.appendChild(div);
                                                                                            div.addEventListener('click', () => navigator.clipboard.writeText(code));
                                                                                        });
                                                                                        lastCodes = valid;
                                                                                    }
                                                                                }
                                                                            } catch (err) {
                                                                                console.error('[EXTRA FETCH ERROR]', err);
                                                                            }
                                                                        }

                                                                        function updateAll() {
                                                                            fetchMainSMS();
                                                                            fetchExtraCodes();
                                                                        }

                                                                        const vfPollMs = 60000;
                                                                        function updateAllIfVisible() {
                                                                            if (document.visibilityState === 'hidden') return;
                                                                            updateAll();
                                                                        }

                                                                        if (status === 2) {
                                                                            vfMarkUsa2RowCompleted();
                                                                        }
                                                                        if (status === 1) {
                                                                            startCountdown();
                                                                            updateAll();
                                                                            setInterval(updateAllIfVisible, vfPollMs);
                                                                            document.addEventListener('visibilitychange', () => {
                                                                                if (document.visibilityState === 'visible') updateAll();
                                                                            });
                                                                        } else {
                                                                            updateAll();
                                                                        }
                                                                    });
                                                                </script>
                                                            </td>



                                                            <td data-label="Price">₦{{ number_format($data->cost, 2) }}</td>

                                                            <td data-label="Status" id="vf-status-cell-{{ $data->id }}">
                                                                @if ($data->status == 1)
                                                                    <div class="vf-status-row">
                                                                        <span class="vf-status vf-status--pending">Pending</span>
                                                                        <form method="POST"
                                                                              action="{{ (int) $data->type === 3 ? url('delete-order-usa2?id='.$data->id.'&delete=1') : url('delete-order?id='.$data->id.'&delete=1') }}"
                                                                              class="d-inline vf-cancel-form"
                                                                              @if($cancelCooldownEndMs) data-hero-cooldown-end="{{ $cancelCooldownEndMs }}" @endif
                                                                              onsubmit="return confirmDelete(event, this);">
                                                                            @csrf
                                                                            <span class="vf-cancel-inline">
                                                                                <button type="submit" class="vf-btn-del @if($cancelCooldownEndMs) vf-btn-del--locked @endif" @if($cancelCooldownEndMs) disabled aria-disabled="true" @endif>Cancel</button>
                                                                                @if($cancelCooldownEndMs)
                                                                                    <span class="vf-hero-cancel-hint" role="status" aria-live="polite"></span>
                                                                                @endif
                                                                            </span>
                                                                        </form>
                                                                    </div>
                                                                @else
                                                                    <span class="vf-status vf-status--done">Completed</span>
                                                                @endif
                                                            </td>





                                                            <td class="vf-date-cell" data-label="Date">{{ $data->created_at?->format('M j, Y g:i A') ?? $data->created_at }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="vf-empty">No verification requests yet.</td>
                                                        </tr>
                                                    @endforelse
                                                    </tbody>
                                                </table>
                        </div>
                    </article>

                                        <script>
                                            function vfFlashCopy(btn) {
                                                if (!btn) return;
                                                var ic = btn.querySelector('i');
                                                if (!ic) return;
                                                var prev = ic.className;
                                                ic.className = 'bi bi-check2';
                                                setTimeout(function () { ic.className = prev; }, 1100);
                                            }

                                            function confirmDelete(event, form) {
                                                event.preventDefault();
                                                var btn = form.querySelector('.vf-btn-del');
                                                if (btn && btn.disabled) return false;
                                                Swal.fire({
                                                    title: 'Cancel order?',
                                                    text: 'Are you sure you want to cancel this verification?',
                                                    icon: 'question',
                                                    showCancelButton: true,
                                                    confirmButtonText: 'Yes, delete it',
                                                    cancelButtonText: 'No, keep it'
                                                }).then(result => {
                                                    if (result.isConfirmed) form.submit();
                                                });
                                                return false;
                                            }

                                            function vfInitHeroCancelCooldown() {
                                                document.querySelectorAll('form.vf-cancel-form[data-hero-cooldown-end]').forEach(function (form) {
                                                    var end = parseInt(form.getAttribute('data-hero-cooldown-end'), 10);
                                                    var btn = form.querySelector('.vf-btn-del');
                                                    var hint = form.querySelector('.vf-hero-cancel-hint');
                                                    if (!btn || !end || isNaN(end)) return;

                                                    function fmt(secs) {
                                                        var m = Math.floor(secs / 60);
                                                        var s = secs % 60;
                                                        return m + ':' + (s < 10 ? '0' : '') + s;
                                                    }

                                                    function tick() {
                                                        var left = Math.ceil((end - Date.now()) / 1000);
                                                        if (left <= 0) {
                                                            btn.disabled = false;
                                                            btn.removeAttribute('aria-disabled');
                                                            btn.classList.remove('vf-btn-del--locked');
                                                            if (hint) {
                                                                hint.textContent = '';
                                                                hint.classList.add('d-none');
                                                            }
                                                            form.removeAttribute('data-hero-cooldown-end');
                                                            return;
                                                        }
                                                        btn.disabled = true;
                                                        btn.setAttribute('aria-disabled', 'true');
                                                        if (hint) {
                                                            hint.classList.remove('d-none');
                                                            hint.textContent = 'Cancel unlocks in ' + fmt(left);
                                                        }
                                                        setTimeout(tick, 1000);
                                                    }
                                                    tick();
                                                });
                                            }
                                        </script>

                                        <script>
                                            (function () {
                                                vfInitHeroCancelCooldown();
                                                var el = document.getElementById("vf-requests-filter");
                                                var table = document.getElementById("vf-requests-table");
                                                if (!el || !table) return;
                                                table.addEventListener('click', function (e) {
                                                    var btn = e.target.closest('.vf-btn-copy[data-copy]');
                                                    if (!btn || btn.hasAttribute('disabled')) return;
                                                    var t = btn.getAttribute('data-copy');
                                                    if (!t) return;
                                                    e.preventDefault();
                                                    navigator.clipboard.writeText(t).then(function () { vfFlashCopy(btn); });
                                                });
                                                el.addEventListener("input", function () {
                                                    var filter = el.value.toLowerCase().trim();
                                                    table.querySelectorAll("tbody tr").forEach(function (row) {
                                                        if (row.querySelector(".vf-empty")) {
                                                            row.style.display = "";
                                                            return;
                                                        }
                                                        var text = row.textContent.toLowerCase();
                                                        row.style.display = !filter || text.includes(filter) ? "" : "none";
                                                    });
                                                });
                                            })();
                                        </script>

                @endauth

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
