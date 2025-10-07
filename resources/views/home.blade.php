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


                            <p class="d-flex justify-content-center">You are on 🇺🇸 USA Numbers only Panel</p>


                            <div class="p-2 col-lg-12 position-relative">
                                <!-- Search input + settings -->
                                <div class="d-flex align-items-center">
                                    <input type="text" id="searchInput" class="form-control" placeholder="Search for a service...">
                                    <button class="btn btn-outline-secondary ms-2" id="toggleSettings" type="button">
                                        <i class="bi bi-gear"></i>
                                    </button>
                                </div>

                                <!-- Dropdown (services) -->
                                <div id="servicesDropdown" class="list-group mt-2 position-absolute w-100 bg-white shadow-sm"
                                     style="max-height: 750px; overflow-y: auto; display:none; z-index:1000;">
                                    @foreach ($allServices as $service)
                                        @php $cost = $get_rate * $service->cost + $margin; @endphp
                                        <a href="javascript:void(0);"
                                           class="list-group-item list-group-item-action service-option"
                                            data-service="{{ $service->name }}"
                                            data-provider="{{ $service->provider }}"
                                            data-cost="{{ $service->cost }}"
                                            data-price="{{ number_format($cost, 2, '.', '') }}">
                                            <span style="font-size: 12px">{{ $service->name }}</span>
                                            <span class="float-end"><strong>N{{ number_format($cost, 2) }}</strong></span>
                                        </a>
                                    @endforeach
                                </div>

                                <!-- Extra fields -->
                                <div id="extraFields" class="card p-2 shadow-sm mt-2" style="display: none;">
                                    <div class="mb-2">
                                        <label class="my-1">Area codes</label>
                                        <input type="text" id="areaCode" class="form-control" placeholder="503, 202, 404">
                                    </div>
                                    <div>
                                        <label class="my-1">Carriers</label>
                                        <select id="carrier" class="form-control" placeholder="Enter Carrier">
                                            <option value="">Any Carrier</option>
                                            <option value="tmo">T-Mobile</option>
                                            <option value="vz">Verizon</option>
                                            <option value="att">AT&T</option>
                                        </select>
                                    </div>
                                    <p style="color:#8e0404;" class="my-3 d-flex justify-content-center">This may attract extra 20% charge</p>
                                </div>
                            </div>

                            <div class="list-group-item text-center bg-light">
                                <button style="background: rgba(23, 69, 132, 1); color: white; border-color: transparent" id="rentNumberBtn" class="btn btn-primary w-100" disabled>
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
                                                style="background: rgba(23, 69, 132, 1); color: white;" class="card-header text-white d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 text-white">Verification Requests</h6>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table  table-striped align-middle mb-0"
                                                       id="verificationTable">
                                                    <thead class="table-light">
                                                    <tr>
                                                        <th>ID</th>
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
                                                            <td>{{ $data->id }}</td>
                                                            <td>{{ $data->service }}</td>
                                                            <td class="text-success fw-semibold">{{ $data->phone }}</td>


                                                            <td>
                                                                @if($data->sms)
                                                                    <div
                                                                        class="d-flex align-items-center justify-content-between">
                                                                            <span id="data-sm{{ $data->id }}"
                                                                                  class="sms-code"
                                                                                  style="cursor: pointer;"
                                                                                  title="Click to copy">
                                                                                {{ $data->sms }}
                                                                            </span>

                                                                        <button
                                                                            class="btn btn-sm btn-outline-primary ms-3"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#extraSmsModal{{ $data->id }}">
                                                                            <i class="bi bi-chat-dots"></i>
                                                                        </button>
                                                                    </div>
                                                                @else
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <div
                                                                            class="spinner-border text-danger spinner-border-sm"
                                                                            role="status"></div>
                                                                        <input
                                                                            class="form-control form-control-sm border-0"
                                                                            id="response-input{{ $data->id }}" readonly>
                                                                    </div>

                                                                    <script>
                                                                        const endpoint{{ $data->id }} = '{{ $data->type === 3 ? url('get-smscode-usa2?num='.$data->phone) : url('get-smscode?num='.$data->phone) }}';

                                                                        function updateSMS{{ $data->id }}() {
                                                                            fetch(endpoint{{ $data->id }})
                                                                                .then(res => res.json())
                                                                                .then(data => document.getElementById('response-input{{ $data->id }}').value = data.message)
                                                                                .catch(err => console.error(err));
                                                                        }

                                                                        updateSMS{{ $data->id }}();
                                                                        setInterval(updateSMS{{ $data->id }}, 5000);
                                                                    </script>
                                                                @endif

                                                                <!-- Modal -->
                                                                <div class="modal fade"
                                                                     id="extraSmsModal{{ $data->id }}" tabindex="-1"
                                                                     aria-hidden="true">
                                                                    <div
                                                                        class="modal-dialog modal-dialog-centered modal-lg">
                                                                        <div
                                                                            class="modal-content glassy-modal p-3 position-relative">
                                                                            <button type="button"
                                                                                    class="btn-close position-absolute end-0 top-0 m-3"
                                                                                    data-bs-dismiss="modal"></button>

                                                                            <div class="modal-header border-0">
                                                                                <h5 class="modal-title fw-semibold text-white">
                                                                                    <i class="bi bi-chat-square-text me-2"></i>
                                                                                    Extra SMS Codes
                                                                                </h5>
                                                                            </div>

                                                                            <div class="modal-body text-white">
                                                                                <div
                                                                                    class="d-flex justify-content-between align-items-center mb-3">
                                                                                    <small
                                                                                        class="text-light opacity-75">Phone: {{ $data->phone }}</small>
                                                                                    <button
                                                                                        class="btn btn-sm btn-outline-light"
                                                                                        id="refreshSmsBtn{{ $data->id }}">
                                                                                        <i class="bi bi-arrow-clockwise"></i>
                                                                                        Refresh
                                                                                    </button>
                                                                                </div>

                                                                                <div id="extraSmsList{{ $data->id }}"
                                                                                     class="list-group small border-0 rounded-3 glassy-list shadow-sm"
                                                                                     style="max-height: 300px; overflow-y: auto;">
                                                                                    <div
                                                                                        class="text-center py-3 text-light opacity-75"
                                                                                        id="smsLoading{{ $data->id }}">
                                                                                        Loading...
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <script>

                                                                    document.querySelectorAll('.sms-code').forEach(el => {
                                                                        el.addEventListener('click', () => {
                                                                            const sms = el.textContent.trim();
                                                                            navigator.clipboard.writeText(sms).then(() => {
                                                                                el.innerHTML = sms + ' <i class="bi bi-check2 text-success"></i>';
                                                                                setTimeout(() => {
                                                                                    el.innerHTML = sms;
                                                                                }, 1200);
                                                                            }).catch(err => {
                                                                                console.error('Copy failed:', err);
                                                                            });
                                                                        });
                                                                    });

                                                                    const modalTrigger{{ $data->id }} = document.querySelector('[data-bs-target="#extraSmsModal{{ $data->id }}"]');

                                                                    modalTrigger{{ $data->id }}.addEventListener('click', () => {
                                                                        const smsList = document.getElementById('extraSmsList{{ $data->id }}');
                                                                        const loading = document.getElementById('smsLoading{{ $data->id }}');
                                                                        smsList.innerHTML = '';
                                                                        loading.textContent = 'Fetching extra codes...';


                                                                        fetch(`{{ url('check-more-sms') }}?num={{ $data->phone }}`)
                                                                            .then(res => res.json())
                                                                            .then(data => {
                                                                                smsList.innerHTML = '';

                                                                                // ✅ Handle both array and object response
                                                                                const messages = Array.isArray(data) ? data : data.codes || [];

                                                                                if (messages.length > 0) {
                                                                                    messages.forEach((msg, index) => {
                                                                                        const code = msg.sms ?? msg; // support either object or string
                                                                                        smsList.innerHTML += `
                                                                                        <div class="list-group-item bg-transparent border-bottom d-flex justify-content-between align-items-center text-white clickable-code" data-code="${code}">
                                                                                            <span>${code}</span>
                                                                                            <span class="badge bg-success bg-opacity-75">#${index + 1}</span>
                                                                                        </div>`;
                                                                                    });

                                                                                    smsList.querySelectorAll('.clickable-code').forEach(el => {
                                                                                        el.addEventListener('click', () => {
                                                                                            const code = el.getAttribute('data-code');
                                                                                            fetch(`{{ url('check-more-sms') }}?num={{ $data->phone }}&code=${code}`)
                                                                                                .then(r => r.json())
                                                                                        });
                                                                                    });
                                                                                } else {
                                                                                    smsList.innerHTML = '<div class="text-center py-3 text-light opacity-75">No extra codes found</div>';
                                                                                }
                                                                            })
                                                                    });

                                                                    document.getElementById('refreshSmsBtn{{ $data->id }}').addEventListener('click', () => {
                                                                        const btn = document.getElementById('refreshSmsBtn{{ $data->id }}');
                                                                        btn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Reloading...';
                                                                        setTimeout(() => location.reload(), 700);
                                                                    });
                                                                </script>

                                                                <style>

                                                                    .spin {
                                                                        display: inline-block;
                                                                        animation: spin 0.8s linear infinite;
                                                                    }

                                                                    @keyframes spin {
                                                                        100% {
                                                                            transform: rotate(360deg);
                                                                        }
                                                                    }

                                                                    /* Glassmorphism Modal */
                                                                    .glassy-modal {
                                                                        background: rgba(20, 20, 40, 0.6);
                                                                        backdrop-filter: blur(18px);
                                                                        border-radius: 20px;
                                                                        border: 1px solid rgba(255, 255, 255, 0.1);
                                                                        color: #fff;
                                                                        animation: fadeInUp 0.4s ease;
                                                                    }

                                                                    /* Glassy List */
                                                                    .glassy-list .list-group-item {
                                                                        transition: background 0.3s, transform 0.2s;
                                                                    }

                                                                    .glassy-list .list-group-item:hover {
                                                                        background: rgba(255, 255, 255, 0.1);
                                                                        transform: scale(1.02);
                                                                        cursor: pointer;
                                                                    }

                                                                    /* Animation */
                                                                    @keyframes fadeInUp {
                                                                        from {
                                                                            opacity: 0;
                                                                            transform: translateY(15px);
                                                                        }
                                                                        to {
                                                                            opacity: 1;
                                                                            transform: translateY(0);
                                                                        }
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

        $.get('/api/user', function(response) {
            console.log(response);
        });


    </script>

@endsection
