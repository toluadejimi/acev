@php use App\Models\User; @endphp
@extends('layout.admin')
@section('content')

    <main class="main-content">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <div class="admin-badge">ADMIN</div>
                <div class="admin-role">Administrator</div>
            </div>
            <div class="header-right">

                <a href="users"  class="header-btn">
                    <i class="fas fa-users"></i>
                    Manage Users
                </a>


                <a href="price-setting"  class="header-btn">
                    <i class="fas fa-money-bill"></i>
                    Price Setting
                </a>


            </div>
        </header>

        <!-- Welcome Section -->

        <!-- Stats Grid -->
        <section class="stats-grid">
            <!-- Top Row Stats -->
            <div class="stat-card">

                <div class="stat-icon inflow">
                    <i class="fas fa-wallet"></i>
                </div>

                <div class="stat-content">
                    <div class="stat-label">Total Funded</div>
                    <div class="stat-value">₦{{number_format($credit, 2)}}</div>

                    <div class="stat-status processed">
                        <i class="fas fa-check"></i>
                        Processed
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon outflow">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Spent</div>
                    <div class="stat-value">₦{{number_format($debit, 2)}}</div>
                    <div class="stat-status attention">Processed</div>

                </div>
            </div>



        </section>


        <section class="stats-grid">
            <div class="card">

                <div class="card-body">

                    <h5 class="my-3">Search Transaction</h5>

                    <form action="search-trx" method="get" class="mb-3">
                        @csrf

                        <div class="row">

                            <div class="col-6">

                                <label>Date From</label>
                                <input class="form-control" name="from" type="date" value="{{ request('from') }}">

                            </div>

                            <div class="col-6">

                                <label>Date To</label>
                                <input class="form-control" name="to" type="date" value="{{ request('to') }}">

                            </div>

                        </div>

                        <button type="submit" class="btn btn-lg btn-success my-3">Search</button>
                    </form>

                    <form action="{{ url('transactions') }}" method="get" class="d-flex align-items-center" style="gap: 0.5rem;">
                        <label class="me-2 mb-0">Filter by type:</label>
                        <select name="kind" class="form-select form-select-sm" style="max-width: 220px;">
                            @php $selectedKind = request('kind') ?: ($kind ?? null); @endphp
                            <option value="">All types</option>
                            <option value="funding" {{ $selectedKind === 'funding' ? 'selected' : '' }}>Funding</option>
                            <option value="verification" {{ $selectedKind === 'verification' ? 'selected' : '' }}>Verification (user orders)</option>
                            <option value="order_cancel" {{ $selectedKind === 'order_cancel' ? 'selected' : '' }}>Order cancel refunds</option>
                            <option value="api_order" {{ $selectedKind === 'api_order' ? 'selected' : '' }}>API orders</option>
                            <option value="api_order_cancel" {{ $selectedKind === 'api_order_cancel' ? 'selected' : '' }}>API order cancels</option>
                            <option value="vtu" {{ $selectedKind === 'vtu' ? 'selected' : '' }}>VTU (airtime/data/etc)</option>
                        </select>
                        <button type="submit" class="btn btn-outline-primary btn-sm ms-2">Apply</button>
                    </form>


                </div>


            </div>

        </section>


        <section class="transactions-section">

            <div class="table-container">
                <h6>Recent Transactions</h6>
                <table class="transactions-table">
                    <thead>
                    <tr>
                        <th class="border-0">Ref</th>
                        <th class="border-0">User</th>
                        <th class="border-0">Wallet(NGN)</th>
                        <th class="border-0">OLD Balance</th>
                        <th class="border-0">NEW Balance</th>
                        <th class="border-0">Type</th>
                        <th class="border-0">Status</th>
                        <th class="border-0">Date/Time</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($transaction as $data)

                        <tr>
                            <td>{{ $data->ref_id }} </td>
                            @php
                                $userModel = User::where('id', $data->user_id)->first();
                                $userEmail = $userModel->email ?? null;
                            @endphp
                            <td>
                                @if($userModel)
                                    <a href="{{ url('view-user?id='.$userModel->id) }}">
                                        {{ $userEmail }}
                                    </a>
                                @else
                                    name
                                @endif
                            </td>
                            <td>{{ number_format($data->amount, 2) }} </td>
                            <td>{{ number_format($data->old_balance, 2) }} </td>
                            <td>{{ number_format($data->balance, 2) }} </td>
                            @if($data->type == 2)
                                <td>
                                    <div class="stat-status active-green">Credit</div>
                                </td>
                            @else
                                <td>                                    <div class="stat-status attention">Debit</div>

                                </td>
                            @endif

                            @if($data->status == 1)
                                <td>
                                    <div class="stat-status processed">
                                        <i class="fas fa-check"></i>
                                        Initiated
                                    </div>
                                </td>

                            @elseif($data->status == 0)
                                <td>
                                    <span class="badge badge-pill badge-warning">Pending</span>
                                </td>

                            @elseif($data->status == 3)
                                <td>
                                    <div class="stat-status attention">Canceled</div>

                                </td>

                            @else
                                <td>
                                    <div class="stat-status active-green">Completed</div>


                                </td>
                            @endif
                            <td>{{ $data->created_at }} </td>


                        </tr>

                    @empty

                        No transaction found

                    @endforelse
                    </tbody>

                    {{$transaction->links()}}
                </table>
            </div>
        </section>



    </main>

@endsection
