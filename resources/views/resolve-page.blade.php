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
                            @auth
                                <div class="col-4">
                                    <a href="fund-wallet">
                                        <h3 class="mt-2 d-flex text-white justify-content-end">
                                            N{{number_format(Auth::user()->wallet, 2)}}</h3>
                                    </a>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div><!-- [ breadcrumb ] end --><!-- [ Main Content ] start -->


            <div class="col-lg-12 col-sm-12 d-flex justify-content-center">
                <div class="card border-0 mb-5 rounded-20">
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


                                <form action="resolve-now" method="POST">
                                    @csrf
                                    <p style="font-size: 12px" class="mb-4"> Resolve pending transactions by using your
                                        bank session ID / Refrence No on your
                                        transaction recepit
                                    <p>

                                        <strong><h6 class="mb-3">{{ $ref }}</h6></strong>

                                        <label class="my-2">Enter Session ID</label>
                                        <input class="form-control" name="session_id" required autofocus>
                                        <input hidden value="{{ $ref }}" name="trx_ref">


                                        <button type="submit" class="text-white btn w-100 btn-primary my-4">
                                            Add Funds
                                        </button>
                                </form>
                            </div>


                        </div>

                    </div>
                </div>


            </div>


        </div>

@endsection
