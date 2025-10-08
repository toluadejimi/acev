@extends('layout.main')
@section('content')

    <section id="technologies mt-4 my-5">
        <div class="container title my-5">
            <div class="row justify-content-center text-center wow fadeInUp" data-wow-delay="0.2s">
                <div class="col-md-8 col-xl-6">
                    <h4 class="mb-3 text-danger">{{ Auth::user()->username }}</h4>
                    <p class="mb-0">
                        SMS Verifications<br>
                        Rent a phone for 7 minutes.<br>
                        Credits are only used if you receive the SMS code.
                    </p>
                </div>
            </div>
        </div>


        <div class="container technology-block">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session()->get('error') }}
                </div>
            @endif


            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">


                            <div class="d-flex justify-content-center my-3">
                                <div class="d-flex justify-content-center my-3">

                                    <div class="btn-group" role="group" aria-label="Third group">
                                        <a style="font-size: 12px; background: rgba(23, 69, 132, 1); color: white"
                                           href="/us" class="btn  w-200 mt-1">
                                            🇺🇸 USA SV1
                                        </a>

                                        <a style="font-size: 12px; background: rgb(230 61 138); color: white"
                                           href="/usa2" class="btn  w-200 mt-1">
                                            🇺🇸 USA SV2
                                        </a>


                                        <a style="font-size: 12px; box-shadow: deeppink" href="/world"
                                           class="btn btn-dark w-200 mt-1">
                                            🌎 ALL COUNTRIES

                                        </a>


                                    </div>

                                </div>

                            </div>


                            <p class="d-flex justify-content-center">You are on 🇺🇸 USA Server 2 Numbers only Panel</p>


                            <div class="p-2 col-lg-12 position-relative">
                                <!-- Search input + settings -->
                                <div class="d-flex align-items-center">
                                    <input type="text" id="searchInput" class="form-control"
                                           placeholder="Search for a service...">
                                    <button class="btn btn-outline-secondary ms-2" id="toggleSettings" type="button">
                                        <i class="bi bi-gear"></i>
                                    </button>
                                </div>

                                <!-- Dropdown (services) -->
                                <div id="servicesDropdown"
                                     class="list-group mt-2 position-absolute w-100 bg-white shadow-sm"
                                     style="max-height: 750px; overflow-y: auto; display:none; z-index:1000;">
                                    @foreach ($allServices as $service)
                                        @php $cost = $get_rate * $service->price + $margin; @endphp
                                        <a href="javascript:void(0);"
                                           class="list-group-item list-group-item-action service-option"
                                           data-service="{{ $service->name }}"
                                           data-provider="{{ $service->name }}"
                                           data-cost="{{ $service->price }}"
                                           data-price="{{ number_format($cost, 2, '.', '') }}">
                                            <span style="font-size: 12px">{{ $service->name }}</span>
                                            <span
                                                class="float-end"><strong>N{{ number_format($cost, 2) }}</strong></span>
                                        </a>
                                    @endforeach
                                </div>

                                <!-- Extra fields -->
                                <div id="extraFields" class="card p-2 shadow-sm mt-2" style="display: none;">
                                    <div class="container">
                                        <label class="my-3">Area Code</label>
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


                                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

                                        <script
                                            src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

                                        <script>
                                            // Initialize Select2 on the dropdown
                                            $(document).ready(function () {
                                                $('#area_code').select2({
                                                    placeholder: "Search or select an area code...",
                                                    allowClear: true
                                                });
                                            });
                                        </script>


                                    </div>


                                    <p style="color:#8e0404;" class="my-3 d-flex justify-content-center">This may
                                        attract extra 20% charge</p>
                                </div>
                            </div>

                            <div class="list-group-item text-center bg-light">
                                <button id="rentNumberBtn" class="btn btn-primary w-100" disabled>
                                    <i class="bi bi-telephone"></i> Rent Number
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
                                servicesDropdown.addEventListener("click", function (e) {
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

                                    fetch("/order-usa2", {
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
                                            rentButton.innerHTML = '<i class="bi bi-telephone"></i> Rent Number';

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
                    </div>
                </div>


                @auth
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">

                                <div class="">

                                    <div class="p-2 col-lg-6">
                                        <strong>
                                            <h4>Rented numbers</h4>
                                            <p class="text-danger">No need to refresh the page to get the code.</p>
                                        </strong>
                                    </div>

                                    <div>


                                        <div class="card shadow-sm border-0">
                                            <div
                                                class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 text-white">Verification Requests</h6>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table  table-striped align-middle mb-0"
                                                       id="verificationTable">
                                                    <thead class="table-light">
                                                    <tr>
                                                        <th>Service</th>
                                                        <th>Phone No</th>
                                                        <th>Code</th>
                                                        <th>Time Left</th>
                                                        <th>Price</th>
                                                        <th>Status</th>
                                                        <th>Date</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @forelse($verification as $data)
                                                        <tr>
                                                            <td>{{ $data->service }}</td>
                                                            <td class="text-success fw-semibold">{{ $data->phone }}</td>


                                                            <td>
                                                                <div id="smsContainer{{ $data->id }}">
                                                                    <div class="d-flex align-items-center gap-2" id="loader{{ $data->id }}">
                                                                        <div class="spinner-border text-danger spinner-border-sm" role="status"></div>
                                                                        <span class="text-muted small">Waiting for code...</span>
                                                                    </div>

                                                                    <!-- Main Code -->
                                                                    <span id="data-sm{{ $data->id }}" class="sms-code text-success fw-semibold d-none"
                                                                          style="cursor:pointer;" title="Click to copy"></span>

                                                                    <!-- Extra Codes -->
                                                                    <div id="extraSmsList{{ $data->id }}" class="small text-light mt-1 d-none"></div>
                                                                </div>

                                                                <script>
                                                                    document.addEventListener('DOMContentLoaded', () => {
                                                                        const id = {{ $data->id }};
                                                                        const phone = `{{ $data->phone }}`;
                                                                        const type = {{ $data->type }};
                                                                        const smsSpan = document.getElementById(`data-sm${id}`);
                                                                        const loader = document.getElementById(`loader${id}`);
                                                                        const extraList = document.getElementById(`extraSmsList${id}`);
                                                                        let countdownTimer = null;
                                                                        let lastCodes = [];

                                                                        // URLs
                                                                        const mainUrl = type === 3
                                                                            ? `{{ url('get-smscode-usa2') }}?num=${phone}`
                                                                            : `{{ url('get-smscode') }}?num=${phone}`;
                                                                        const fetchUrl = `{{ url('check-more-sms') }}?num=${phone}`;

                                                                        console.log(`[INIT] Watching for SMS on ${phone}, mainUrl: ${mainUrl}`);

                                                                        // ========== COUNTDOWN HANDLER ==========
                                                                        async function startCountdown() {
                                                                            console.log(`[COUNTDOWN] Starting countdown for ID ${id}`);
                                                                            try {
                                                                                const res = await fetch('{{ url('getInitialCountdown') }}?id={{ $data->id }}');
                                                                                const data = await res.json();
                                                                                let secs = data.seconds || 0;
                                                                                const countdownDisplay = document.getElementById('secondsDisplay' + id);
                                                                                if (countdownDisplay) countdownDisplay.textContent = secs;

                                                                                countdownTimer = setInterval(() => {
                                                                                    secs--;
                                                                                    if (countdownDisplay) countdownDisplay.textContent = secs;
                                                                                    if (secs <= 0) {
                                                                                        clearInterval(countdownTimer);
                                                                                        console.log(`[COUNTDOWN] Time up for ID ${id}, deleting order...`);
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

                                                                        @if($data->status == 1)
                                                                        startCountdown();
                                                                        @endif

                                                                        async function fetchMainSMS() {
                                                                            console.log(`[CHECK] Checking main SMS for ${phone} ...`);
                                                                            try {
                                                                                const res = await fetch(mainUrl);
                                                                                const data = await res.json();
                                                                                console.log('[MAIN RESPONSE]', data);
                                                                                const msg = data?.message?.trim();

                                                                                if (msg && msg.length > 0) {
                                                                                    console.log(`[FOUND] Main code for ${phone}: ${msg}`);
                                                                                    loader.classList.add('d-none');
                                                                                    smsSpan.classList.remove('d-none');
                                                                                    smsSpan.textContent = msg;

                                                                                    smsSpan.addEventListener('click', () => {
                                                                                        navigator.clipboard.writeText(msg).then(() => {
                                                                                            smsSpan.innerHTML = msg + ' <i class="bi bi-check2 text-success"></i>';
                                                                                            setTimeout(() => smsSpan.textContent = msg, 500);
                                                                                        });
                                                                                    });

                                                                                    if (countdownTimer) clearInterval(countdownTimer);
                                                                                } else {
                                                                                    console.log(`[NOT FOUND] Still waiting for code for ${phone}`);
                                                                                }
                                                                            } catch (err) {
                                                                                console.error('[MAIN FETCH ERROR]', err);
                                                                            }
                                                                        }

                                                                        // ========== FETCH EXTRA CODES ==========
                                                                        async function fetchExtraCodes() {
                                                                            console.log(`[CHECK] Checking extra codes for ${phone} ...`);
                                                                            try {
                                                                                const res = await fetch(fetchUrl);
                                                                                const result = await res.json();
                                                                                console.log('[EXTRA RESPONSE]', result);

                                                                                const messages = Array.isArray(result) ? result : result.codes || [];

                                                                                if (messages.length > 0) {
                                                                                    loader.classList.add('d-none');
                                                                                    extraList.classList.remove('d-none');

                                                                                    if (countdownTimer) clearInterval(countdownTimer);

                                                                                    if (JSON.stringify(messages) !== JSON.stringify(lastCodes)) {
                                                                                        extraList.innerHTML = '';
                                                                                        messages.forEach((msg, index) => {
                                                                                            const code = msg.sms ?? msg;
                                                                                            const isNew = !lastCodes.some(old => (old.sms ?? old) === code);

                                                                                            const div = document.createElement('div');
                                                                                            div.className = 'border-bottom py-1 d-flex justify-content-between align-items-center code-line';
                                                                                            div.innerHTML = `
                                    <span class="text-dark">${code}</span>
                                    <span class="badge bg-success bg-opacity-75"></span>
                                `;
                                                                                            extraList.appendChild(div);

                                                                                            div.addEventListener('click', () => navigator.clipboard.writeText(code));

                                                                                            if (isNew) {
                                                                                                div.classList.add('flash-green');
                                                                                                setTimeout(() => div.classList.remove('flash-green'), 2000);
                                                                                            }
                                                                                        });

                                                                                        lastCodes = messages;
                                                                                    }
                                                                                } else {
                                                                                    console.log(`[NONE] No extra codes yet for ${phone}`);
                                                                                }
                                                                            } catch (err) {
                                                                                console.error('[EXTRA FETCH ERROR]', err);
                                                                            }
                                                                        }

                                                                        // ========== RUN BOTH CHECKERS ==========
                                                                        function updateAll() {
                                                                            fetchMainSMS();
                                                                            fetchExtraCodes();
                                                                        }

                                                                        updateAll();
                                                                        setInterval(updateAll, 10000);
                                                                    });
                                                                </script>

                                                                <style>
                                                                    .flash-green {
                                                                        background-color: rgba(0, 255, 0, 0.3);
                                                                        animation: flashFade 2s ease-out;
                                                                    }

                                                                    @keyframes flashFade {
                                                                        0% { background-color: rgba(0, 255, 0, 0.4); }
                                                                        100% { background-color: transparent; }
                                                                    }
                                                                </style>
                                                            </td>


                                                            {{-- COUNTDOWN TIMER --}}
                                                            <td>
                                                                @if($data->status == 1)
                                                                    <p class="text-danger fw-bold mb-0"
                                                                       id="secondsDisplay{{$data->id}}"></p>

                                                                    <script>
                                                                        async function fetchSeconds{{$data->id}}() {
                                                                            const res = await fetch('{{url('getInitialCountdown')}}?id={{$data->id}}');
                                                                            const data = await res.json();
                                                                            return data.seconds || 0;
                                                                        }

                                                                        async function startCountdown{{$data->id}}() {
                                                                            let secs = await fetchSeconds{{$data->id}}();
                                                                            const display = document.getElementById('secondsDisplay{{$data->id}}');
                                                                            display.textContent = secs;
                                                                            const timer = setInterval(() => {
                                                                                secs--;
                                                                                display.textContent = secs;
                                                                                if (secs <= 0) {
                                                                                    clearInterval(timer);
                                                                                    fetch('{{url('api/delete-order')}}', {
                                                                                        method: 'POST',
                                                                                        headers: {'Content-Type': 'application/json'},
                                                                                        body: JSON.stringify({id: {{$data->id}}})
                                                                                    }).then(() => location.reload());
                                                                                }
                                                                            }, 1000);
                                                                        }

                                                                        document.addEventListener('DOMContentLoaded', startCountdown{{$data->id}});
                                                                    </script>
                                                                @endif
                                                            </td>

                                                            <td>₦{{ number_format($data->cost, 2) }}</td>

                                                            {{-- STATUS + ACTIONS --}}
                                                            <td>
                                                                @if ($data->status == 1)
                                                                    <span
                                                                        class="badge bg-warning text-dark">Pending</span>
                                                                    <form method="POST"
                                                                          action="{{ $data->type === 3 ? url('delete-order-usa2?id='.$data->id.'&delete=1') : url('delete-order?id='.$data->id.'&delete=1') }}"
                                                                          class="d-inline-block"
                                                                          onsubmit="return confirmDelete(event, this);">
                                                                        @csrf
                                                                        <button type="submit"
                                                                                class="btn btn-sm btn-danger ms-1">
                                                                            Delete
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    <span class="badge bg-success">Completed</span>
                                                                @endif
                                                            </td>

                                                            <td>{{ $data->created_at }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="8" class="text-center text-muted py-3">No
                                                                verification found
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        {{-- SweetAlert Confirm --}}
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

                                        {{-- Live Search --}}
                                        <script>
                                            document.getElementById("searchInput").addEventListener("keyup", function () {
                                                const filter = this.value.toLowerCase();
                                                document.querySelectorAll("#verificationTable tbody tr").forEach(row => {
                                                    const text = row.textContent.toLowerCase();
                                                    row.style.display = text.includes(filter) ? "" : "none";
                                                });
                                            });
                                        </script>

                                    </div>


                                </div>


                            </div>
                        </div><!-- [ sample-page ] end -->

                    </div>
                @endauth
            </div>
        </div>

    </section>



    <script>
        function filterServices() {
            var input, filter, serviceRows, serviceNames, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            serviceRows = document.getElementsByClassName("service-row");
            for (i = 0; i < serviceRows.length; i++) {
                serviceNames = serviceRows[i].getElementsByClassName("service-name");
                txtValue = serviceNames[0].textContent || serviceNames[0].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    serviceRows[i].style.display = "";
                } else {
                    serviceRows[i].style.display = "none";
                }
            }
        }
    </script>

    <script>
        function hideButtondelete(link) {
            // Hide the clicked link
            link.style.display = 'none';

            setTimeout(function () {
                link.style.display = 'inline'; // or 'block' depending on your layout
            }, 5000); // 5 seconds
        }
    </script>


    <script>

        $.ajaxSetup({
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('access_token')
            }
        });

        $.get('/api/user', function (response) {
            console.log(response);
        });


    </script>

@endsection
