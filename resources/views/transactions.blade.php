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
                <div class="stat-icon balance">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Spent</div>
                    <div class="stat-value">₦{{number_format($debit, 2)}}</div>
                    <div class="stat-status processed">
                        <i class="fas fa-check"></i>
                        Processed
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orders">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value">0}</div>
                    <div class="stat-status complete">
                        <i class="fas fa-check-circle"></i>
                        Complete
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon deposits">
                    <i class="fas fa-credit-card"></i>
                </div>

            </div>


        </section>


        <section class="stats-grid">


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
                            @php $user = User::where('id', $data->user_id)->first()->email ?? null; @endphp
                            <td>{{$user ?? "name"}}</td>
                            <td>{{ number_format($data->amount, 2) }} </td>
                            <td>{{ number_format($data->old_balance, 2) }} </td>
                            <td>{{ number_format($data->balance, 2) }} </td>
                            @if($data->type == 2)
                                <td>                    <div class="stat-status active-green">Credit</div>

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
