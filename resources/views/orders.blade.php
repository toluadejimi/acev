@extends('layout.main')
@section('content')

    <section id="technologies mt-4 my-5">
        <div class="container title my-5">
            <div class="row justify-content-center text-center wow fadeInUp" data-wow-delay="0.2s">
                <div class="col-md-8 col-xl-6">
                    <h4 class="mb-3 text-danger">Hi {{ Auth::user()->username }},</h4>
                    <p class="mb-0">
                        Experience the AceSMSVerify advantage today and unlock seamless,<br/> reliable SMS verifications
                        for all your needs
                    </p>
                </div>
            </div>
        </div>


        <div class="container technology-block">
            <div class="col-lg-12 col-sm-12 d-flex justify-content-center">
                <div class="card border-0 mb-5 rounded-20">
                    <div class="card-body">

                        <div class="card-header d-flex justify-content-center mb-3">
                            <h5 class="">My Orders</h5>
                        </div>


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


                        <div class="col-xl-12 col-md-12 col-sm-12  justify-center">


                            <div class="card">
                                <div class="card-body">
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

                    </div>


                </div>


            </div>
        </div>
    </section>

@endsection
