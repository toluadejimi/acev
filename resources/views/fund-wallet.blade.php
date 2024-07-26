@extends('layout.main')
@section('content')

    <section id="technologies mt-4 my-5">
        <div class="container title my-5">
            <div class="row justify-content-center text-center wow fadeInUp" data-wow-delay="0.2s">
                <h6 class="col-md-8 col-xl-6">
                    <h4 class="mb-3 text-danger">Hi {{ Auth::user()->username }},</h4>
                    <p class="mb-0">
                    <span class="text-danger">IMPORTANT</span> | Make sure to click pay to get a new account number each time you want to fund your
                        wallet.<br>
                        Make sure to pay in the exact amount you inputted.<br>
                    <hr>
                    <a href="https://t.me/acesmsverify/8">click here for tutorial on how to deposit .</a>
                    </p>
                </div>
            </div>
        </div>


        <div class="container technology-block">

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


                            <form action="fund-now" method="POST">
                                @csrf

                                <label class="my-2">Enter the Amount (NGN)</label>
                                <input type="text" name="amount" class="form-control" max="999999" min="5" name="amount"
                                       placeholder="Enter the Amount you want Add" required>


                                <label class="my-2 mt-4">Select Payment mode</label>
                                <select name="type" class="form-control">
                                    <option value="1">Instant</option>
                                    @if($status == "ON")
                                     <option value="2">Manual</option>
                                    @else
                                    @endif
                                </select>


                                <button style="border: 0px; background: rgba(23, 69, 132, 1); color: white;"
                                        type="submit"
                                        class="btn btn btn-lg w-100 mt-3 border-0">Add Funds
                                </button>
                            </form>


                        </div>

                    </div>
                </div>

                <div class="col-lg-6 col-sm-12">
                    <div class="card border-0 shadow-lg p-3 mb-5 bg-body rounded-40">

                        <div class="card-body">


                            <div class="">

                                <div class="p-2 col-lg-6">
                                    <strong>
                                        <h4>Latest Transactions</h4>
                                    </strong>
                                </div>

                                <div>


                                    <div class="table-responsive ">
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


                                                    <td style="font-size: 12px;">₦{{ number_format($data->amount, 2) }}


                                                    <td>
                                                        @if ($data->status == 1)
                                                            <span
                                                                style="background: orange; border:0px; font-size: 10px"
                                                                class="btn btn-warning btn-sm">Pending</span>
                                                        @elseif ($data->status == 2)
                                                            <span style="font-size: 10px;"
                                                                  class="text-white btn btn-success btn-sm">Completed</span>
                                                        @else
                                                        @endif

                                                    </td>

                                                    <td style="font-size: 12px;">{{ $data->created_at }}

                                                </tr>

                                            @empty

                                                <h6>No transaction found</h6>
                                            @endforelse

                                            </tbody>

                                            {{ $transaction->links() }}

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
