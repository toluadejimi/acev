@extends('layout.main')
@section('content')

    <section id="technologies mt-4 my-5">

        <div class="row p-3">


            <div class="d-flex justify-content-center mt-5 my-5">

                <h4>Hi, Welcome Back! 👋</h4>
            </div>

            <div class="d-flex justify-content-center mt-5 my-5">


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


            </div>







            <div class="d-flex justify-content-center mt-4">

                <form action="login_now" method="post">
                    @csrf


                    <label class="my-2">Email</label>
                    <div class="mb-3">
                        <input name="email" type="email" autofocus class="form-control" id="floatingInput"
                               placeholder="example@gmail.com">
                    </div>

                    <label class="my-2">Password</label>

                    <div class="input-group">
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


                    <div class="mb-3 d-flex align-items-center justify-content-between my-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe">
                            <label class="form-check-label" for="rememberMe">Remember me</label>
                        </div>
                        <a href="forgot-password">Forgot password?</a>
                    </div>


                    <div class="cf-turnstile p-3"
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
                        <button type="submit" style="background: rgba(23, 69, 132, 1); border: 0px; border-radius: 2px" disabled
                                class="btn btn-primary">Login
                        </button>
                    </div>


                    <div class="d-flex justify-content-center align-items-end text-dark mt-4">
                        <h6 class="f-w-500 text-dark mb-0">Don't have an Account? </h6>
                        <a href="register" class="text-primary">Create Account</a></div>


                </form>


            </div>


        </div>




    </section>

@endsection






