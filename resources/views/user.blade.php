@extends('layout.admin')
@section('content')

    <main class="main-content">
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
            <div class="alert alert-success">{{ session()->get('message') }}</div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger">{{ session()->get('error') }}</div>
        @endif

        <header class="header">
            <div class="header-left">
                <div class="admin-badge">ADMIN</div>
                <div class="admin-role">Manage users</div>
            </div>
        </header>

        <section class="welcome-section">
            <div class="breadcrumb">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </div>
            <div class="welcome-message">
                <h1>All Users</h1>
                <p>Search users, manage balances, and login as user.</p>
            </div>
        </section>

        <section class="my-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <p class="text-muted mb-1">Total users</p>
                            <h3 class="mb-0">{{ number_format($user ?? 0) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <form action="{{ url('search-user') }}" method="get" class="row g-2 align-items-end">
                                <div class="col-md-9">
                                    <label class="form-label">Search user</label>
                                    <input name="search" class="form-control" placeholder="Email or username" required>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-primary w-100" type="submit">Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="transactions-section">
            <div class="table-container card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Wallet</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th style="min-width: 380px;">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $trx)
                                <tr>
                                    <td>{{ $trx->email }}</td>
                                    <td>{{ $trx->username }}</td>
                                    <td>₦{{ number_format($trx->wallet, 2) }}</td>
                                    <td>
                                        @if($trx->status == 2)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $trx->created_at->format('d M, Y H:i A') }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @if($trx->status == 2)
                                                <a href="{{ url('ban-user?id=' . $trx->id) }}" class="btn btn-danger btn-sm">Ban</a>
                                            @else
                                                <a href="{{ url('unban-users?id=' . $trx->id) }}" class="btn btn-warning btn-sm">Unban</a>
                                            @endif
                                            <a href="{{ url('view-verifications?user_id=' . $trx->id) }}" class="btn btn-success btn-sm">Verification</a>
                                            <a href="{{ url('view-trx?user_id=' . $trx->id) }}" class="btn btn-primary btn-sm">Transactions</a>

                                            <form action="{{ route('admin.login-as-user', $trx->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-dark btn-sm"
                                                        onclick="return confirm('Login as {{ $trx->username }} now?');">
                                                    Login as user
                                                </button>
                                            </form>

                                            <a href="#" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#fundsModal-{{ $trx->id }}">
                                                Add/Remove Funds
                                            </a>

                                            <form action="{{ route('delete-user', $trx->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('Are you sure you want to delete this user?');">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>

                                        <div class="modal fade" id="fundsModal-{{ $trx->id }}" tabindex="-1" aria-labelledby="fundsModalLabel-{{ $trx->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="fundsModalLabel-{{ $trx->id }}">Manage Funds · {{ $trx->username }} (₦{{ number_format($trx->wallet, 2) }})</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('user.funds.update', $trx->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="actionType{{ $trx->id }}" class="form-label">Action</label>
                                                                <select name="action" id="actionType{{ $trx->id }}" class="form-select" required>
                                                                    <option value="add">Add Funds</option>
                                                                    <option value="remove">Remove Funds</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="amount{{ $trx->id }}" class="form-label">Amount</label>
                                                                <input type="number" name="amount" id="amount{{ $trx->id }}" class="form-control" min="1" required>
                                                            </div>
                                                            <div class="mb-0">
                                                                <label for="note{{ $trx->id }}" class="form-label">Note (optional)</label>
                                                                <textarea name="note" id="note{{ $trx->id }}" class="form-control"></textarea>
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
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-3">{{ $users->links() }}</div>
        </section>
    </main>

@endsection
