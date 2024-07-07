@extends('layout.main')
@section('content')

    <section id="technologies mt-4 my-5">

        <div class="row p-3">


            <div class="d-flex justify-content-center mt-5">
                <h4>Create an account</h4>
            </div>

            <p class="d-flex justify-content-center">Join may others that are enjoy fast services</p>


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


            <div class="d-flex justify-content-center mt-4">

                    <form action="login_now" method="post">
                        @csrf


                    </form>

                    <form action="register_now" method="post">
                        @csrf


                        <div class="mb-3">
                            <input type="text" placeholder="Enter Your Username" name="username" class="form-control" autofocus
                                   id="floatingInput">
                        </div>

                        <div class="mb-3">
                            <input name="email" type="email" autofocus class="form-control" id="floatingInput"
                                   placeholder="Enter you Email ">
                        </div>


                        <div class="input-group my-4">
                            <input name="password" type="password" class="form-control" id="floatingInput1" placeholder="Enter your password">
                            <button type="button" style="border-radius: 0px 10px 10px 0px; border: grey" id="togglePassword">
                                <i class="far fa-eye" id="eyeIcon"></i>
                            </button>

                            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
                            <script>
                                $(document).ready(function() {
                                    $('#togglePassword').click(function(){
                                        // Toggle password visibility
                                        var passwordField = $('#floatingInput1');
                                        var eyeIcon = $('#eyeIcon');

                                        if (passwordField.attr('type') === 'password') {
                                            passwordField.attr('type', 'text');
                                            eyeIcon.removeClass('far fa-eye').addClass('fas fa-eye-slash');
                                        } else {
                                            passwordField.attr('type', 'password');
                                            eyeIcon.removeClass('fas fa-eye-slash').addClass('far fa-eye');
                                        }
                                    });
                                });
                            </script>
                        </div>


                        <div class="input-group my-4">
                            <input name="password_confirmation" type="password" class="form-control" id="floatingInput12" placeholder="Enter your password">
                            <button type="button" style="border-radius: 0px 10px 10px 0px; border: grey" id="togglePassword12">
                                <i class="far fa-eye" id="eyeIcon"></i>
                            </button>

                            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
                            <script>
                                $(document).ready(function() {
                                    $('#togglePassword12').click(function(){
                                        // Toggle password visibility
                                        var passwordField = $('#floatingInput12');
                                        var eyeIcon = $('#eyeIcon');

                                        if (passwordField.attr('type') === 'password') {
                                            passwordField.attr('type', 'text');
                                            eyeIcon.removeClass('far fa-eye').addClass('fas fa-eye-slash');
                                        } else {
                                            passwordField.attr('type', 'password');
                                            eyeIcon.removeClass('fas fa-eye-slash').addClass('far fa-eye');
                                        }
                                    });
                                });
                            </script>
                        </div>




                        <div class="cf-turnstile"
                             data-sitekey="{{ config('services.cloudflare.turnstile.site_key') }}"
                             data-callback="onTurnstileSuccess"
                        >

                        </div>

                        <script>
                            window.onTurnstileSuccess = function (code) {
                                document.querySelector('form button[type="submit"]').disabled = false;
                            }
                        </script>



                        <div class="d-grid mt-4">
                            <button type="submit" style="background: rgba(23, 69, 132, 1); border: 0px;  border-radius: 2px;" class="btn btn-primary" disabled>Register</button>
                        </div>




                        <div class="d-flex justify-content-center mt-5 align-items-end text-dark mt-4">
                            <h6 class="f-w-500 text-dark mb-0">Already have an account? </h6>
                            <a href="login" class="text-primary">Login</a></div>




                    </form>

                </div>
            </div>


@endsection












