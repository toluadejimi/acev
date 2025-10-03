@extends('layout.admin')
@section('content')

    <main class="main-content">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <div class="admin-badge">ADMIN</div>
                <div class="admin-role">{{$user->username ?? "user"}}</div>
            </div>
        </header>

        <!-- Page Title -->
        <section class="welcome-section">
            <div class="breadcrumb">
                <i class="fas fa-exchange-alt"></i>
                <span>Users</span>
            </div>
            <div class="welcome-message">
                <h1>{{$user->username ?? "user"}}</h1>
                <p>User Data</p>
            </div>
        </section>



        <section class="transactions-section">

            <div class="table-container" style="max-height:400px; overflow-y:auto;">
                <h6>All Transaction</h6>
                <table class="transactions-table">
                    <thead>
                    <tr>
                        <th>Trx ID</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Old Balance</th>
                        <th>New Balance</th>
                        <th>Status</th>
                        <th> Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($trasnaction as $trx)
                        <tr>
                            <td>{{$trx->ref_id}}</td>
                            <td>@if($trx->type == 2)
                                    <span style="color: #038221" class="badge badge-success">Credit</span>
                                @elseif($trx->type == 1)
                                    <span style="color: #8e0404" class="badge badge-success">Debit</span>
                                @endif
                            </td>
                            <td>₦{{number_format($trx->amount, 2)}}</td>
                            <td>₦{{number_format($trx->old_balance, 2)}}</td>
                            <td>₦{{number_format($trx->balance, 2)}}</td>
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

                    {{$trasnaction->links()}}
                </table>
            </div>
        </section>




    </main>

@endsection
