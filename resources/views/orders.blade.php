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
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Phone</th>
                                                <th>SMS</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Action</th>

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


                </div>


            </div>
        </div>
    </section>

@endsection
