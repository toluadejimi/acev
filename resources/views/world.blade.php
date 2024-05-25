@extends('layout.main')
@section('content')

    <section id="technologies mt-4 my-5">
        <div class="container title my-5">
            <div class="row justify-content-center text-center wow fadeInUp" data-wow-delay="0.2s">
                <div class="col-md-8 col-xl-6">
                    <h4 class="mb-3 text-danger">Welcome {{ Auth::user()->username }}</h4>
                    <p class="mb-0">
                        Experience the AceSMSVerify advantage today and unlock seamless,<br/> reliable SMS verifications
                        for all your needs
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
                                                    <option value="{{ $data->ID }}">{{ $data->name }}
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
                                                    <option value="{{ $data->ID }}">{{ $data->name }}
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
                                            <input type="text" name="service" hidden value="{{ $serv ?? null }}">


                                            <button type="submit"
                                                    style="border: 0px; background: rgba(23, 69, 132, 1); color: white;"
                                                    class="mb-2 btn btn w-100 btn-lg mt-6">Buy Number
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
                                    <th class="text-white">Phone</th>
                                    <th class="text-white">SMS</th>
                                    <th class="text-white">Amount</th>
                                    <th class="text-white">Status</th>
                                    <th class="text-white">Action</th>

                                </tr>
                                </thead>


                                @foreach ($orders as $data)

                                    <tbody>

                                    <tr>

                                        <td>
                                            {{ $data->id }}
                                        </td>
                                        <td>
                                            {{ $data->phone }}
                                        </td>
                                        <td>
                                            {{ $data->sms}}
                                        </td>

                                        <td>
                                            {{ number_format($data->cost, 2) }}
                                        </td>

                                        @if($data->status == 2)
                                            <td class="text-success">
                                                Delivered
                                            </td>
                                        @else
                                            <td class="text-warning">
                                                Pending
                                            </td>
                                        @endif

                                        <td>
                                            <a href="delete-order?id={{$data->id}}"
                                               class="btn btn-sm btn-dark text-small">Delete</a>
                                        </td>

                                    </tr>


                                    </tbody>

                                @endforeach

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

@endsection
