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
                <div class="admin-role">Price setting</div>
            </div>

        </header>

        <!-- Page Title -->
        <section class="welcome-section">
            <div class="breadcrumb">
                <i class="fas fa-exchange-alt"></i>
                <span>Price Setting</span>
            </div>
            <div class="welcome-message">
            </div>
        </section>


        <section class="my-5">

            <div class="row">


                <div class="col-4">

                    <div class="card">

                        <div class="card-body">

                            <form action="set_rate_1" method="post">
                                @csrf
                                <label>Diasy Rate Setting</label>
                                <input name="rate" class="form-control my-3" value={{$set1->rate}} required>
                                <button class="form-control btn btn-sm btn-primary" type="submit"> Set Rate </button>

                            </form>


                            <form action="set_margin_1" method="post" class="my-3">
                                @csrf
                                <label>Diasy Margin Setting</label>
                                <input name="margin" class="form-control my-3" value={{$set1->margin}} required>
                                <button class="form-control btn btn-sm btn-primary" type="submit"> Set Margin </button>

                            </form>


                        </div>
                    </div>



                </div>

                <div class="col-4">


                    <div class="card">

                        <div class="card-body">

                            <form action="set_rate_2" method="post">
                                @csrf
                                <label>SMS POOL Rate Setting</label>
                                <input name="rate" class="form-control my-3" value={{$set2->rate}} required>
                                <button class="form-control btn btn-sm btn-primary" type="submit"> Set Rate </button>

                            </form>


                            <form action="set_margin_2" method="post" class="my-3">
                                @csrf
                                <label>SMS POOL Margin Setting</label>
                                <input name="margin" class="form-control my-3" value={{$set2->margin}} required>
                                <button class="form-control btn btn-sm btn-primary" type="submit"> Set Margin </button>

                            </form>
                        </div>
                    </div>



                </div>

                <div class="col-4">


                    <div class="card">

                        <div class="card-body">

                            <form action="set_rate_2" method="post">
                                @csrf
                                <label>Unlimited Rate Setting</label>
                                <input name="rate" class="form-control my-3" value={{$set3->rate}} required>
                                <button class="form-control btn btn-sm btn-primary" type="submit"> Set Rate </button>

                            </form>


                            <form action="set_margin_2" method="post" class="my-3">
                                @csrf
                                <label>Unlimited Margin Setting</label>
                                <input name="margin" class="form-control my-3" value={{$set3->margin}} required>
                                <button class="form-control btn btn-sm btn-primary" type="submit"> Set Margin </button>

                            </form>
                        </div>
                    </div>



                </div>


            </div>
        </section>

        <!-- Transactions Table -->
    </main>

@endsection
