@extends('layout.admin')
@section('content')

    <main class="main-content">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <div class="admin-badge">ADMIN</div>
                <div class="admin-role">Transactions</div>
            </div>
            <div class="header-right">
                <button class="header-btn">
                    <i class="fas fa-download"></i>
                    Export CSV
                </button>
            </div>
        </header>

        <!-- Page Title -->
        <section class="welcome-section">
            <div class="breadcrumb">
                <i class="fas fa-exchange-alt"></i>
                <span>Users</span>
            </div>
            <div class="welcome-message">
                <h1>All Users</h1>
                <p>Monitor all Users</p>
            </div>
        </section>


        <section class="my-5">

            <div class="row">


                <div class="col-6">

                    <div class="card">

                        <div class="card-body">

                            <form action="search-user" method="get">
                                @csrf
                                <label>Search for user</label>
                                <input name="search" class="form-control my-3" placeholder="Email / Username" required>
                                <button class="form-control btn btn-sm btn-primary" type="submit"> Search User </button>
                            </form>
                        </div>
                    </div>



                </div>

            </div>
        </section>

        <!-- Transactions Table -->
        <section class="transactions-section">
            <div class="table-container">
                <table class="transactions-table">
                    <thead>
                    <tr>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Wallet Balance</th>
                        <th>Status</th>
                        <th>Joined Date</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $trx)
                        <tr>
                            <td>{{$trx->email}}</td>
                            <td>{{$trx->username}}</td>
                            <td>₦{{number_format($trx->wallet, 2)}}</td>
                            <td>
                                @if($trx->status == 2)
                                    <span style="color: #014473" class="badge badge-success">Active</span>
                                @else
                                    <span style="color: #fd690d" class="badge badge-warning">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $trx->created_at->format('d M, Y H:i A') }}</td>

                            <td>
                               @if($trx->status == 2)
                                    <a href="ban-user?id={{$trx->id}}" class="btn btn-danger btn-sm">Ban</a>
                                @else
                                    <a href="unban-user" class="btn btn-warning btn-sm">Unban</a>
                                @endif

                                   <a href="view-verifications?user_id={{$trx->id}}" class="btn btn-success btn-sm">Verification</a>
                                   <a href="view-trx?user_id={{$trx->id}}" class="btn btn-primary btn-sm">Transactions</a>


                            </td>

                        </tr>
                    @endforeach
                    </tbody>

                    {{$users->links()}}
                </table>
            </div>
        </section>
    </main>

@endsection
