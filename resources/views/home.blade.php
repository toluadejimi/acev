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
                                            🇺🇸 USA NUMBERS
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
                                    <input type="text" id="searchInput" class="form-control"
                                           placeholder="Search for a service...">

                                    <!-- Settings icon -->
                                    <button class="btn btn-outline-secondary ms-2" id="toggleSettings" type="button">
                                        <i class="bi bi-gear"></i>
                                    </button>
                                </div>

                                <!-- Dropdown (services) -->
                                <div id="servicesDropdown" class="list-group mt-2 position-absolute w-100  bg-white shadow-sm"
                                     style="max-height: 750px; overflow-y: auto; display:none; z-index:1000;">
                                    @foreach ($services as $key => $value)
                                        @foreach ($value as $innerKey => $innerValue)
                                            @php $cost = $get_rate * $innerValue->cost + $margin; @endphp
                                            <a href="javascript:void(0);"
                                               class="list-group-item list-group-item-action service-option"
                                               data-service="{{ $innerValue->name }}"
                                               data-key="{{ $key }}"
                                                data-cost="{{ $cost }}">
                                                <span style="font-size: 12px">{{ $innerValue->name }}</span>
                                                <span class="float-end"><strong>N{{ number_format($cost, 2) }}</strong></span>
                                            </a>
                                        @endforeach
                                    @endforeach
                                </div>

                                <!-- Extra fields (hidden by default) -->
                                <div id="extraFields" class="card p-2 shadow-sm mt-2" style="display: none;">
                                    <div class="mb-2">
                                        <label class="my-1">Area codes</label>
                                        <input type="text" id="areaCode" class="form-control" placeholder="503, 202, 404">
                                    </div>
                                    <div>
                                        <label class="my-1">Carriers</label>
                                        <select id="carrier" class="form-control" placeholder="Enter Carrier">
                                            <option value=" "> Any Carrier</option>
                                            <option value="tmo"> T-Mobile</option>
                                            <option value="vz">Verizon</option>
                                            <option value="att">AT&T</option>

                                        </select>
                                    </div>


                                    <p style="color:#8e0404;" class="my-3 d-flex justify-content-center">This may attract extra 20% charge</p>
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
                                const serviceOptions = document.querySelectorAll(".service-option");
                                const rentButton = document.getElementById("rentNumberBtn");

                                let selectedService = null;
                                let selectedCost = null;
                                let selectedKey = null;


                                searchInput.addEventListener("focus", () => {
                                    servicesDropdown.style.display = "block";
                                });

                                searchInput.addEventListener("keyup", () => {
                                    const filter = searchInput.value.toLowerCase();
                                    let visibleCount = 0;

                                    serviceOptions.forEach(option => {
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

                                serviceOptions.forEach(option => {
                                    option.addEventListener("click", function () {
                                        selectedService = this.dataset.service;
                                        selectedCost = this.dataset.cost;
                                        selectedKey = this.dataset.key;

                                        searchInput.value = `${selectedService} - ₦${selectedCost}`;
                                        servicesDropdown.style.display = "none";

                                        rentButton.disabled = false;
                                    });
                                });

                                document.addEventListener("click", (event) => {
                                    if (!searchInput.contains(event.target) && !servicesDropdown.contains(event.target) && !extraFields.contains(event.target)) {
                                        servicesDropdown.style.display = "none";
                                    }
                                });

                                document.getElementById("toggleSettings").addEventListener("click", function () {
                                    extraFields.style.display = (extraFields.style.display === "none") ? "block" : "none";
                                });


                                rentButton.addEventListener("click", () => {
                                    if (!selectedService) return;

                                    const areaCode = document.getElementById("areaCode").value;
                                    const carrier = document.getElementById("carrier").value;

                                    const payload = {
                                        key: selectedKey,
                                        service: selectedService,
                                        cost: selectedCost,
                                        areaCode: areaCode || null,
                                        carrier: carrier || null
                                    };

                                    console.log("Submitting:", payload);

                                    fetch("/order-usano", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/json",
                                            "X-CSRF-TOKEN": "{{ csrf_token() }}" // Laravel CSRF token
                                        },
                                        body: JSON.stringify(payload)
                                    })
                                        .then(response => response.json())
                                        .then(res => {
                                            console.log("Response:", res);
                                            if (res.status) {
                                                Swal.fire({
                                                    title: "Success 🎉",
                                                    text: res.message || "Your purchase was successful!",
                                                    icon: "success",
                                                    timer: 3000,
                                                    showConfirmButton: false
                                                });
                                            } else if (res === 1) {
                                                window.location.reload();
                                            } else {
                                                Swal.fire("Error ❌", res.message || "Purchase failed", "error");
                                            }
                                        })
                                        .catch(() => {
                                            Swal.fire("Error", "Something went wrong. Try again.", "error");
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


                                        <div class="table-responsive ">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Service</th>
                                                    <th>Phone No</th>
                                                    <th>Code</th>
                                                    <th>Time Remain</th>
                                                    <th>Price</th>
                                                    <th>Status</th>
                                                    <th>Date</th>


                                                </tr>
                                                </thead>
                                                <tbody>


                                                @forelse($verification as $data)
                                                    <tr>
                                                        <td style="font-size: 12px;">{{ $data->id }}</td>
                                                        <td style="font-size: 12px;">{{ $data->service }}</td>
                                                        <td style="font-size: 12px; color: green">{{ $data->phone }}
                                                        </td>

                                                        @if($data->sms != null)
                                                            <td style="font-size: 12px;">{{ $data->sms }}
                                                            </td>
                                                        @else
                                                            <style>
                                                                /* HTML: <div class="loader"></div> */
                                                                .loader {
                                                                    width: 50px;
                                                                    aspect-ratio: 1;
                                                                    display: grid;
                                                                    animation: l14 4s infinite;
                                                                }

                                                                .loader::before,
                                                                .loader::after {
                                                                    content: "";
                                                                    grid-area: 1/1;
                                                                    border: 8px solid;
                                                                    border-radius: 50%;
                                                                    border-color: red red #0000 #0000;
                                                                    mix-blend-mode: darken;
                                                                    animation: l14 1s infinite linear;
                                                                }

                                                                .loader::after {
                                                                    border-color: #0000 #0000 blue blue;
                                                                    animation-direction: reverse;
                                                                }

                                                                @keyframes l14 {
                                                                    100% {
                                                                        transform: rotate(1turn)
                                                                    }
                                                                }
                                                            </style>

                                                            <style>#l1 {
                                                                    width: 15px;
                                                                    aspect-ratio: 1;
                                                                    border-radius: 50%;
                                                                    border: 1px solid;
                                                                    border-color: #000 #0000;
                                                                    animation: l1 1s infinite;
                                                                }

                                                                @keyframes l1 {
                                                                    to {
                                                                        transform: rotate(.5turn)
                                                                    }
                                                                }
                                                            </style>

                                                            <td>
                                                                <div id="l1" class="justify-content-start">
                                                                </div>
                                                                <div>
                                                                    <input style=" " class="border-0"
                                                                           id="response-input{{$data->id}}">
                                                                </div>


                                                                <script>
                                                                    makeRequest{{$data->id}}();
                                                                    setInterval(makeRequest{{$data->id}}, 5000);

                                                                    function makeRequest{{$data->id}}() {
                                                                        fetch('{{ url('') }}/get-smscode?num={{ $data->phone }}')
                                                                            .then(response => {
                                                                                if (!response.ok) {
                                                                                    throw new Error(`HTTP error! Status: ${response.status}`);
                                                                                }
                                                                                return response.json();
                                                                            })
                                                                            .then(data => {

                                                                                console.log(data.message);
                                                                                displayResponse{{$data->id}}(data.message);

                                                                            })
                                                                            .catch(error => {
                                                                                console.error('Error:', error);
                                                                                displayResponse{{$data->id}}({
                                                                                    error: 'An error occurred while fetching the data.'
                                                                                });
                                                                            });
                                                                    }

                                                                    function displayResponse{{$data->id}}(data) {
                                                                        const responseInput = document.getElementById('response-input{{$data->id}}');
                                                                        responseInput.value = data;
                                                                    }

                                                                </script>
                                                            </td>
                                                        @endif

                                                        @if($data->status == 1)
                                                            <td><p style="font-size: 16px; color: #e00101"
                                                                   id="secondsDisplay{{$data->id}}"></p></td>
                                                            <script>
                                                                // Function to fetch initial countdown value from the database
                                                                async function fetchInitialCountdown{{$data->id}}() {
                                                                    try {
                                                                        const response = await fetch('{{url('')}}/getInitialCountdown?id={{$data->id}}');
                                                                        if (!response.ok) {
                                                                            throw new Error('Network response was not ok');
                                                                        }
                                                                        const data = await response.json();
                                                                        return data.seconds;
                                                                    } catch (error) {
                                                                        console.error('Error fetching initial countdown:', error);
                                                                        return 0;
                                                                    }
                                                                }

                                                                // Function to update the displayed countdown
                                                                function updateDisplay{{$data->id}}(seconds) {
                                                                    document.getElementById('secondsDisplay{{$data->id}}').textContent = seconds;
                                                                }

                                                                // Function to update the database with current seconds
                                                                function updateDatabase{{$data->id}}(seconds) {
                                                                    fetch('{{url('')}}/api/updatesec', {
                                                                        method: 'POST',
                                                                        headers: {
                                                                            'Content-Type': 'application/json',
                                                                        },

                                                                        body: JSON.stringify({
                                                                            id: {{$data->id}},
                                                                            secs: seconds,
                                                                        }),
                                                                    })
                                                                        .then(response => {
                                                                            if (!response.ok) {
                                                                                throw new Error('Network response was not ok');
                                                                            }
                                                                            console.log('Updated seconds:', seconds);
                                                                        })
                                                                        .catch(error => {
                                                                            console.error('Error updating seconds:', error);
                                                                        });
                                                                }


                                                                function updateStatus{{$data->id}}() {
                                                                    fetch('{{url('')}}/api/delete-order', {
                                                                        method: 'POST',
                                                                        headers: {
                                                                            'Content-Type': 'application/json',
                                                                        },
                                                                        body: JSON.stringify({
                                                                            id:{{$data->id}},
                                                                        }),
                                                                    })
                                                                        .then(response => {
                                                                            if (!response.ok) {
                                                                                throw new Error('Network response was not ok');
                                                                            }

                                                                            location.reload();

                                                                            console.log(response.json());
                                                                        })

                                                                        .catch(error => {
                                                                            console.error('Error updating status:', error);
                                                                        });
                                                                }

                                                                // Countdown timer
                                                                async function countdownTimer{{$data->id}}() {
                                                                    let seconds = await fetchInitialCountdown{{$data->id}}();
                                                                    // Initial update to start the countdown
                                                                    updateDisplay{{$data->id}}(seconds);
                                                                    updateDatabase{{$data->id}}(seconds);

                                                                    const interval = setInterval(function () {
                                                                        seconds--;

                                                                        // Update displayed seconds
                                                                        updateDisplay{{$data->id}}(seconds);

                                                                        // Update database every 5 seconds
                                                                        if (seconds % 5 === 0) {
                                                                            updateDatabase{{$data->id}}(seconds);
                                                                        }

                                                                        // When countdown reaches zero, update status, stop interval and update display
                                                                        if (seconds <= 0) {
                                                                            clearInterval(interval);
                                                                            updateStatus{{$data->id}}();
                                                                            updateDisplay{{$data->id}}(0);
                                                                        }
                                                                    }, 1000); // Timer ticks every second
                                                                }

                                                                document.addEventListener('DOMContentLoaded', function () {
                                                                    countdownTimer{{$data->id}}();
                                                                });
                                                            </script>
                                                        @endif

                                                        <td style="font-size: 12px;">
                                                            ₦{{ number_format($data->cost, 2) }}</td>
                                                        <td>
                                                            @if ($data->status == 1)
                                                                <span
                                                                    style="background: orange; border:0px; font-size: 10px"
                                                                    class="btn btn-warning btn-sm">Pending</span>

                                                                <form method="POST" action="delete-order?id={{ $data->id }}&delete=1"
                                                                      style="display: inline;"
                                                                      onsubmit="return confirmDelete(event, this);">
                                                                    @csrf
                                                                    <button type="submit"
                                                                            style="background: rgb(168, 0, 14); border:0px; font-size: 10px"
                                                                            class="btn btn-warning btn-sm hideButton">
                                                                        Delete
                                                                    </button>
                                                                </form>

                                                                <script>
                                                                    function confirmDelete(event, form) {
                                                                        event.preventDefault();

                                                                        Swal.fire({
                                                                            title: 'Are you sure?',
                                                                            text: "Do you want to cancel this order?",
                                                                            icon: 'question',
                                                                            showCancelButton: true,
                                                                            confirmButtonColor: '#3085d6',
                                                                            cancelButtonColor: '#d33',
                                                                            confirmButtonText: 'Proceed',
                                                                            cancelButtonText: 'Cancel'
                                                                        }).then((result) => {
                                                                            if (result.isConfirmed) {
                                                                                form.submit();
                                                                            }
                                                                        });

                                                                        return false;
                                                                    }
                                                                </script>

                                                            @else
                                                                <span style="font-size: 10px;"
                                                                      class="text-white btn btn-success btn-sm">Completed</span>
                                                            @endif



                                                            <script>
                                                                const buttons = document.querySelectorAll('.hideButton');
                                                                buttons.forEach(button => {
                                                                    button.addEventListener('click', function() {
                                                                        this.style.display = 'none';
                                                                    });
                                                                });
                                                            </script>

                                                        </td>
                                                        <td id="datetime{{$data->id}}"
                                                            style="font-size: 12px;">{{ $data->created_at }}</td>
                                                    </tr>

                                                @empty

                                                    <h6>No verification found</h6>
                                                @endforelse

                                                </tbody>


                                            </table>
                                        </div>
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
