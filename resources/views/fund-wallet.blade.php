@extends('layout.main')
@section('content')

    <section id="technologies mt-4 my-5">





        <div class="container title my-5">


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
                                <a href="{{ url('us') }}"
                                   class="btn btn-light btn-lg px-4 py-2"
                                   style="font-weight: bold;
                              border-radius: 25px;
                              box-shadow: 0 2px 10px rgba(0,0,0,0.25);
                              transition: 0.3s;">
                                    <i class="fas fa-home me-2 text-primary"></i>
                                    Home
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>



            <div class="row justify-content-center text-center wow fadeInUp" data-wow-delay="0.2s">
                <h6 class="col-md-8 col-xl-6">
                    <h4 class="mb-3 text-danger">Hi {{ Auth::user()->username }},</h4>
                    <p class="mb-0">
                        <span class="text-danger">IMPORTANT</span> | Make sure to click pay to get a new account number
                        each time you want to fund your
                        wallet.<br>
                        Make sure to pay in the exact amount you inputted.<br>
                    <hr>
                    <a href="https://t.me/acesmsverify/8">click here for tutorial on how to deposit .</a>
                    </p>
            </div>
        </div>





        <div class="container technology-block">

            <div class="p-3">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
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
            </div>



            <div class="row p-3">

                @php
                    $get_account = \App\Models\PaymentPoint::where('email', Auth::user()->email)->first() ?? null;
                @endphp

                {{-- LEFT SIDE: ACCOUNT DETAILS OR GENERATE BUTTON --}}
                <div class="col-xl-6 col-md-6 col-sm-12">
                    <div class="card">
                        <div class="card-body p-0">

                            <div class="card-header border-0 text-white"
                                 style="background: linear-gradient(90deg, rgba(23, 69, 132, 1), rgba(0, 123, 255, 1));">
                                <h5 class="mb-0 text-white">Bank Account Details</h5>
                                <small style="opacity: .9;">Use the details below to fund your wallet</small>
                            </div>

                            <div class="p-3">

                                @if($get_account != null)

                                    {{-- ACCOUNT DETAILS --}}
                                    <div class="form-group my-3">
                                        <h6 class="text-muted mb-1">Account No</h6>

                                        <div class="d-flex align-items-center">
                                            <p class="mb-0 font-weight-bold mr-2" id="accountNoText">
                                                {{$account_no ?? "NIL"}}
                                            </p>

                                            @if(!empty($account_no))
                                                <button type="button"
                                                        class="btn btn-sm btn-light border"
                                                        onclick="copyAccountNo()"
                                                        title="Copy Account Number">
                                                    📋
                                                </button>

                                                <small class="text-success ml-2 d-none" id="copyMsg">Copied!</small>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group my-3">
                                        <h6 class="text-muted mb-1">Account Name</h6>
                                        <p class="mb-0 font-weight-bold">{{$account_name ?? "NIL"}}</p>
                                    </div>

                                    <div class="form-group my-3">
                                        <h6 class="text-muted mb-1">Bank Name</h6>
                                        <p class="mb-0 font-weight-bold">{{$bank_name ?? "NIL"}}</p>
                                    </div>

                                    <div class="alert alert-light border mt-4 mb-0">
                                        <small class="text-muted">
                                            ⚡ This account can receive transactions within seconds and can be reused anytime for future deposits.
                                        </small>
                                    </div>

                                @else

                                    {{-- GENERATE BUTTON --}}
                                    <button type="button"
                                            class="btn btn-lg w-100 mt-3 border-0"
                                            style="background: rgba(23, 69, 132, 1); color: white;"
                                            data-toggle="modal"
                                            data-target="#generateAccountModal">
                                        Get Account Number
                                    </button>

                                @endif

                            </div>

                        </div>
                    </div>
                </div>




                <div class="col-xl-6 col-md-6 col-sm-12">
                    <div class="card">
                        <div class="card-body p-0">


                            <div class="card-header border-0 text-white"
                                 style="background: linear-gradient(90deg, rgba(23, 69, 132, 1), rgba(0, 123, 255, 1));">
                                <h5 class="mb-0 text-white">Fund Wallet</h5>
                                <small style="opacity: .9;">Enter information below to fund wallet</small>
                            </div>

                            <div class="p-3">
                                <form class="my-2" action="fund-now" method="POST">
                                    @csrf

                                    <label class="my-2">Enter the Amount (NGN)</label>
                                    <input type="text" name="amount" class="form-control" max="999999" min="5"
                                           placeholder="Enter the Amount you want Add" required>

                                    <label class="my-2 mt-4">Select Payment mode</label>
                                    <select name="type" class="form-control">
                                        <option value="1">Instant</option>
                                        @if($status == "ON")
                                            <option value="2">Manual</option>
                                        @endif
                                    </select>

                                    <button style="border: 0px; background: rgba(23, 69, 132, 1); color: white;"
                                            type="submit"
                                            class="btn btn-lg w-100 mt-3 border-0">
                                        Add Funds
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>


                {{-- MODAL OUTSIDE IF (so it always exists) --}}
                <div class="modal fade" id="generateAccountModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">Generate Account</h5>

                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 28px;">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <form action="/generate-account" method="post">
                                    @csrf

                                    <div class="form-group my-3">
                                        <label>Full Name</label>
                                        <input type="text" name="fullname" class="form-control" placeholder="Enter name" required>
                                    </div>

                                    <div class="form-group my-3">
                                        <label>Phone Number</label>
                                        <input type="text" name="phone" class="form-control" placeholder="Enter phone number" required>
                                    </div>

                                    <button type="submit" id="generateBtn" class="btn btn-primary w-100">
                                        <span id="btnText">Generate</span>
                                        <span id="btnLoader" class="spinner-border spinner-border-sm ml-2 d-none" role="status"></span>
                                    </button>
                                </form>
                            </div>

                        </div>
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
                                        <h5 class="text-white mb-1" style="font-weight: bold;">📱 Get Social Media
                                            Accounts</h5>
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
                    <button class="carousel-control-prev" type="button" data-bs-target="#promoCarousel"
                            data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#promoCarousel"
                            data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>


                {{-- TRANSACTIONS --}}
                <div class="col-lg-12 col-sm-12 mt-4">



                    <div class="card border-0 shadow-lg p-3 mb-5 bg-body rounded-40">

                        <div class="card-header border-0 text-white"
                             style="background: linear-gradient(90deg, rgba(23, 69, 132, 1), rgba(0, 123, 255, 1));">
                            <h5 class="mb-0 text-white">Latest Transaction</h5>
                            <small style="opacity: .9;">See all transactions below</small>
                        </div>

                        <div class="card-body">




                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($transaction as $data)
                                        <tr>
                                            <td style="font-size: 12px;">{{ $data->id }}</td>

                                            <td style="font-size: 12px;">
                                                ₦{{ number_format($data->amount, 2) }}
                                            </td>

                                            <td>
                                                @if ($data->status == 1)
                                                    <span style="background: orange; border:0px; font-size: 10px"
                                                          class="btn btn-warning btn-sm">
                                            Pending
                                        </span>
                                                @elseif ($data->status == 2)
                                                    <span style="font-size: 10px;"
                                                          class="text-white btn btn-success btn-sm">
                                            Completed
                                        </span>
                                                @endif
                                            </td>

                                            <td style="font-size: 12px;">{{ $data->created_at }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">
                                                <h6 class="text-muted">No transaction found</h6>
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>

                                {{ $transaction->links() }}
                            </div>

                        </div>
                    </div>
                </div>

            </div>





        </div>
    </section>

    <script>
        function copyAccountNo() {
            let text = document.getElementById("accountNoText").innerText.trim();

            navigator.clipboard.writeText(text).then(function () {
                let msg = document.getElementById("copyMsg");
                msg.classList.remove("d-none");

                setTimeout(() => {
                    msg.classList.add("d-none");
                }, 1500);
            });
        }
    </script>

@endsection
