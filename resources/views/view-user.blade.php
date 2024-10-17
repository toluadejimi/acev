<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ url('') }}/public/concept/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link href="{{ url('') }}/public/concept/assets/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('') }}/public/concept/assets/libs/css/style.css">
    <link rel="stylesheet" href="{{ url('') }}/public/concept/assets/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <link rel="stylesheet" href="{{ url('') }}/public/concept/assets/vendor/charts/chartist-bundle/chartist.css">
    <link rel="stylesheet" href="{{ url('') }}/public/concept/assets/vendor/charts/morris-bundle/morris.css">
    <link rel="stylesheet"
          href="{{ url('') }}/public/concept/assets/vendor/fonts/material-design-iconic-font/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="{{ url('') }}/public/concept/assets/vendor/charts/c3charts/c3.css">
    <link rel="stylesheet" href="{{ url('') }}/public/concept/assets/vendor/fonts/flag-icon-css/flag-icon.min.css">
    <title>ACEVERIFY</title>
</head>

<body>
<!-- ============================================================== -->
<!-- main wrapper -->
<!-- ============================================================== -->
<div class="dashboard-main-wrapper">
    <!-- ============================================================== -->
    <!-- navbar -->
    <!-- ============================================================== -->
    <div class="dashboard-header">
        <nav class="navbar navbar-expand-lg bg-white fixed-top">
            <a class="navbar-brand" href="/">ACEVERIFY</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse"
                    data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse " id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto navbar-right-top">


                    <li class="nav-item dropdown nav-user">
                        <a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink2"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img
                                src="{{ url('') }}/public/concept/assets/images/avatar-1.jpg" alt=""
                                class="user-avatar-md rounded-circle"></a>
                        <div class="dropdown-menu dropdown-menu-right nav-user-dropdown"
                             aria-labelledby="navbarDropdownMenuLink2">
                            <div class="nav-user-info">
                                <h5 class="mb-0 text-white nav-user-name">Admin</h5>
                                <span class="status"></span><span class="ml-2">Available</span>
                            </div>
                            <a class="dropdown-item" href="#"><i class="fas fa-user mr-2"></i>Account</a>
                            <a class="dropdown-item" href="#"><i class="fas fa-cog mr-2"></i>Setting</a>
                            <a class="dropdown-item" href="#"><i class="fas fa-power-off mr-2"></i>Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
    <!-- ============================================================== -->
    <!-- end navbar -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- left sidebar -->
    <!-- ============================================================== -->
    <div class="nav-left-sidebar sidebar-dark">
        <div class="menu-list">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a class="d-xl-none d-lg-none" href="#">Dashboard</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav flex-column">
                        <li class="nav-divider">
                            Menu
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link" href="admin-dashboard"><i
                                    class="fa fa-fw fa-user-circle"></i>Dashboard <span
                                    class="badge badge-success">6</span></a>
                            <div id="submenu-1" class="collapse submenu" style="">
                            </div>
                        </li>


                        <li class="nav-item active">
                            <a class="nav-link active" href="/users" aria-controls="submenu-2"><i
                                    class="fa fa-fw fa-rocket"></i>Users</a>

                        </li>


                    </ul>
                </div>
            </nav>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- end left sidebar -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- wrapper  -->
    <!-- ============================================================== -->
    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content ">
                <!-- ============================================================== -->
                <!-- pageheader  -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="page-header">
                            <h2 class="pageheader-title">Admin Dashboard</h2>
                            <p class="pageheader-text"></p>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#"
                                                                       class="breadcrumb-link">Dashboard</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Users</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- end pageheader  -->
                <!-- ============================================================== -->
                <div class="ecommerce-widget">

                    <div class="row">
                        {{-- <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="text-muted">All User</h5>
                                    <div class="metric-value d-inline-block">
                                        <h3 class="mb-1">{{ $user }}</h3>
                                    </div>
                                    <div
                                        class="metric-label d-inline-block float-right text-success font-weight-bold">
                                    </div>
                                </div>
                            </div>
                        </div> --}}


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

                    <div class="row">
                        <!-- ============================================================== -->

                        <!-- ============================================================== -->

                        <!-- recent orders  -->
                        <!-- ============================================================== -->
                        {{-- <div class="col-xl-12 col-lg-12 col-md-6 col-sm-12 col-12">
                            <div class="card">
                                <h5 class="card-header">Orders</h5>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead class="bg-light">
                                                <tr class="border-0">
                                                    <th class="border-0">Username</th>
                                                    <th class="border-0">Email</th>
                                                    <th class="border-0">Wallet(NGN)</th>
                                                    <th class="border-0">Action</th>
                                                    <th class="border-0">Action</th>


                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($users as $data)

                                                <tr>
                                                    <td><a href="view-user?id={{ $data->id }}">{{ $data->username
                                                            }}</a> </td>
                                                    <td>{{ $data->email }} </td>
                                                    <td>{{ number_format($data->wallet, 2) }} </td>
                                                    <td><a href="view-user?id={{ $data->id }}"
                                                            class="btn btn-success btn-sm">View User</a> </td>
                                                    <td><a href="remove-user?id={{ $data->id }}"
                                                            class="btn btn-danger btn-sm">Delete User</a> </td>


                                                </tr>

                                                @empty

                                                <tr>
                                                    <td> No Record Found</td>
                                                </tr>

                                                @endforelse
                                            </tbody>



                                        </table>
                                        {{ $users->links() }}

                                    </div>
                                </div>
                            </div>
                        </div> --}}


                        <div class="col-xl-12 col-lg-12 col-md-6 col-sm-12 col-12">
                            <div class="card">


                                <h3 class="card-title p-3">User Information</h3>

                                <div class="row">

                                    <div class="col-xl-3 col-lg-4 col-md-4 col-sm-4 col-5">

                                        <div class="card-body p-3">
                                            <h5 class="">Username</h5>


                                            {{$user->username ?? "username"}}


                                        </div>
                                    </div>


                                    <div class="col-xl-3 col-lg-4 col-md-4 col-sm-4 col-7">

                                        <div class="card-body p-3">
                                            <h5 class="">Email</h5>


                                            {{$user->email ?? "email"}}


                                        </div>
                                    </div>


                                    <div class="col-xl-3 col-lg-4 col-md-4 col-sm-4 col-7">

                                        <div class="card-body p-3">
                                            <h5 class="">Wallet</h5>


                                            NGN {{number_format($user->wallet ?? 0, 2) }}


                                        </div>
                                    </div>


                                    <div class="col-xl-3 col-lg-4 col-md-4 col-sm-4 col-5">

                                        <div class="card-body p-3">
                                            <h5 class="">Status</h5>

                                            @if($user->status == 9)
                                               <span class="text-warning">{{"Banned"}} </span>
                                            @else
                                                {{"Active"}}
                                            @endif



                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>

                        <div class="col-xl-12 col-lg-12 col-md-6 col-sm-12 col-12">
                            <div class="card">

                                <h3 class="p-3">Fund User</h3>


                                <form action="/update-user" method="POST">
                                    @csrf

                                    <div class="row">

                                        <div class="col-xl-3 col-lg-4 col-md-4 col-sm-4 col-12">

                                            <div class="card-body p-3">
                                                <label>Enter Amount (NGN)</label>
                                                <input type="number" class="form-control2 text-dark" name="amount"
                                                       required>

                                                <input type="text" hidden value="{{ $user->id ?? "id" }}"
                                                       class="form-control2" name="id" required>


                                            </div>
                                        </div>

                                        <div class="col-xl-3 col-lg-4 col-md-4 col-sm-4 col-12">

                                            <div class="card-body p-3">
                                                <label>Choose Option</label>
                                                <select class="form-control2 text-dark" required name="trade">
                                                    <option value="credit">Credit</option>
                                                    <option value="debit">Debit</option>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-xl-12 col-lg-4 col-md-4 col-sm-4 col-12">

                                            <div class="card-body">

                                                <button type="submit"
                                                        class="btn btn-primary btn-sm">Continue
                                                </button>
                                            </div>

                                        </div>


                                </form>


                            </div>
                        </div>



                        <div class="col-xl-12 col-lg-12 col-md-6 col-sm-12 col-12">
                            <div class="card">

                                <div class="card-body">

                                    <div class="row">

                                        <div class="col-4">
                                            <h4>Total Funded</h4>
                                            NGN {{number_format($total_funded, 2)}}

                                        </div>

                                        <div class="col-4">
                                            <h4>Total Bought</h4>
                                            {{number_format($total_bought, 2)}}
                                        </div>

                                        <div class="col-4">
                                            <h4>Balance</h4>
                                            {{number_format($total_balance, 2)}}
                                        </div>
                                    </div>


                                </div>
                            </div>

                        </div>


                        <div class="col-xl-12 col-lg-12 col-md-6 col-sm-12 col-12">
                            <div class="card">
                                <h5 class="card-header">All Transaction</h5>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead class="bg-dark">
                                            <tr class="border-0">
                                                <th class="border-0">Ref</th>
                                                <th class="border-0">Wallet(NGN)</th>
                                                <th class="border-0">Type</th>
                                                <th class="border-0">Status</th>
                                                <th class="border-0">Date/Time</th>


                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse ($transaction as $data)

                                                <tr>
                                                    <td>{{ $data->ref_id }} </td>
                                                    <td>{{ number_format($data->amount, 2) }} </td>
                                                    @if($data->type == 2)
                                                        <td><span class="badge badge-success">Credit</span>
                                                        </td>
                                                    @else
                                                        <td><span class="badge badge-danger">Debit</span>
                                                        </td>
                                                    @endif

                                                    @if($data->status == 1)
                                                        <td>
                                                            <span
                                                                class="badge badge-pill badge-warning">Intitated</span>
                                                        </td>

                                                    @elseif($data->status == 0)
                                                        <td>
                                                            <span class="badge badge-pill badge-warning">Pending</span>
                                                        </td>

                                                    @elseif($data->status == 3)
                                                        <td>
                                                            <span class="badge badge-pill badge-danger">Cancled</span>
                                                        </td>

                                                    @else
                                                        <td>
                                                            <span
                                                                class="badge badge-pill badge-success">Completed</span>

                                                        </td>
                                                    @endif
                                                    <td>{{ $data->created_at }} </td>


                                                </tr>

                                            @empty

                                                <tr>
                                                    <td> No Record Found</td>
                                                </tr>

                                            @endforelse
                                            </tbody>


                                            {{ $transaction->links() }}

                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-12 col-lg-12 col-md-6 col-sm-12 col-12">
                            <div class="card">
                                <h5 class="card-header">All Orders</h5>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead class="bg-dark">
                                            <tr class="border-0">
                                                <th class="border-0">Order ID</th>
                                                <th class="border-0">Service</th>
                                                <th class="border-0">Phone</th>
                                                <th class="border-0">SMS</th>
                                                <th class="border-0">Amount</th>
                                                <th class="border-0">Status</th>
                                                <th class="border-0">Date</th>
                                                <th class="border-0">Time</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @forelse ($verification as $data)

                                                <tr>

                                                    <td>{{ $data->phone }} </td>
                                                    <td>{{ $data->service }} </td>
                                                    <td>{{ $data->order_id }} </td>
                                                    <td>{{ $data->full_sms }} </td>
                                                    <td>{{ $data->cost }} </td>
                                                    @if($data->status == 2)
                                                        <td>
                                                            <span
                                                                class="badge badge-pill badge-success">Successful</span>
                                                        </td>
                                                    @else
                                                        <td>
                                                            <span class="badge badge-pill badge-warning">Pending</span>

                                                        </td>
                                                    @endif


                                                    <td>{{ date('d/m/y', strtotime($data->created_at)) }} </td>
                                                    <td>{{ date('h:i', strtotime($data->created_at)) }} </td>


                                                </tr>

                                            @empty

                                                <tr>
                                                    <td> No Record Found</td>
                                                </tr>

                                            @endforelse


                                            </tbody>

                                            {{ $verification->links() }}


                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- footer -->
    <!-- ============================================================== -->
    <div class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    Copyright © 2018 ACEVERIFY. All rights reserved. Dashboard by <a
                        href="https://colorlib.com/wp/">Colorlib</a>.
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    <div class="text-md-right footer-links d-none d-sm-block">
                        <a href="javascript: void(0);">About</a>
                        <a href="javascript: void(0);">Support</a>
                        <a href="javascript: void(0);">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- end footer -->
    <!-- ============================================================== -->
</div>
<!-- ============================================================== -->
<!-- end wrapper  -->
<!-- ============================================================== -->
</div>
<!-- ============================================================== -->
<!-- end main wrapper  -->
<!-- ============================================================== -->
<!-- Optional JavaScript -->
<!-- jquery 3.3.1 -->
<script src="{{ url('') }}/public/concept/assets/vendor/jquery/jquery-3.3.1.min.js"></script>
<!-- bootstap bundle js -->
<script src="{{ url('') }}/public/concept/assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
<!-- slimscroll js -->
<script src="{{ url('') }}/public/concept/assets/vendor/slimscroll/jquery.slimscroll.js"></script>
<!-- main js -->
<script src="{{ url('') }}/public/concept/assets/libs/js/main-js.js"></script>
<!-- chart chartist js -->
<script src="{{ url('') }}/public/concept/assets/vendor/charts/chartist-bundle/chartist.min.js"></script>
<!-- sparkline js -->
<script src="{{ url('') }}/public/concept/assets/vendor/charts/sparkline/jquery.sparkline.js"></script>
<!-- morris js -->
<script src="{{ url('') }}/public/concept/assets/vendor/charts/morris-bundle/raphael.min.js"></script>
<script src="{{ url('') }}/public/concept/assets/vendor/charts/morris-bundle/morris.js"></script>
<!-- chart c3 js -->
<script src="{{ url('') }}/public/concept/assets/vendor/charts/c3charts/c3.min.js"></script>
<script src="{{ url('') }}/public/concept/assets/vendor/charts/c3charts/d3-5.4.0.min.js"></script>
<script src="{{ url('') }}/public/concept/assets/vendor/charts/c3charts/C3chartjs.js"></script>
<script src="{{ url('') }}/public/concept/assets/libs/js/dashboard-ecommerce.js"></script>
</body>

</html>
