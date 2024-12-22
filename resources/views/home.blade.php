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


                            <div class="">

                                <div class="p-2 col-lg-6">
                                    <input type="text" id="searchInput" class="form-control"
                                           placeholder="Search for a service..." onkeyup="filterServices()">
                                </div>


                                <div class="row my-3 p-1 text-white"
                                     style="background: #dedede; border-radius: 10px; font-size: 10px; border-radius: 12px">
                                    <div class="col-5">
                                        <h5 class="mt-2">Services</h5>
                                    </div>
                                    <div class="col">
                                        <h5 class="mt-2">Price</h5>
                                    </div>
                                </div>


                            </div>


                            <div style="height:150px; width:100%; overflow-y: scroll;" class="p-2">


                                @foreach ($services as $key => $value)
                                    <div class="row service-row">
                                        @foreach ($value as $innerKey => $innerValue)
                                            <div style="font-size: 11px" class="col-5 service-name">
                                                {{ $innerValue->name }}
                                            </div>

                                            <div style="font-size: 11px" class="col">
                                                @php $cost = $get_rate * $innerValue->cost + $margin  @endphp
                                                <strong>N{{ number_format($cost, 2) }}</strong>
                                            </div>

                                            <div style="font-size: 11px" class="col">

                                            </div>


                                            <div class="col mr-3">
                                                @auth

                                                    @if(Auth::user()->wallet < $cost)

                                                        <a href="fund-wallet" style="color: #7c7c7c"><i
                                                                class="fas fa-wallet"></i></a>

                                                    @else
                                                        <form action="order-usano" method="POST">
                                                            @csrf

                                                            <input hidden name="service" value="{{ $key }}">
                                                            <input hidden name="price" value="{{ $cost }}">
                                                            <input hidden name="price2" value="{{ $cost }}">
                                                            <input hidden name="price3" value="{{ $cost }}">
                                                            <input hidden name="price4" value="{{ $cost }}">
                                                            <input hidden name="cost" value="{{ $innerValue->cost }}">
                                                            <input hidden name="name" value="{{ $innerValue->name }}">
                                                            <button class="myButton"
                                                                    style="border: 0px; background: transparent"
                                                                    onclick="this.style.display='none'"><i
                                                                    class="fa fa-shopping-bag"></i></button>
                                                        </form>

                                                    @endif

                                                @else

                                                    <a class=""
                                                       href="/login">
                                                        <i class="fa fa-lock text-dark"></i>
                                                    </a>
                                                @endauth


                                                <script>
                                                    function hideButton(link) {
                                                        // Hide the clicked link
                                                        link.style.display = 'none';

                                                        setTimeout(function () {
                                                            link.style.display = 'inline'; // or 'block' depending on your layout
                                                        }, 5000); // 5 seconds
                                                    }
                                                </script>


                                            </div>


                                            <hr style="border-color: #cccccc" class=" my-2">
                                        @endforeach
                                    </div>
                                @endforeach


                            </div>


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
                                                                <a href="delete-order?id={{  $data->id }}&delete=1"
                                                                   style="background: rgb(168, 0, 14); border:0px; font-size: 10px"
                                                                   class="btn btn-warning btn-sm hideButton">Delete</span>

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

        // Example AJAX request
        $.get('/api/user', function(response) {
            console.log(response);
        });


    </script>

@endsection
