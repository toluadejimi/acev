@extends('layout.main')
@section('content')

    <div class="pc-container">
        <div class="pc-content"><!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">

                        </div>
                        <div class="col-12 row">
                            <div class="col-8">
                                <div class="page-header-title">
                                    <h2 class="d-flex justify-content-start">Welcome</h2>
                                </div>
                            </div>
                            <div class="col-4">
                                <a href="fund-wallet">
                                    <h3 class="mt-2 d-flex text-white justify-content-end">
                                        N{{number_format(Auth::user()->wallet, 2)}}</h3>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->


            <div class="row p-3">
                <div class="col-xl-6 col-md-6 col-sm-12">
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


                            <h4 class="text-small text-center mb-2">Pay<br>
                                NGN{{ number_format($amount, 2) }}</h4>


                            <div
                                class="text-center  mt-1 mb-2 text-sm justify-center items-stretch  self-stretch mt-2 px-6 py-3 rounded-xl">
                                Add funds to wallet by sending to the<br> account details below.<br>
                                <p class="text-danger">Manual Funding is only available from 10am - 10pm</p>
                            </div>


                            <div class="col-lg-12 col-sm-12">
                                <div class="card border-0 shadow-lg text-center p-3 mb-5 bg-body rounded-40">
                                    <div class="card-body">
                                        <div class="">

                                            <strong text-black class="my-2">Bank Name</strong><br>
                                            <p class="mb-2 text-small">{{ $account_details->bank_name }}</p>

                                            <hr>

                                            <strong text-black class="my-2">Account Name</strong><br>
                                            <p class="mb-2 text-small">{{ $account_details->account_name }}</p>

                                            <hr>

                                            <strong text-black class="my-2">Account Number</strong><br>
                                            <p text-small>{{ $account_details->bank_account }}</p>


                                        </div>

                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-xl-6 col-md-6 col-sm-12">
                    <div class="card border-0 shadow-lg p-3 my-2 bg-body rounded-40">
                        <div class="card-body">
                            <div class="">
                                <form id="myDiv" action="fund-manual-now" enctype="multipart/form-data"
                                      text-center class="grid grid-cols-1"
                                      gap-3 method="POST">
                                    @csrf

                                    <label class="my-2 text-gray-600 text-center font-bold gap-3 text-sm">Upload
                                        Payment prove</label>

                                    <div class="flex mt-2">

                                        <input type="file"
                                               class="form-control"
                                               id="amount" type="number" max="999999" min="5" name="receipt"
                                               required>
                                    </div>

                                    <input name="amount" hidden value="{{ $amount }}">

                                    <button type="submit" class="text-white btn w-100 btn-primary my-4">
                                        Add Funds
                                    </button>
                                </form>


                            </div>
                        </div>


                    </div>


                </div>
            </div>

@endsection
