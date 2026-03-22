@extends('layout.dashboard-modern')
@section('title', 'SMS verification')

@push('styles')
    <link rel="stylesheet" href="{{ url('') }}/public/css/verification-page.css?v=6">
@endpush

@section('content')

<div class="vf-shell">

    <header class="vf-hero">
        <div class="vf-hero__row">
            <div class="vf-hero__lead">
                <span class="vf-hero__badge"><i class="bi bi-shield-check" aria-hidden="true"></i> Verification hub</span>
                <h1 class="vf-hero__title">SMS verification</h1>
                <p class="vf-hero__text">Rent US numbers (Server 1 / 2) or open <a href="{{ url('/world') }}">all countries</a>. Pick a service, then track OTP codes in your active requests.</p>
            </div>
            <div class="vf-hero__stats">
                <p class="vf-hero__user">{{ Auth::user()->username }}</p>
                <p class="vf-hero__balance">₦{{ number_format(Auth::user()->wallet ?? 0, 2) }}</p>
                <p class="vf-hero__bal-label">Wallet balance</p>
                <a href="{{ url('/fund-wallet') }}" class="vf-hero__cta"><i class="bi bi-plus-lg" aria-hidden="true"></i> Fund wallet</a>
            </div>
        </div>
    </header>

    <nav class="vf-servers" aria-label="Number pools">
        <a href="{{ route('verification.index') }}" class="vf-server vf-server--active">
            <span class="vf-server__flag" aria-hidden="true">🇺🇸</span>
            <span class="vf-server__name">USA · Server 1</span>
            <span class="vf-server__hint">Current panel</span>
        </a>
        <a href="{{ url('/usa2') }}" class="vf-server">
            <span class="vf-server__flag" aria-hidden="true">🇺🇸</span>
            <span class="vf-server__name">USA · Server 2</span>
        </a>
        <a href="{{ url('/world') }}" class="vf-server">
            <span class="vf-server__flag" aria-hidden="true">🌎</span>
            <span class="vf-server__name">All countries</span>
        </a>
    </nav>

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
                        <p class="vf-panel__sub">Search for a service, then use the gear icon if you need a US area code or carrier (may add ~20% to price).</p>
                    </div>
                    <div class="vf-panel__body">

                            <p class="vf-order-hint">Ordering on <strong>USA Server 1</strong></p>

                            <div class="position-relative">
                                <div class="vf-search-row">
                                    <input type="text" id="searchInput" class="vf-field-input" placeholder="Search for a service…" autocomplete="off">
                                    <button class="vf-icon-btn" id="toggleSettings" type="button" aria-label="Area code and carrier settings">
                                        <i class="bi bi-sliders"></i>
                                    </button>
                                </div>

                                <div id="servicesDropdown" class="list-group vf-dropdown mt-2 position-absolute w-100 bg-white shadow-sm vf-services-dropdown"
                                     style="top: 100%; left: 0; display:none; z-index:1000;">
                                    <div id="vf-service-no-results" class="vf-service-no-results" style="display:none;" role="status">No services match your search.</div>
                                    @foreach ($allServices as $service)
                                        @php
                                            $cost = $get_rate * ($service->cost ?? 0) + $margin;
                                            $sflat = (array) $service;
                                            $svcName = $sflat['name'] ?? $sflat['Name'] ?? $sflat['service'] ?? $sflat['title'] ?? null;
                                            if ($svcName === null || $svcName === '') {
                                                $svcName = $service->provider ?? '';
                                            }
                                            $svcName = trim((string) $svcName);
                                            $svcProvider = (string) ($service->provider ?? '');
                                            $svcSearch = mb_strtolower($svcName . ' ' . $svcProvider, 'UTF-8');
                                        @endphp
                                        <button type="button"
                                           class="list-group-item list-group-item-action service-option vf-service-option-btn"
                                            data-service="{{ e($svcName) }}"
                                            data-search="{{ e($svcSearch) }}"
                                            data-provider="{{ e($svcProvider) }}"
                                            data-cost="{{ e($service->cost ?? '') }}"
                                            data-price="{{ number_format($cost, 2, '.', '') }}">
                                            <span class="vf-service-name">{{ $svcName !== '' ? $svcName : $svcProvider }}</span>
                                            <span class="vf-service-price">₦{{ number_format($cost, 2) }}</span>
                                        </button>
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
                                    <div>
                                        <label class="vf-label" for="carrier">Carriers</label>
                                        <select id="carrier" class="form-control" placeholder="Enter Carrier">
                                            <option value="">Any Carrier</option>
                                            <option value="tmo">T-Mobile</option>
                                            <option value="vz">Verizon</option>
                                            <option value="att">AT&T</option>
                                        </select>
                                    </div>
                                    <p class="vf-extra-note">Optional filters may add about 20% to the price.</p>
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
                                const vfServiceNoResults = document.getElementById("vf-service-no-results");

                                let selectedService = null;
                                let selectedCost = null;
                                let selectedPrice = null;
                                let selectedProvider = null;

                                function applyServiceFilter() {
                                    if (!searchInput || !servicesDropdown) return;
                                    const raw = (searchInput.value || "").trim();
                                    const filter = raw.toLowerCase();
                                    let visibleCount = 0;
                                    document.querySelectorAll("#servicesDropdown .service-option").forEach(function (option) {
                                        const hay = (option.getAttribute("data-search") || option.getAttribute("data-service") || "").toLowerCase();
                                        const match = filter.length === 0 || hay.indexOf(filter) !== -1;
                                        option.classList.toggle("vf-service-option--hidden", !match);
                                        option.setAttribute("aria-hidden", match ? "false" : "true");
                                        if (match) visibleCount++;
                                    });
                                    if (vfServiceNoResults) {
                                        vfServiceNoResults.style.display = (filter.length > 0 && visibleCount === 0) ? "block" : "none";
                                    }
                                }

                                function openServiceDropdown() {
                                    if (!servicesDropdown) return;
                                    servicesDropdown.style.display = "block";
                                    applyServiceFilter();
                                }

                                if (searchInput) {
                                    searchInput.addEventListener("focus", openServiceDropdown);
                                    searchInput.addEventListener("input", applyServiceFilter);
                                    searchInput.addEventListener("keyup", applyServiceFilter);
                                    searchInput.addEventListener("search", function () {
                                        applyServiceFilter();
                                        if (searchInput.value === "") openServiceDropdown();
                                    });
                                }

                                servicesDropdown.addEventListener("click", function(e) {
                                    const option = e.target.closest(".service-option");
                                    if (!option) return;

                                    selectedService = option.getAttribute("data-service");
                                    selectedCost = option.getAttribute("data-cost");
                                    selectedProvider = option.getAttribute("data-provider");
                                    selectedPrice = option.getAttribute("data-price");

                                    searchInput.value = selectedService || "";
                                    servicesDropdown.style.display = "none";

                                    if (rentButton) rentButton.disabled = false;
                                });

                                document.addEventListener("click", (event) => {
                                    if (!searchInput || !servicesDropdown) return;
                                    if (!searchInput.contains(event.target) && !servicesDropdown.contains(event.target) && extraFields && !extraFields.contains(event.target)) {
                                        servicesDropdown.style.display = "none";
                                    }
                                });

                                document.getElementById("toggleSettings").addEventListener("click", () => {
                                    extraFields.style.display = extraFields.style.display === "none" ? "block" : "none";
                                });

                                rentButton.addEventListener("click", () => {
                                    if (!selectedService) return;

                                    rentButton.disabled = true;
                                    rentButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing...';

                                    const areaCode = document.getElementById("areaCode").value || null;
                                    const carrier = document.getElementById("carrier").value || null;

                                    const payload = {
                                        provider: selectedProvider,
                                        service: selectedService,
                                        cost: selectedCost,
                                        price: selectedPrice,
                                        areaCode: areaCode,
                                        carrier: carrier
                                    };

                                    fetch("/order-usano", {
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
                                <p class="vf-panel__sub">Codes update automatically. Use <strong>Copy</strong> beside the number or SMS, or tap the code. Filter below only affects this list — not the service search when ordering.</p>
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
                                                        <th>Code / SMS</th>
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
                                                            $vfSearchHaystack = mb_strtolower(
                                                                ($data->service ?? '') . ' ' . ($data->phone ?? '') . ' ' . ($data->sms ?? ''),
                                                                'UTF-8'
                                                            );
                                                        @endphp
                                                        <tr class="vf-req-row"
                                                            data-vf-id="{{ $data->id }}"
                                                            data-vf-phone="{{ e($data->phone) }}"
                                                            data-vf-type="{{ $data->type }}"
                                                            data-vf-status="{{ $data->status }}"
                                                            data-vf-initial-sms="{{ e($data->sms ?? '') }}"
                                                            data-vf-search="{{ e($vfSearchHaystack) }}">
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
                                                            </td>

                                                            <td data-label="Price">₦{{ number_format($data->cost, 2) }}</td>

                                                            <td data-label="Status">
                                                                @if ($data->status == 1)
                                                                    <div class="vf-status-row">
                                                                        <span class="vf-status vf-status--pending">Pending</span>
                                                                        <form method="POST"
                                                                              action="{{ $data->type === 3 ? url('delete-order-usa2?id='.$data->id.'&delete=1') : url('delete-order?id='.$data->id.'&delete=1') }}"
                                                                              class="d-inline"
                                                                              onsubmit="return confirmDelete(event, this);">
                                                                            @csrf
                                                                            <button type="submit" class="vf-btn-del">Cancel</button>
                                                                        </form>
                                                                    </div>
                                                                @else
                                                                    <span class="vf-status vf-status--done">Completed</span>
                                                                @endif
                                                            </td>

                                                            <td class="vf-date-cell" data-label="Date">{{ $data->created_at?->format('M j, Y g:i A') ?? $data->created_at }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr class="vf-empty-row">
                                                            <td colspan="6" class="vf-empty">No verification requests yet.</td>
                                                        </tr>
                                                    @endforelse
                                                    </tbody>
                                                </table>
                        </div>
                    </article>

                                        <script>
                                            function confirmDelete(event, form) {
                                                event.preventDefault();
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
                                        </script>

                                        <script>
                                            (function () {
                                                var VF = {
                                                    getSms: @json(url('get-smscode')),
                                                    getSmsUsa2: @json(url('get-smscode-usa2')),
                                                    checkMore: @json(url('check-more-sms')),
                                                    getCountdown: @json(url('getInitialCountdown')),
                                                    deleteOrder: @json(url('api/delete-order')),
                                                };

                                                function vfFlashCopy(btn) {
                                                    if (!btn) return;
                                                    var ic = btn.querySelector('i');
                                                    if (!ic) return;
                                                    var prev = ic.className;
                                                    ic.className = 'bi bi-check2';
                                                    setTimeout(function () { ic.className = prev; }, 1100);
                                                }

                                                document.getElementById('vf-requests-table').addEventListener('click', function (e) {
                                                    var btn = e.target.closest('.vf-btn-copy[data-copy]');
                                                    if (!btn || btn.hasAttribute('disabled')) return;
                                                    var t = btn.getAttribute('data-copy');
                                                    if (!t) return;
                                                    e.preventDefault();
                                                    navigator.clipboard.writeText(t).then(function () {
                                                        vfFlashCopy(btn);
                                                    });
                                                });

                                                document.querySelectorAll('tr.vf-req-row').forEach(function (row) {
                                                    var id = row.getAttribute('data-vf-id');
                                                    var phone = row.getAttribute('data-vf-phone') || '';
                                                    var type = parseInt(row.getAttribute('data-vf-type'), 10);
                                                    var status = parseInt(row.getAttribute('data-vf-status'), 10);
                                                    var smsSpan = document.getElementById('data-sm' + id);
                                                    var loader = document.getElementById('loader' + id);
                                                    var wrap = document.getElementById('vf-sms-wrap' + id);
                                                    var copySmsBtn = document.getElementById('vf-copy-sms' + id);
                                                    var extraList = document.getElementById('extraSmsList' + id);
                                                    var countdownTimer = null;
                                                    var lastCodes = [];

                                                    var enc = encodeURIComponent(phone);
                                                    var mainUrl = type === 3
                                                        ? VF.getSmsUsa2 + '?num=' + enc
                                                        : VF.getSms + '?num=' + enc;
                                                    var fetchUrl = VF.checkMore + '?num=' + enc;

                                                    function extendSearch(extra) {
                                                        if (!extra) return;
                                                        var cur = (row.getAttribute('data-vf-search') || '').toLowerCase();
                                                        var add = String(extra).toLowerCase();
                                                        if (cur.indexOf(add) === -1) {
                                                            row.setAttribute('data-vf-search', (cur + ' ' + add).trim());
                                                        }
                                                    }

                                                    function showMainSms(msg) {
                                                        if (!msg) return;
                                                        loader.classList.add('d-none');
                                                        wrap.classList.remove('d-none');
                                                        smsSpan.textContent = msg;
                                                        copySmsBtn.hidden = false;
                                                        copySmsBtn.setAttribute('data-copy', msg);
                                                        smsSpan.onclick = function () {
                                                            navigator.clipboard.writeText(msg).then(function () {
                                                                vfFlashCopy(copySmsBtn);
                                                            });
                                                        };
                                                        extendSearch(msg);
                                                        if (countdownTimer) {
                                                            clearInterval(countdownTimer);
                                                            countdownTimer = null;
                                                        }
                                                    }

                                                    async function startCountdown() {
                                                        try {
                                                            var res = await fetch(VF.getCountdown + '?id=' + encodeURIComponent(id));
                                                            var data = await res.json();
                                                            var secs = data.seconds || 0;
                                                            var countdownDisplay = document.getElementById('secondsDisplay' + id);
                                                            countdownTimer = setInterval(function () {
                                                                secs--;
                                                                if (countdownDisplay) countdownDisplay.textContent = secs;
                                                                if (secs <= 0) {
                                                                    clearInterval(countdownTimer);
                                                                    countdownTimer = null;
                                                                    fetch(VF.deleteOrder, {
                                                                        method: 'POST',
                                                                        headers: {'Content-Type': 'application/json'},
                                                                        body: JSON.stringify({id: parseInt(id, 10)})
                                                                    }).then(function () { location.reload(); });
                                                                }
                                                            }, 1000);
                                                        } catch (e) { console.error(e); }
                                                    }

                                                    async function fetchMainSMS() {
                                                        try {
                                                            var res = await fetch(mainUrl);
                                                            var data = await res.json();
                                                            var msg = (data && data.message) ? String(data.message).trim() : '';
                                                            if (msg.length > 0) showMainSms(msg);
                                                        } catch (err) { console.error(err); }
                                                    }

                                                    async function fetchExtraCodes() {
                                                        try {
                                                            var res = await fetch(fetchUrl);
                                                            var result = await res.json();
                                                            var messages = Array.isArray(result) ? result : (result.codes || []);

                                                            if (messages.length > 0) {
                                                                loader.classList.add('d-none');
                                                                extraList.classList.remove('d-none');
                                                                if (countdownTimer) {
                                                                    clearInterval(countdownTimer);
                                                                    countdownTimer = null;
                                                                }

                                                                if (JSON.stringify(messages) !== JSON.stringify(lastCodes)) {
                                                                    extraList.innerHTML = '';
                                                                    messages.forEach(function (msg) {
                                                                        var code = (msg && msg.sms !== undefined) ? msg.sms : msg;
                                                                        code = String(code);
                                                                        var div = document.createElement('div');
                                                                        div.className = 'vf-code-line';
                                                                        var sp = document.createElement('span');
                                                                        sp.textContent = code;
                                                                        div.appendChild(sp);
                                                                        var cp = document.createElement('button');
                                                                        cp.type = 'button';
                                                                        cp.className = 'vf-btn-copy vf-btn-copy--inline';
                                                                        cp.setAttribute('data-copy', code);
                                                                        cp.setAttribute('aria-label', 'Copy');
                                                                        cp.innerHTML = '<i class="bi bi-clipboard" aria-hidden="true"></i>';
                                                                        div.appendChild(cp);
                                                                        extraList.appendChild(div);
                                                                        extendSearch(code);
                                                                    });
                                                                    lastCodes = messages;
                                                                }
                                                            }
                                                        } catch (err) { console.error(err); }
                                                    }

                                                    function updateAll() {
                                                        fetchMainSMS();
                                                        fetchExtraCodes();
                                                    }

                                                    var initialSms = (row.getAttribute('data-vf-initial-sms') || '').trim();
                                                    if (initialSms) {
                                                        showMainSms(initialSms);
                                                    }

                                                    if (status === 1) {
                                                        startCountdown();
                                                        updateAll();
                                                        setInterval(updateAll, 30000);
                                                    } else {
                                                        updateAll();
                                                    }
                                                });

                                                var el = document.getElementById("vf-requests-filter");
                                                var table = document.getElementById("vf-requests-table");
                                                if (!el || !table) return;
                                                el.addEventListener("input", function () {
                                                    var filter = el.value.toLowerCase().trim();
                                                    table.querySelectorAll("tbody tr").forEach(function (row) {
                                                        if (row.classList.contains("vf-empty-row")) {
                                                            row.style.display = "";
                                                            return;
                                                        }
                                                        var hay = (row.getAttribute("data-vf-search") || "").toLowerCase();
                                                        row.style.display = !filter || hay.indexOf(filter) !== -1 ? "" : "none";
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
