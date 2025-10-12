@extends('layout.main')
@section('content')

    <section id="technologies mt-4 my-5">



        <div class="container technology-block">

            <div class="col-lg-12 col-md-12 mt-4">
                <div class="card"
                     style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
                border: none;
                box-shadow: 0 4px 15px rgba(0,0,0,0.15);
                border-radius: 15px;">
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">

                            <!-- Wallet Info -->
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

                            <!-- Fund Button -->
                            <div class="mt-3 mt-md-0">
                                <a href="{{ url('fund-wallet') }}"
                                   class="btn btn-light btn-lg px-4 py-2"
                                   style="font-weight: bold;
                              border-radius: 25px;
                              box-shadow: 0 2px 10px rgba(0,0,0,0.25);
                              transition: 0.3s;">
                                    <i class="fas fa-coins me-2 text-primary"></i>
                                    Fund Wallet
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


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
                                        <form action="order_now" method="POST" onsubmit="return confirmBuyNumberNow(event, this)">
                                            @csrf

                                            <input type="text" name="country" hidden value="{{ $count_id ?? null }}">
                                            <input type="text" name="price" hidden value="{{ $price ?? null }}">
                                            <input type="text" name="price2" hidden value="{{ $price ?? null }}">
                                            <input type="text" name="price3" hidden value="{{ $price ?? null }}">
                                            <input type="text" name="price4" hidden value="{{ $price ?? null }}">
                                            <input type="text" name="service" hidden value="{{ $serv ?? null }}">

                                            <button type="submit"
                                                    style="border: 0px; background: rgba(23, 69, 132, 1); color: white;"
                                                    class="mb-2 btn btn w-100 btn-lg mt-6">
                                                Buy Number Now
                                            </button>

                                            <p class="text-muted text-center my-5">
                                                At AceSMSVerify, we prioritize quality, ensuring that you receive the highest standard of SMS verifications for all your needs. Our commitment to excellence means we only offer non-VoIP phone numbers, guaranteeing compatibility with any service you require.
                                            </p>
                                        </form>

                                        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                                        <script>
                                            function confirmBuyNumberNow(event, form) {
                                                event.preventDefault();

                                                Swal.fire({
                                                    title: 'Confirm Purchase',
                                                    text: "Are you sure you want to buy this number now?",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonText: 'Yes, Buy it!',
                                                    cancelButtonText: 'Cancel',
                                                    confirmButtonColor: '#1f7ded',
                                                    cancelButtonColor: '#d33',
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        form.querySelector('button[type="submit"]').style.display = 'none';
                                                        form.submit();
                                                    }
                                                });

                                                return false;
                                            }
                                        </script>
                                    @endif


                                </div>


                            </div>

                        </div>
                    @endif
                </div>


            </div>


            <div id="promoCarousel" class="carousel slide mt-4" data-bs-ride="carousel">
                <div class="carousel-inner">

                    <!-- Card 1 -->
                    <div class="carousel-item active">
                        <div class="col-lg-12 col-md-12">
                            <div class="card"
                                 style="background: linear-gradient(135deg, #2990d8 0%, #022843 100%);
                    border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                                <div class="card-body text-center p-4">
                                    <h5 class="text-white mb-1" style="font-weight: bold;">📱 Get Social Media Accounts</h5>
                                    <p class="text-white-50 mb-2">For all types of social accounts</p>
                                    <a href="https://acelogstores.com" target="_blank"
                                       class="btn btn-light btn-lg px-4 py-2"
                                       style="font-weight: bold; border-radius: 25px;">
                                        Visit Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2 -->

                    <div class="carousel-item">
                        <div class="col-lg-12 col-md-12">
                            <div class="card"
                                 style="background: linear-gradient(135deg, #353333 0%, #010f19 100%);
                    border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                                <div class="card-body text-center p-4">
                                    <h5 class="text-white mb-1" style="font-weight: bold;">📱 Boost your Engagement</h5>
                                    <p class="text-white-50 mb-2">with more followers and likes</p>
                                    <a href="https://aceboosts.com" target="_blank"
                                       class="btn btn-light btn-lg px-4 py-2"
                                       style="font-weight: bold; border-radius: 25px;">
                                        Boost Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>



                </div>

                <!-- Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#promoCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#promoCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
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
