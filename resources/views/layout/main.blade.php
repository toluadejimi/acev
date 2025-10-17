<!doctype html>
<html lang="en"><!-- [Head] start -->
<head><title>ACESMSVERIFY</title><!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description"
          content="ACESMSVERIFY NUMBER VERIFICATION">
    <meta name="keywords"
          content="ACESMSVERIFY VERIFICATION">
    <meta name="author" content="Phoenixcoded"><!-- [Favicon] icon -->
    <link rel="icon" href="{{url('')}}/public/assets/fav.svg" type="image/x-icon"><!-- [Font] Family -->
    <link rel="stylesheet" href="{{url('')}}/public/assets/fonts/inter/inter.css" id="main-font-link">
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="{{url('')}}/public/assets/fonts/tabler-icons.min.css">
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="{{url('')}}/public/assets/fonts/feather.css">
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="{{url('')}}/public/assets/fonts/fontawesome.css">
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="{{url('')}}/public/assets/fonts/material.css"><!-- [Template CSS Files] -->
    <link rel="stylesheet" href="{{url('')}}/public/assets/css/style.css" id="main-style-link">
    <link rel="stylesheet" href="{{url('')}}/public/assets/css/style-preset.css">

    <link rel="shortcut icon" href="{{ url('') }}/public/assets/fav.ico">

    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>




    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"
          integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA=="
          crossorigin="anonymous"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>



    <style>

        .popup-banner {
            position: fixed;
            top: -120px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(90deg, #2563eb, #1e40af);
            color: white;
            padding: 15px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
            z-index: 9999;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: top 0.6s ease;
            width: 90%;          /* ✅ make it fill most of the screen */
            max-width: 800px;    /* ✅ optional limit on large screens */
            box-sizing: border-box;
        }


        .popup-banner.show { top: 20px; }

        .popup-close {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            font-weight: bold;
            margin-left: 15px;
            cursor: pointer;
        }
        .popup-close:hover { opacity: 0.7; }

        /* ===== Center Popup ===== */
        .popup-center {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.55);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.4s ease, visibility 0.4s ease;
            z-index: 10000;
            box-sizing: border-box; /* ensures padding doesn’t affect layout */
        }


        .popup-center.show {
            opacity: 1;
            visibility: visible;
        }

        .popup-box {
            background: white;
            color: #333;
            border-radius: 14px;
            padding: 25px 30px;
            max-width: 420px;
            text-align: center;
            box-shadow: 0 5px 25px rgba(0,0,0,0.3);
            animation: fadeInUp 0.6s ease;
        }

        .popup-box span {
            display: block;
            margin-bottom: 20px;
            font-size: 16px;
            line-height: 1.5;
        }

        .telegram-btn {
            background: #014473;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }

        .popup-btn {
            background: #bf0616;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }
        .popup-btn:hover { background: #1e40af; }

        @keyframes fadeInUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>


</head><!-- [Head] end --><!-- [Body] Start -->
<body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-layout="vertical" data-pc-direction="ltr"
      data-pc-theme_contrast="" data-pc-theme="light"><!-- [ Pre-loader ] start -->
<div class="page-loader">
    <div class="bar"></div>
</div><!-- [ Pre-loader ] End --><!-- [ Sidebar Menu ] start -->

<header
    id="home"
    style="background-image: url({{url('')}}/public/assets/images/landing/img-headerbg.jpg)"
>
    <!-- [ Nav ] start --><!-- [ Nav ] start -->
    <nav class="navbar navbar-expand-md navbar-light default">
        <div class="container">
            <a class="navbar-brand" href="/"
            ><img src="{{url('')}}/public/assets/images/logo.svg" alt="logo"/> </a >

            <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-left">

                    <li class="nav-item px-1">
                        <a class="nav-link"
                           href="home">Dashboard
                        </a>
                    </li>


                    <li class="nav-item px-1">
                        <a class="nav-link"
                           href="fund-wallet" >Fund Wallet</a >
                    </li>



                    <li class="nav-item px-1">
                        <a class="nav-link"
                           href="https://aceboosts.com/" > Boost social account</a >
                    </li>

                    <li class="nav-item px-1">
                        <a class="nav-link"
                           href="https://acelogstores.com/" > Purchase Logs</a >
                    </li>


                    <li class="nav-item px-1">
                        <a class="nav-link"
                           href="api-docs" > Api Docs</a >
                    </li>

                    @auth

                    <li class="nav-item">
                        <a
                            style="background: rgb(142,4,4); color: white"
                            class="btn btn btn-buy  d-none d-lg-block d-md-none"
                            target="_blank"
                            href="log-out"><i class="ti ti-wallet">
                            </i> Log Out </a>
                    </li>

                    @else

                        <li class="nav-item">
                            <a
                                style="background: rgb(129,3,62); color: white"
                                class="btn btn btn-buy  d-none d-lg-block d-md-none"
                                target="_blank"
                                href="us"><i class="ti ti-user">
                                </i> Get Started </a>
                        </li>


                    @endauth

                </ul>




            </div>


            <div class="d-sm-none d-lg-block d-md-none">

            <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-end">

                    @auth
                        <li class="nav-item">
                            <a
                                style="background: rgb(142,4,4); color: white"
                                class="btn btn btn-buy  d-none d-lg-block d-md-none"
                                target="_blank"
                                href="log-out"><i class="ti ti-lock">
                                </i> Log Out </a>
                        </li>
                    @endauth
                </ul>
            </div>
            </div>


            @auth
            <div class="d-lg-none d-sm-block">
                    <a
                        style="background: rgb(142,4,4); color: white"
                        class="btn btn btn-buy  d-none d-lg-block d-md-none"
                        target="_blank"
                        href="log-out"><i class="ti ti-lock">
                        </i> Log Out </a>

            </div>
            @endauth



            <button
                style="background: rgba(23, 69, 132, 1); color: white"
                class="navbar-toggler rounded"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarTogglerDemo01"
                aria-controls="navbarTogglerDemo01"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>


        </div>
    </nav>
    <!-- [ Nav ] start --><!-- [ Nav ] start -->

</header>

@if(session('topMessage'))
    <div id="top-popup" class="popup-banner">
        <div class="popup-content">
            <span>{{ session('topMessage') }}</span>
        </div>
    </div>
@endif



@yield('content')


<footer class="footer mt-5" style="padding-top: 200px">
    <p class="d-flex justify-content-center"><a href="https://t.me/acesmsverify">Telegram | ACESMSVERIFY </a>  </p>
</footer>


<!-- Required Js -->
<script data-cfasync="false" src="../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
<script src="{{url('')}}/public/assets/js/plugins/popper.min.js"></script>
<script src="{{url('')}}/public/assets/js/plugins/simplebar.min.js"></script>
<script src="{{url('')}}/public/assets/js/plugins/bootstrap.min.js"></script>
<script src="{{url('')}}/public/assets/js/fonts/custom-font.js"></script>
<script src="{{url('')}}/public/assets/js/pcoded.js"></script>
<script src="{{url('')}}/public/assets/js/plugins/feather.min.js"></script>
<script>layout_change('false');</script>
<script>layout_theme_contrast_change('false');</script>
<script>change_box_container('false');</script>
<script>layout_caption_change('true');</script>
<script>layout_rtl_change('false');</script>
<script>preset_change('preset-4');</script>
<script>main_layout_change('vertical');</script>

</style>

<style>
    .float{
        position:fixed;
        width:60px;
        height:60px;
        bottom:40px;
        right:40px;
        background-color: #060d46;
        color:#FFF;
        border-radius:50px;
        text-align:center;
        font-size:30px;
        box-shadow: 2px 2px 3px #999;
        z-index:100;
    }

    .my-float{
        margin-top:16px;
    }
</style>


<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<a style="color: white" href="https://t.me/acesmsverify" class="float" target="_blank">
    <i class="fa fa-comment my-float"></i>
</a>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const topPopup = document.getElementById("top-popup");
        const centerPopup = document.getElementById("center-popup");

        // Show top banner
        if (topPopup) {
            setTimeout(() => topPopup.classList.add("show"), 400);
            setTimeout(() => topPopup.classList.remove("show"), 8000);
        }

        // Show center popup a few seconds later
        if (centerPopup) {
            setTimeout(() => centerPopup.classList.add("show"), 3500);
        }
    });

    function closeTopPopup() {
        document.getElementById("top-popup")?.classList.remove("show");
    }

    function closeCenterPopup() {
        document.getElementById("center-popup")?.classList.remove("show");
    }
</script>


</body>
<!-- [Body] end -->
</html>
