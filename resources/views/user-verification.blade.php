@extends('layout.admin')
@section('content')

    <main class="main-content">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <div class="admin-badge">ADMIN</div>
                <div class="admin-role">{{$user->username}}</div>
            </div>
        </header>

        <!-- Page Title -->
        <section class="welcome-section">
            <div class="breadcrumb">
                <i class="fas fa-exchange-alt"></i>
                <span>Users</span>
            </div>
            <div class="welcome-message">
                <h1>{{$user->username}}</h1>
                <p>User Data</p>
            </div>
        </section>



        <section class="transactions-section">

            <div class="table-container" style="max-height:400px; overflow-y:auto;">
                <h6>Recent Verification</h6>
                <table class="transactions-table">
                    <thead>
                    <tr>
                        <th>Trx ID</th>
                        <th>Username</th>
                        <th>Phone</th>
                        <th>Country</th>
                        <th>Service</th>
                        <th>Provider</th>
                        <th>SMS</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th> Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($verification as $trx)
                        <tr>
                            <td>{{$trx->id}}</td>
                            <td>{{$trx->user->username}}</td>
                            <td>{{$trx->phone}}</td>
                            <td>{{$trx->country}}</td>
                            <td>{{$trx->service}}</td>
                            <td>@if($trx->type == 1)
                                    <span style="color: #014473" class="badge badge-success">USA · SV1</span>
                                @elseif($trx->type == 8)
                                    <span style="color: #014473" class="badge badge-success">SMS POOL</span>
                                @elseif($trx->type == 9)
                                    <span style="color: #014473" class="badge badge-success">HERO SMS</span>
                                @elseif($trx->type == 3)
                                    <span style="color: #014473" class="badge badge-success">UNLIMITED SMS</span>
                                @else
                                    <span style="color: #64748b" class="badge badge-secondary">UNKNOWN</span>
                                @endif
                            </td>

                            <td>{{$trx->sms}}</td>
                            <td>₦{{number_format($trx->cost, 2)}}</td>

                            <td>@if($trx->status == 2)
                                    <span style="color: #014473" class="badge badge-success">Completed</span>
                                @else
                                    <span style="color: #fd690d" class="badge badge-danger">Pending</span>

                                @endif
                            </td>


                            <td>{{ $trx->created_at->format('d M, Y H:i A') }}</td>



                        </tr>
                    @endforeach
                    </tbody>

                    {{$verification->links()}}
                </table>
            </div>
        </section>




    </main>

@endsection
