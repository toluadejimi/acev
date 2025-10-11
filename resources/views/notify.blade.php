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
                <div class="admin-role">Notification</div>
            </div>

        </header>

        <!-- Page Title -->
        <section class="welcome-section">
            <div class="breadcrumb">
                <i class="fas fa-exchange-alt"></i>
                <span>Users</span>
            </div>
            <div class="welcome-message">
                <h1>Notification</h1>
            </div>
        </section>


        <section class="my-5">

            <div class="row">



                <div class="col-10">

                    <div class="card">
                        <div class="card-body">
                            <form action="{{ url('save-notification') }}" method="POST">
                                @csrf

                                <label for="message">Message</label>
                                <textarea
                                    name="message"
                                    id="message"
                                    class="form-control my-3"
                                    rows="4"
                                    required>{{ old('message', $notify ?? '') }}</textarea>

                                <button type="submit" class="btn btn-primary">Save Message</button>
                            </form>
                        </div>
                    </div>




                </div>

            </div>
        </section>

        <!-- Transactions Table -->
    </main>

@endsection
