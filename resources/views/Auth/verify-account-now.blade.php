@extends('layout.main')
@section('content')

    <section id="technologies mt-4 my-5">

        <div class="row p-3">


            <div class="d-flex justify-content-center mt-5 my-5">
                <h4>Hi, Welcome Back! 👋</h4>
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



                <form>

                        <h1 class="text-center">Congratulations!!</h1>
                        <p class="text-center">Your account has been verified, Login to continue</p>

                        <div class="d-grid mt-4">
                            <a href="/login" type="submit"
                               style="background: rgba(23, 69, 132, 1); border: 0px; border-radius: 2px"
                               class="btn btn-primary w-70">Login
                            </a>
                        </div>


                </form>






            </div>


        </div>
    </section>

@endsection






