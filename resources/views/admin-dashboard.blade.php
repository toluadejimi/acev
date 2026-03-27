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

                <a href="{{ url('notify') }}" class="header-btn">
                    <i class="fas fa-bullhorn"></i>
                    User announcement
                </a>
            </div>
        </header>

        <!-- Welcome Section -->
        <section class="welcome-section">
            <div class="breadcrumb">
                <i class="fas fa-user-shield"></i>
                <span>Administrator</span>
            </div>
            <div class="welcome-message">
                <h1>Hi, <span class="username">{{Auth::user()->username}}</span> 👋</h1>
                <p>Welcome to the admin control panel</p>
            </div>
        </section>

        <!-- Stats Grid -->
        <section class="stats-grid">
            <!-- Top Row Stats -->
            <div class="stat-card">
                <div class="stat-icon users">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Users</div>
                    <div class="stat-value">{{number_format($user)}}</div>
                    <div class="stat-status active">
                        <i class="fas fa-arrow-up"></i>
                        Active users
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon balance">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Balance</div>
                    <div class="stat-value">₦{{number_format($user_total, 2)}}</div>
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
                    <div class="stat-value">{{number_format($total_verified_message)}}</div>
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
                <div class="stat-content">
                    <div class="stat-label">Pending Deposits</div>
                    <div class="stat-value">{{number_format($manual_payment)}}</div>
                    <div class="stat-status requires-action">
                        <i class="fas fa-exclamation-triangle"></i>
                        Requires action
                    </div>
                </div>
            </div>

            <!-- Bottom Row Stats -->
            <div class="stat-card">
                <div class="stat-icon failed">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Failed Transactions</div>
                    <div class="stat-value">0</div>
                    <div class="stat-description">Needs review</div>
                    <div class="stat-status attention">Attention</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon todays-orders">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Today's Orders</div>
                    <div class="stat-value">{{number_format($today_order)}}</div>
                    <div class="stat-description">Today</div>
                    <div class="stat-status active-green">Active</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon new-users">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Today's New Users</div>
                    <div class="stat-value">{{number_format($new_user_today)}}</div>
                    <div class="stat-description">New registrations</div>
                    <div class="stat-status growth">Growth</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon revenue">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Today Revenue</div>
                    <div class="stat-value">₦{{number_format($total_in_d, 2)}}</div>
                    <div class="stat-description">Last 30 days</div>
                    <div class="stat-status monthly">Monthly</div>
                </div>
            </div>
        </section>


        <section class="transactions-section">

            <div class="table-container">
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
                            <td>
                                <a style="text-decoration: none" href="view-user?id={{ $trx->user->id }}">
                                    {{ $trx->user->username }}
                                </a>
                            </td>
                            <td>{{$trx->phone}}</td>
                            <td>{{$trx->country}}</td>
                            <td>{{$trx->service}}</td>
                            <td>@if($trx->type == 1)
                                    <span style="color: #014473" class="badge badge-success">USA · SV1</span>
                                @elseif($trx->type == 8)
                                    <span style="color: #014473" class="badge badge-success">SMS POOL</span>
                                @elseif($trx->type == 9)
                                    <span style="color: #014473" class="badge badge-success">HERO SMS</span>
                                @elseif($trx->type == 10)
                                    <span style="color: #014473" class="badge badge-success">WORLD SV3</span>
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
