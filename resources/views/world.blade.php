@extends('layout.main')
@section('content')

    <section id="technologies mt-4 my-5">
        <div class="container technology-block">

            <!-- Wallet Card -->
            <div class="col-lg-12 col-md-12 mt-4">
                <div class="card" style="background: linear-gradient(135deg, #000102 0%, #0c0c0c 100%);
                border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.15); border-radius: 15px;">
                    <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-wallet fa-3x text-white"></i>
                            </div>
                            <div>
                                <h5 class="text-white mb-1" style="font-weight: 600;">
                                    {{ Auth::user()->username }}
                                </h5>
                                <h3 class="text-white mb-0" style="font-weight: bold;">
                                    ₦{{ number_format(Auth::user()->wallet ?? 0, 2) }}
                                </h3>
                                <p class="text-white-50 mb-0" style="font-size: 13px;">Available Balance</p>
                            </div>
                        </div>
                        <div>
                            <a href="{{ url('fund-wallet') }}" class="btn btn-light btn-lg px-4 py-2"
                               style="font-weight: bold; border-radius: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.25); transition: 0.3s;">
                                <i class="fas fa-coins me-2 text-primary"></i> Fund Wallet
                            </a>
                        </div>
                    </div>
                </div>
            </div>


            @if(session('topMessage'))
                <div id="top-popup" class="popup-banner">
                    <div class="popup-content">
                        <span>{{ session('topMessage') }}</span>
                    </div>
                </div>
            @endif


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



            <!-- Country and Service Selector -->
            <div class="row mt-4">
                <div class="col-xl-6 col-md-6 col-sm-12 my-3">
                    <div class="card">
                        <div class="card-body">



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


                                </div>

                            </div>

                            <p class="text-center mb-0">🌎 Choose Country & Service</p>
                            <hr>

                            <form id="worldForm" method="POST">
                                @csrf
                                <label class="mt-3 text-muted mb-2">🌎 Select Country</label>
                                <select id="countrySelect" name="country" class="form-control">
                                    <option value="">Select Country</option>
                                    @foreach ($countries as $data)
                                        <option value="{{ $data['ID'] }}">{{ $data['name'] }}</option>
                                    @endforeach
                                </select>

                                <label class="mt-3 text-muted mb-2">💬 Select Service</label>
                                <select id="serviceSelect" name="service" class="form-control" disabled>
                                    <option value="">Select a Country First</option>
                                </select>

                                <div id="priceSection" style="display: none;">
                                    <h5 class="text-center mt-4">💰 Price:</h5>
                                    <h3 class="text-center" id="priceDisplay">Loading...</h3>
                                    <button type="button" id="buyNowBtn"
                                            class="btn btn-primary w-100 mt-3"
                                            style="background: rgb(0,4,9); border: none;">Buy Number Now
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <div class="col-xl-6 col-md-6 col-sm-12 my-3">

                    <div class="card shadow-sm border-0">
                        <div
                            class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
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
                                                    <div class="spinner-border text-danger spinner-border-sm"
                                                         role="status"></div>
                                                    <span class="text-muted small">Waiting for code...</span>
                                                </div>

                                                <!-- Main Code -->
                                                <span id="data-sm{{ $data->id }}"
                                                      class="sms-code text-success fw-semibold d-none"
                                                      style="cursor:pointer;" title="Click to copy"></span>

                                                <!-- Extra Codes -->
                                                <div id="extraSmsList{{ $data->id }}"
                                                     class="small text-light mt-1 d-none"></div>
                                            </div>

                                            <script>
                                                document.addEventListener('DOMContentLoaded', () => {
                                                    const id = {{ $data->id }};
                                                    const status = {{ $data->status }};
                                                    const phone = `{{ $data->phone }}`;
                                                    const type = {{ $data->type }};
                                                    const smsSpan = document.getElementById(`data-sm${id}`);
                                                    const loader = document.getElementById(`loader${id}`);
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
                                                            const msg = data?.message?.trim();

                                                            if (msg && msg.length > 0) {
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

                                                            if (messages.length > 0) {
                                                                loader.classList.add('d-none');
                                                                extraList.classList.remove('d-none');
                                                                if (countdownTimer) clearInterval(countdownTimer);

                                                                if (JSON.stringify(messages) !== JSON.stringify(lastCodes)) {
                                                                    extraList.innerHTML = '';
                                                                    messages.forEach(msg => {
                                                                        const code = msg.sms ?? msg;
                                                                        const div = document.createElement('div');
                                                                        div.className = 'border-bottom py-1 d-flex justify-content-between align-items-center code-line';
                                                                        div.innerHTML = `<span class="text-dark">${code}</span>`;
                                                                        extraList.appendChild(div);
                                                                        div.addEventListener('click', () => navigator.clipboard.writeText(code));
                                                                    });
                                                                    lastCodes = messages;
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

                                                    // ✅ Logic
                                                    if (status === 1) {
                                                        startCountdown();
                                                        updateAll();
                                                        setInterval(updateAll, 30000);
                                                    } else {
                                                        updateAll();
                                                    }
                                                });
                                            </script>

                                            <style>
                                                .flash-green {
                                                    background-color: rgba(0, 255, 0, 0.3);
                                                    animation: flashFade 2s ease-out;
                                                }

                                                @keyframes flashFade {
                                                    0% {
                                                        background-color: rgba(0, 255, 0, 0.4);
                                                    }
                                                    100% {
                                                        background-color: transparent;
                                                    }
                                                }
                                            </style>
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
    </section>

    <!-- SweetAlert + Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            $("select").select2();

            // Load services when country is selected
            $('#countrySelect').on('change', function () {
                const countryID = $(this).val();
                $('#serviceSelect').html('<option>Loading...</option>').prop('disabled', true);

                if (countryID) {
                    $.ajax({
                        url: '/get-world-services/' + countryID,
                        type: 'GET',
                        success: function (res) {
                            $('#serviceSelect').empty().append('<option value="">Select Service</option>');
                            res.forEach(service => {
                                $('#serviceSelect').append(`<option value="${service.ID}">${service.name}</option>`);
                            });
                            $('#serviceSelect').prop('disabled', false);
                        }
                    });
                }
            });

            // When service selected → check availability
            $('#serviceSelect').on('change', function () {
                const country = $('#countrySelect').val();
                const service = $(this).val();

                if (country && service) {
                    $.ajax({
                        url: '/check-world-availability',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            country: country,
                            service: service
                        },
                        beforeSend: function () {
                            $('#priceSection').hide();
                            Swal.fire({
                                title: 'Checking...',
                                text: 'Please wait',
                                allowOutsideClick: false,
                                didOpen: () => Swal.showLoading()
                            });
                        },
                        success: function (res) {
                            Swal.close();
                            if (res.status === 'success') {
                                $('#priceSection').show();
                                $('#priceDisplay').text('₦' + res.price);
                                $('#buyNowBtn').data('country', country).data('service', service).data('price', res.price);
                            } else {
                                Swal.fire('Unavailable', res.message, 'error');
                            }
                        },
                        error: function () {
                            Swal.close();
                            Swal.fire('Error', 'Unable to check availability', 'error');
                        }
                    });
                }
            });

            // Buy number button
            $('#buyNowBtn').on('click', function () {
                const btn = $(this);
                const country = btn.data('country');
                const service = btn.data('service');
                const price = btn.data('price');

                Swal.fire({
                    title: 'Confirm Purchase',
                    text: `Buy number for ₦${price}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Buy Now',
                    cancelButtonText: 'Cancel'
                }).then((res) => {
                    if (res.isConfirmed) {

                        // 🔒 Disable button and show loading spinner
                        btn.prop('disabled', true)
                            .html('<i class="fas fa-spinner fa-spin me-2"></i> Getting number...');

                        $.ajax({
                            url: '/order-world-number',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                country: country,
                                service: service,
                                price: price
                            },
                            success: function (resp) {
                                // Try to parse if response is JSON
                                try {
                                    resp = JSON.parse(resp);
                                } catch (e) {
                                    // Leave as-is if not JSON
                                }

                                if (resp === 3 || resp.status === 3 || resp.status === 'success') {
                                    Swal.fire('Success', 'Please wait, page reloading....', 'success')
                                        .then(() => location.reload());
                                } else if (resp === 99) {
                                    Swal.fire('Error', 'Insufficient balance', 'error');
                                } else if (resp === 5) {
                                    Swal.fire('Error', 'Service unavailable or failed', 'error');
                                } else if (resp === 8) {
                                    Swal.fire('Error', 'Wallet not found. Please try again later.', 'error');
                                } else {
                                    Swal.fire('Error', resp.message || 'Something went wrong', 'error');
                                }
                            },
                            error: function () {
                                Swal.fire('Error', 'Unable to process your request', 'error');
                            },
                            complete: function () {
                                btn.prop('disabled', false)
                                    .html('Buy Number Now');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
