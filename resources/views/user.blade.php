@extends('layout.admin')
@section('content')

    <main class="main-content">


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
                                    <a href="unban-users?id={{$trx->id}}" class="btn btn-warning btn-sm">Unban</a>
                                @endif

                                   <a href="view-verifications?user_id={{$trx->id}}" class="btn btn-success btn-sm">Verification</a>
                                   <a href="view-trx?user_id={{$trx->id}}" class="btn btn-primary btn-sm">Transactions</a>

                                   <form action="{{ route('delete-user', $trx->id) }}" method="POST" style="display:inline;">
                                       @csrf
                                       @method('DELETE')
                                       <button type="submit" class="btn btn-danger btn-sm"
                                               onclick="return confirm('Are you sure you want to delete this user?');">
                                           Delete
                                       </button>
                                   </form>



                                   <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#fundsModal-{{ $trx->id }}">
                                       Add/Remove Funds
                                   </a>

                                   <!-- Modal -->
                                   <div class="modal fade" id="fundsModal-{{ $trx->id }}" tabindex="-1" aria-labelledby="fundsModalLabel-{{ $trx->id }}" aria-hidden="true">
                                       <div class="modal-dialog">
                                           <div class="modal-content">

                                               <div class="modal-header">
                                                   <h5 class="modal-title" id="fundsModalLabel-{{ $trx->id }}"> Manage Funds for {{ $trx->username }} | Balance {{number_format($trx->wallet, 2)}}</h5>
                                                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                               </div>

                                               <form action="{{ route('user.funds.update', $trx->id) }}" method="POST">
                                                   @csrf
                                                   <div class="modal-body">
                                                       <!-- Action Type -->
                                                       <div class="mb-3">
                                                           <label for="actionType" class="form-label">Action</label>
                                                           <select name="action" id="actionType" class="form-select" required>
                                                               <option value="add">Add Funds</option>
                                                               <option value="remove">Remove Funds</option>
                                                           </select>
                                                       </div>

                                                       <!-- Amount -->
                                                       <div class="mb-3">
                                                           <label for="amount" class="form-label">Amount</label>
                                                           <input type="number" name="amount" id="amount" class="form-control" min="1" required>
                                                       </div>

                                                       <!-- Optional Note -->
                                                       <div class="mb-3">
                                                           <label for="note" class="form-label">Note (optional)</label>
                                                           <textarea name="note" id="note" class="form-control"></textarea>
                                                       </div>
                                                   </div>

                                                   <div class="modal-footer">
                                                       <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                       <button type="submit" class="btn btn-success btn-sm">Submit</button>
                                                   </div>
                                               </form>
                                           </div>
                                       </div>
                                   </div>

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
