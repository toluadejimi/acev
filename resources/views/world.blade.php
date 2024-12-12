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

            <div class="row">
                <div class="col-xl-6 col-md-6 col-sm-12 my-3">
                    <div class="card">
                        <div class="card-body">
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


                            <div class="d-flex justify-content-center my-3">

                                <div class="btn-group" role="group" aria-label="Third group">
                                    <a style="font-size: 12px; background: rgba(23, 69, 132, 1); color: white"
                                       href="/us" class="btn  w-200 mt-1">
                                        🇺🇸 USA NUMBERS
                                    </a>

                                    <a style="font-size: 12px; box-shadow: deeppink" href="/home"
                                       class="btn btn-dark w-200 mt-1">
                                        🌎 ALL COUNTRIES

                                    </a>


                                </div>

                            </div>


                            <form action="check-av" method="POST">
                                @csrf

                                <div class="row">

                                    <div class="col-xl-10 col-md-10 col-sm-12 p-3">

                                        <p class="d-flex justify-content-center">You are on all 🌎 countries Panel</p>


                                        <p class="mb-3 text-muted d-flex justify-content-center"> Choose country and
                                            service
                                        </p>

                                        <hr>


                                        <label for="country" class="mb-2  mt-3 text-muted">🌎 Select
                                            Country</label>
                                        <div>
                                            <select style="border-color:rgb(0, 11, 136); padding: 10px" class="w-100"
                                                    id="dropdownMenu" class="dropdown-content" name="country">
                                                <option style="background: black" value=""> Select Country</option>
                                                @foreach ($countries as $data)
                                                    <option value="{{ $data['ID'] }}">{{ $data['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>


                                        <label for="country" class="mt-3 text-muted mb-2">💬 Select
                                            Services</label>
                                        <div>
                                            <select class="form-control w-100" id="select_page2" name="service">

                                                <option value=""> Choose Service</option>
                                                @foreach ($services as $data)
                                                    <option value="{{ $data['ID'] }}">{{ $data['name'] }}
                                                    </option>
                                                @endforeach

                                            </select>
                                        </div>


                                        <button style="border: 0px; background: rgba(23, 69, 132, 1); color: white;"
                                                type="submit"
                                                class="btn btn btn-lg w-100 mt-3 border-0">Check
                                            availability
                                        </button>


                                    </div>
                                </div>
                            </form>


                        </div>

                    </div>
                </div>


                <div class="col-xl-6 col-md-6 col-sm-12 p-3">

                    @if ($product != null)
                        <div class="card mb-3">
                            <div class="card-body">

                                <div class="row">
                                    <p class="text-muted text-center">Service Information</p>

                                    <h5 class="text-center my-2">Amount</h5>
                                    <h6 class="text-center text-muted my-2 mb-4">Price:
                                        NGN {{ number_format($price, 2) }}</h6>


                                    <h5 class="text-center text-muted my-2">Success rate: <span
                                            style="font-size: 30px; color: rgba(23, 69, 132, 1);"> @if ($rate < 10)
                                                {{ $rate }}%
                                            @elseif ($rate < 20)
                                                {{ $rate }}%
                                            @elseif ($rate < 30)
                                                {{ $rate }}%
                                            @elseif ($rate < 40)
                                                {{ $rate }}%
                                            @elseif ($rate < 50)
                                                {{ $rate }}%
                                            @elseif ($rate < 60)
                                                {{ $rate }}%
                                            @elseif ($rate < 70)
                                                {{ $rate }}%
                                            @elseif ($rate < 80)
                                                {{ $rate }}%

                                            @elseif ($rate < 90)
                                                {{ $rate }}%
                                            @elseif ($rate <= 100)
                                                {{ $rate }}%
                                            @else
                                            @endif</span></h5>
                                    <h6></h6>


                                    @if (Auth::user()->wallet < $price)
                                        <a href="fund-wallet" class="btn btn-secondary text-white btn-lg">Fund
                                            Wallet</a>
                                    @else
                                        <form action="order_now" method="POST">
                                            @csrf

                                            <input type="text" name="country" hidden value="{{ $count_id ?? null }}">
                                            <input type="text" name="price" hidden value="{{ $price ?? null }}">
                                            <input type="text" name="price2" hidden value="{{ $price ?? null }}">
                                            <input type="text" name="price3" hidden value="{{ $price ?? null }}">
                                            <input type="text" name="price4" hidden value="{{ $price ?? null }}">
                                            <input type="text" name="service" hidden value="{{ $serv ?? null }}">


                                            <button type="submit"
                                                    style="border: 0px; background: rgba(23, 69, 132, 1); color: white;"
                                                    class="mb-2 btn btn w-100 btn-lg mt-6" onclick="this.style.display='none'">Buy Number
                                                Now
                                            </button>


                                            <p class="text-muted text-center my-5">
                                                At AceSMSVerify, we prioritize quality, ensuring that you receive the
                                                highest standard of SMS verifications for all your needs. Our commitment
                                                to excellence means we only offer non-VoIP phone numbers, guaranteeing
                                                compatibility with any service you require.
                                            </p>


                                        </form>
                                    @endif


                                </div>


                            </div>

                        </div>
                    @endif
                </div>


            </div>

            <div class="col-xl-12 col-md-12 col-sm-12 my-3">


                <div class="card">
                    <div class="card-body">
                        <div class="card-header">
                            <h6>Ongoing Orders</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead style="background: rgb(159,16,47); border-radius: 10px; color: white">
                                <tr>
                                    <th class="text-white">ID</th>
                                    <th class="text-white">Service</th>
                                    <th class="text-white">Phone</th>
                                    <th class="text-white">SMS</th>
                                    <th class="text-white">Time Remain</th>
                                    <th class="text-white">Amount</th>
                                    <th class="text-white">Action</th>
                                    <th class="text-white">Date / Time</th>



                                </tr>
                                </thead>


                                @forelse($orders as $data)
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
                                                @keyframes l14{
                                                    100%{transform: rotate(1turn)}
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
                                                @keyframes l1 {to{transform: rotate(.5turn)}}
                                            </style>

                                            <td>
                                                <div id="l1" class="justify-content-start">
                                                </div>
                                                <div>
                                                    <input class="border-0 justify-content-end" id="response-input{{$data->id}}">
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

                                                <a href="delete-order?id={{  $data->id }}&delete=1"
                                                   style="background: rgb(168, 0, 14); border:0px; font-size: 10px"
                                                   onclick="hideButtondelete(this)"
                                                   class="btn btn-warning btn-sm">Delete</span>

                                                    @else
                                                        <span style="font-size: 10px;"
                                                              class="text-white btn btn-success btn-sm">Completed</span>
                                            @endif

                                        </td>
                                        <td style="font-size: 12px;">{{ $data->created_at }}</td>
                                    </tr>

                                @empty

                                    No Verification found

                              @endforelse

                            </table>
                        </div>


                    </div>

                </div>


            </div>

        </div>


    </section>








    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const table = document.getElementById('data-table');
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const countdownElement = row.cells[2]; // Assumes "Expires" is in the third column (index 2)
                let seconds = parseInt(countdownElement.getAttribute('data-seconds'), 10);

                const countdownInterval = setInterval(function () {
                    countdownElement.textContent = seconds + 's';

                    if (seconds <= 0) {
                        clearInterval(countdownInterval);
                        // Add your logic to handle the expiration, e.g., sendPostRequest(row);
                        console.log('Expired:', row);
                    }

                    seconds--;
                }, 1000);
            });

            // You may add the sendPostRequest function here or modify the code accordingly
        });
    </script>

    <script>
        $(document).ready(function () {
            //change selectboxes to selectize mode to be searchable
            $("select").select2();
        });
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

@endsection
