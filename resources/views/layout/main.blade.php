<!doctype html>
<html lang="en"><!-- [Head] start -->
<head><title>SMS LORD</title><!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description"
          content="SMSLORD NUMBER VERIFICATION">
    <meta name="keywords"
          content="SMS LORD VERIFICATION">
    <meta name="author" content="Phoenixcoded"><!-- [Favicon] icon -->
    <link rel="icon" href="{{url('')}}/public/assets/images/favicon.svg" type="image/x-icon"><!-- [Font] Family -->
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"
          integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA=="
          crossorigin="anonymous"/>


</head><!-- [Head] end --><!-- [Body] Start -->
<body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-layout="vertical" data-pc-direction="ltr"
      data-pc-theme_contrast="" data-pc-theme="light"><!-- [ Pre-loader ] start -->
<div class="page-loader">
    <div class="bar"></div>
</div><!-- [ Pre-loader ] End --><!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="/" class="b-brand text-primary">
                <img src="{{url('')}}/public/assets/images/logo.svg">
            </a>
        </div>
        <div class="navbar-content">


            @auth
                <div class="card pc-user-card">
                    <div class="card-body">

                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0"><img src="{{url('')}}/public/assets/images/user/avatar-1.jpg"
                                                            alt="user-image"
                                                            class="user-avtar wid-45 rounded-circle"></div>
                            <div class="flex-grow-1 ms-3 me-2"><h6 class="mb-0">{{Auth::user()->username}}</h6>
                                <small>Customer</small>
                            </div>
                            <a class="btn btn-icon btn-link-secondary avtar" data-bs-toggle="collapse"
                               href="#pc_sidebar_userlink">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-sort-outline"></use>
                                </svg>
                            </a></div>
                        <div class="collapse pc-user-links" id="pc_sidebar_userlink">
                            <div class="pt-3">
                                <a href="#">
                                    <i class="ti ti-user">
                                    </i>
                                    <span>Profile</span>
                                </a>


                                <a href="log-out">
                                    <i class="ti ti-power">

                                    </i>
                                    <span>Logout</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="pc-navbar">
                    <li class="pc-item pc-caption"><label>Home</label></li>
                    <li class="pc-item pc-menu">
                        <a href="home" class="pc-link"><span class="pc-micon">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-text-align-justify-center">
                                </use>
                            </svg>
                        </span>
                            <span class="pc-mtext">Dashboard</span>
                            <span class="pc-arrow">
                        </span>
                        </a>
                    </li>


                    <li class="pc-item pc-menu"><a href="fund-wallet" class="pc-link"><span
                                class="pc-micon">
                            <svg class="pc-icon"><use xlink:href="#custom-dollar-square"></use>
                            </svg> </span>
                            <span class="pc-mtext">Fund Wallet</span>
                            <span class="pc-arrow"><idata-feather="chevron-right"></i>
                        </span>
                        </a>

                    </li>


                    <li class="pc-item pc-caption"><label>Verification</label>
                        <svg class="pc-icon">
                            <use xlink:href="#custom-presentation-chart"></use>
                        </svg>
                    </li>
                    <li class="pc-item">
                        <a href="home" class="pc-link">
                        <span class="pc-micon">
                            <img src="{{url('')}}/public/assets/images/usa.svg" height="20" width="20">
                        </span>
                            <span class="pc-mtext">Verify USA Numbers</span>
                        </a>
                    </li>

                    <li class="pc-item">
                        <a href="world" class="pc-link">
                        <span class="pc-micon">
                            <img src="{{url('')}}/public/assets/images/world.svg" height="20" width="20">
                        </span>
                            <span class="pc-mtext">Verify All Countries</span>
                        </a>
                    </li>


                    <li class="pc-item">
                        <a href="orders" class="pc-link">
                        <span class="pc-micon">
                            <svg class="pc-icon"><use xlink:href="#custom-message-2">

                                </use>
                            </svg>
                        </span>
                            <span class="pc-mtext">My Verifications</span>
                        </a>
                    </li>


                    <li class="pc-item pc-caption"><label>Other Links</label>
                        <svg class="pc-icon">
                            <use xlink:href="#custom-layer"></use>
                        </svg>
                    </li>


                    <li class="pc-item">
                        <a href="https://socialplugboost.com" class="pc-link">
                        <span class="pc-micon">
                            <svg class="pc-icon"><use xlink:href="#custom-status-up"></use></svg>
                        </span>
                            <span class="pc-mtext">Boost Social Account</span>
                        </a>
                    </li>

                    <li class="pc-item">
                        <a href="https://loggsplug.com" class="pc-link">
                        <span class="pc-micon">
                            <svg class="pc-icon"><use xlink:href="#custom-bag"></use></svg>
                        </span>
                            <span class="pc-mtext">Buy Account</span>
                        </a>
                    </li>


                    <hr>


                    <li class="pc-item">

                        <a href="log-out" class="btn btn-dark w-100  btn-block">
                            Logout
                        </a>
                    </li>


                </ul>

            @else

                <div class="card">
                    <div class="card-body">


                        <a href="register" class="btn btn-dark w-100 my-3  btn-block">
                            Register
                        </a>

                        <a href="login" class="btn btn-primary w-100  btn-block">
                            Login
                        </a>


                    </div>

                </div>

            @endauth


        </div>
    </div>
</nav><!-- [ Sidebar Menu ] end --><!-- [ Header Topbar ] start -->
<header class="pc-header">
    <div class="header-wrapper"><!-- [Mobile Media Block] start -->
        <div class="me-auto pc-mob-drp">
            <ul class="list-unstyled"><!-- ======= Menu collapse Icon ===== -->
                <li class="pc-h-item pc-sidebar-collapse">
                    <a href="#" class="pc-head-link ms-0"
                       id="sidebar-hide"><i class="ti ti-menu-2">

                        </i>
                    </a>

                </li>

                <li class="pc-h-item pc-sidebar-popup">
                    <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                        <i class="ti ti-menu-2"></i>
                    </a>
                    <img src="{{url('')}}/public/assets/images/logo.svg">

                </li>


            </ul>
        </div><!-- [Mobile Media Block end] -->
        <div class="ms-auto">
            <ul class="list-unstyled">
                <li class="dropdown pc-h-item">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                       role="button" aria-haspopup="false" aria-expanded="false">
                        <svg class="pc-icon">
                            <use xlink:href="#custom-sun-1"></use>
                        </svg>
                    </a>


                    <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
                        <a href="#!"
                           class="dropdown-item"
                           onclick="layout_change('dark')">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-moon"></use>
                            </svg>
                            <span>Dark</span> </a>
                        <a href="#!" class="dropdown-item" onclick="layout_change('light')">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-sun-1"></use>
                            </svg>
                            <span>Light</span> </a><a href="#!" class="dropdown-item" onclick="layout_change_default()">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-setting-2"></use>
                            </svg>
                            <span>Default</span>
                        </a>
                    </div>
                </li>


                <li class="dropdown pc-h-item header-user-profile">

                    <a
                        class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                        href="#" role="button" aria-haspopup="false"
                        data-bs-auto-close="outside" aria-expanded="false">

                        <img src="{{url('')}}/public/assets/images/user/avatar-2.jpg"
                             alt="user-image" class="user-avtar">
                    </a>

                    <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                        @auth
                            <div class="dropdown-header d-flex align-items-center justify-content-between"><h5
                                    class="m-0">
                                    Profile</h5></div>
                            <div class="dropdown-body">
                                <div class="profile-notification-scroll position-relative"
                                     style="max-height: calc(100vh - 225px)">
                                    <div class="d-flex mb-1">
                                        <div class="flex-shrink-0"><img
                                                src="{{url('')}}/public/assets/images/user/avatar-2.jpg"
                                                alt="user-image" class="user-avtar wid-35"></div>
                                        <div class="flex-grow-1 ms-3"><h6 class="mb-1">{{Auth::user()->username}} 🖖</h6>
                                            <span><a
                                                    href="../cdn-cgi/l/email-protection.html" class="__cf_email__"
                                                    data-cfemail="80e3e1f2f3efeeaee4e1f2f2e9eec0e3efedf0e1eef9aee9ef">[email&#160;protected]</a></span>
                                        </div>
                                    </div>
                                    <hr class="border-secondary border-opacity-50">

                                    <p class="text-span">Manage</p>
                                    <a href="#"
                                       class="dropdown-item"><span><svg
                                                class="pc-icon text-muted me-2"><use
                                                    xlink:href="#custom-setting-outline"></use></svg> <span>Settings</span> </span></a>
                                    <a href="#" class="dropdown-item">
                                    <span><svg
                                            class="pc-icon text-muted me-2"><use xlink:href="#custom-share-bold"></use></svg> <span>Share</span> </span></a>
                                    <a href="#" class="dropdown-item"><span>
                                        <svg
                                            class="pc-icon text-muted me-2"><use
                                                xlink:href="#custom-lock-outline"></use></svg> <span>Change Password</span></span></a>
                                    <hr class="border-secondary border-opacity-50">
                                    <p class="text-span">Team</p><a href="#" class="dropdown-item"><span><svg
                                                class="pc-icon text-muted me-2"><use
                                                    xlink:href="#custom-profile-2user-outline"></use></svg> <span>UI Design team</span></span>
                                        <div class="user-group"><img
                                                src="{{url('')}}/public/assets/images/user/avatar-1.jpg"
                                                alt="user-image" class="avtar"> <span
                                                class="avtar bg-danger text-white">K</span> <span
                                                class="avtar bg-success text-white"><svg class="pc-icon m-0"><use
                                                        xlink:href="#custom-user"></use></svg> </span><span
                                                class="avtar bg-light-primary text-primary">+2</span></div>
                                    </a><a href="#" class="dropdown-item"><span><svg
                                                class="pc-icon text-muted me-2"><use
                                                    xlink:href="#custom-profile-2user-outline"></use></svg> <span>Friends Groups</span></span>
                                        <div class="user-group"><img
                                                src="{{url('')}}/public/assets/images/user/avatar-1.jpg"
                                                alt="user-image" class="avtar"> <span
                                                class="avtar bg-danger text-white">K</span> <span
                                                class="avtar bg-success text-white"><svg class="pc-icon m-0"><use
                                                        xlink:href="#custom-user"></use></svg></span></div>
                                    </a><a href="#" class="dropdown-item"><span><svg
                                                class="pc-icon text-muted me-2"><use
                                                    xlink:href="#custom-add-outline"></use></svg> <span>Add new</span></span>
                                        <div class="user-group"><span class="avtar bg-primary text-white"><svg
                                                    class="pc-icon m-0"><use
                                                        xlink:href="#custom-add-outline"></use></svg></span></div>
                                    </a>
                                    <hr class="border-secondary border-opacity-50">
                                    <div class="d-grid mb-3">
                                        <button class="btn btn-primary">
                                            <svg class="pc-icon me-2">
                                                <use xlink:href="#custom-logout-1-outline"></use>
                                            </svg>
                                            Logout
                                        </button>
                                    </div>
                                    <div class="card border-0 shadow-none drp-upgrade-card mb-0"
                                         style="background-image: url({{url('')}}/public/assets/images/layout/img-profile-card.jpg)">
                                        <div class="card-body">
                                            <div class="user-group"><img
                                                    src="{{url('')}}/public/assets/images/user/avatar-1.jpg"
                                                    alt="user-image" class="avtar"> <img
                                                    src="{{url('')}}/public/assets/images/user/avatar-2.jpg"
                                                    alt="user-image" class="avtar">
                                                <img src="{{url('')}}/public/assets/images/user/avatar-3.jpg"
                                                     alt="user-image"
                                                     class="avtar"> <img
                                                    src="{{url('')}}/public/assets/images/user/avatar-4.jpg"
                                                    alt="user-image" class="avtar"> <img
                                                    src="{{url('')}}/public/assets/images/user/avatar-5.jpg"
                                                    alt="user-image" class="avtar">
                                                <span class="avtar bg-light-primary text-primary">+20</span></div>
                                            <h3 class="my-3 text-dark">245.3k <small
                                                    class="text-muted">Followers</small>
                                            </h3>
                                            <div class="btn btn btn-warning">
                                                <svg class="pc-icon me-2">
                                                    <use xlink:href="#custom-logout-1-outline"></use>
                                                </svg>
                                                Upgrade to Business
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @else
                            <div class="dropdown-body">
                                <div class="profile-notification-scroll position-relative"
                                     style="max-height: calc(100vh - 225px)">


                                    <hr class="border-secondary border-opacity-50">
                                    <div class="d-grid mb-3">
                                        <a href="/login" class="btn btn-primary my-2">
                                            Login
                                        </a>

                                        <a href="register" class="btn btn-dark">
                                            Register
                                        </a>


                                    </div>
                                </div>
                            </div>
                        @endauth
                    </div>
                </li>


            </ul>
        </div>
    </div>
</header>
<div class="offcanvas pc-announcement-offcanvas offcanvas-end" tabindex="-1" id="announcement"
     aria-labelledby="announcementLabel">
    <div class="offcanvas-header"><h5 class="offcanvas-title" id="announcementLabel">What's new announcement?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

</div><!-- [ Header ] end --><!-- [ Main Content ] start -->


@yield('content')


<footer class="pc-footer">
    <p class="d-flex justify-content-center">2024 SMSLORD</p>
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
<div class="pct-c-btn"><a href="#" data-bs-toggle="offcanvas"
                          data-bs-target="#offcanvas_pc_layout"><i class="ph-duotone ph-gear-six"></i></a></div>
<div class="offcanvas border-0 pct-offcanvas offcanvas-end" tabindex="-1" id="offcanvas_pc_layout">
    <div class="offcanvas-header"><h5 class="offcanvas-title">Settings</h5>
        <button type="button" class="btn btn-icon btn-link-danger ms-auto" data-bs-dismiss="offcanvas"
                aria-label="Close"><i class="ti ti-x"></i></button>
    </div>
    <div class="pct-body customizer-body">
        <div class="offcanvas-body py-0">
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <div class="pc-dark"><h6 class="mb-1">Theme Mode</h6>
                        <p class="text-muted text-sm">Choose light or dark mode or Auto</p>
                        <div class="row theme-color theme-layout">
                            <div class="col-4">
                                <div class="d-grid">
                                    <button class="preset-btn btn active" data-value="true"
                                            onclick="layout_change('light');" data-bs-toggle="tooltip" title="Light">
                                        <svg class="pc-icon text-warning">
                                            <use xlink:href="#custom-sun-1"></use>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="d-grid">
                                    <button class="preset-btn btn" data-value="false" onclick="layout_change('dark');"
                                            data-bs-toggle="tooltip" title="Dark">
                                        <svg class="pc-icon">
                                            <use xlink:href="#custom-moon"></use>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="d-grid">
                                    <button class="preset-btn btn" data-value="default"
                                            onclick="layout_change_default();" data-bs-toggle="tooltip"
                                            title="Automatically sets the theme based on user's operating system's color scheme.">
                                        <span class="pc-lay-icon d-flex align-items-center justify-content-center"><i
                                                class="ph-duotone ph-cpu"></i></span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="list-group-item"><h6 class="mb-1">Theme Contrast</h6>
                    <p class="text-muted text-sm">Choose theme contrast</p>
                    <div class="row theme-contrast">
                        <div class="col-6">
                            <div class="d-grid">
                                <button class="preset-btn btn" data-value="true"
                                        onclick="layout_theme_contrast_change('true');" data-bs-toggle="tooltip"
                                        title="True">
                                    <svg class="pc-icon">
                                        <use xlink:href="#custom-mask"></use>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-grid">
                                <button class="preset-btn btn active" data-value="false"
                                        onclick="layout_theme_contrast_change('false');" data-bs-toggle="tooltip"
                                        title="False">
                                    <svg class="pc-icon">
                                        <use xlink:href="#custom-mask-1-outline"></use>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="list-group-item"><h6 class="mb-1">Custom Theme</h6>
                    <p class="text-muted text-sm">Choose your primary theme color</p>
                    <div class="theme-color preset-color"><a href="#!" data-bs-toggle="tooltip"
                                                             title="Blue" class="active" data-value="preset-1"><i
                                class="ti ti-checks"></i></a> <a href="#!"
                                                                 data-bs-toggle="tooltip" title="Indigo"
                                                                 data-value="preset-2"><i class="ti ti-checks"></i></a>
                        <a href="#!" data-bs-toggle="tooltip" title="Purple"
                           data-value="preset-3"><i class="ti ti-checks"></i></a> <a href="#!"
                                                                                     data-bs-toggle="tooltip"
                                                                                     title="Pink" data-value="preset-4"><i
                                class="ti ti-checks"></i></a> <a href="#!"
                                                                 data-bs-toggle="tooltip" title="Red"
                                                                 data-value="preset-5"><i class="ti ti-checks"></i></a>
                        <a href="#!" data-bs-toggle="tooltip" title="Orange"
                           data-value="preset-6"><i class="ti ti-checks"></i></a> <a href="#!"
                                                                                     data-bs-toggle="tooltip"
                                                                                     title="Yellow"
                                                                                     data-value="preset-7"><i
                                class="ti ti-checks"></i></a> <a href="#!"
                                                                 data-bs-toggle="tooltip" title="Green"
                                                                 data-value="preset-8"><i class="ti ti-checks"></i></a>
                        <a href="#!" data-bs-toggle="tooltip" title="Teal"
                           data-value="preset-9"><i class="ti ti-checks"></i></a> <a href="#!"
                                                                                     data-bs-toggle="tooltip"
                                                                                     title="Cyan"
                                                                                     data-value="preset-10"><i
                                class="ti ti-checks"></i></a></div>
                </li>
                <li class="list-group-item"><h6 class="mb-1">Theme layout</h6>
                    <p class="text-muted text-sm">Choose your layout</p>
                    <div class="theme-main-layout d-flex align-center gap-1 w-100"><a href="#!"
                                                                                      data-bs-toggle="tooltip"
                                                                                      title="Vertical" class="active"
                                                                                      data-value="vertical"><img
                                src="{{url('')}}/public/assets/images/customizer/caption-on.svg" alt="img"
                                class="img-fluid"> </a><a
                            href="#!" data-bs-toggle="tooltip" title="Horizontal"
                            data-value="horizontal"><img
                                src="{{url('')}}/public/assets/images/customizer/horizontal.svg" alt="img"
                                class="img-fluid"> </a><a href="#!"
                                                          data-bs-toggle="tooltip"
                                                          title="Color Header"
                                                          data-value="color-header"><img
                                src="{{url('')}}/public/assets/images/customizer/color-header.svg" alt="img"
                                class="img-fluid"> </a><a
                            href="#!" data-bs-toggle="tooltip" title="Compact"
                            data-value="compact"><img src="{{url('')}}/public/assets/images/customizer/compact.svg"
                                                      alt="img"
                                                      class="img-fluid"> </a><a href="#!"
                                                                                data-bs-toggle="tooltip" title="Tab"
                                                                                data-value="tab"><img
                                src="{{url('')}}/public/assets/images/customizer/tab.svg" alt="img"
                                class="img-fluid"></a></div>
                </li>
                <li class="list-group-item"><h6 class="mb-1">Sidebar Caption</h6>
                    <p class="text-muted text-sm">Sidebar Caption Hide/Show</p>
                    <div class="row theme-color theme-nav-caption">
                        <div class="col-6">
                            <div class="d-grid">
                                <button class="preset-btn btn-img btn active" data-value="true"
                                        onclick="layout_caption_change('true');" data-bs-toggle="tooltip"
                                        title="Caption Show"><img
                                        src="{{url('')}}/public/assets/images/customizer/caption-on.svg"
                                        alt="img" class="img-fluid"></button>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-grid">
                                <button class="preset-btn btn-img btn" data-value="false"
                                        onclick="layout_caption_change('false');" data-bs-toggle="tooltip"
                                        title="Caption Hide"><img
                                        src="{{url('')}}/public/assets/images/customizer/caption-off.svg"
                                        alt="img" class="img-fluid"></button>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="pc-rtl"><h6 class="mb-1">Theme Layout</h6>
                        <p class="text-muted text-sm">LTR/RTL</p>
                        <div class="row theme-color theme-direction">
                            <div class="col-6">
                                <div class="d-grid">
                                    <button class="preset-btn btn-img btn active" data-value="false"
                                            onclick="layout_rtl_change('false');" data-bs-toggle="tooltip" title="LTR">
                                        <img src="{{url('')}}/public/assets/images/customizer/ltr.svg" alt="img"
                                             class="img-fluid">
                                    </button>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-grid">
                                    <button class="preset-btn btn-img btn" data-value="true"
                                            onclick="layout_rtl_change('true');" data-bs-toggle="tooltip" title="RTL">
                                        <img src="{{url('')}}/public/assets/images/customizer/rtl.svg" alt="img"
                                             class="img-fluid">
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="list-group-item pc-box-width">
                    <div class="pc-container-width"><h6 class="mb-1">Layout Width</h6>
                        <p class="text-muted text-sm">Choose Full or Container Layout</p>
                        <div class="row theme-color theme-container">
                            <div class="col-6">
                                <div class="d-grid">
                                    <button class="preset-btn btn-img btn active" data-value="false"
                                            onclick="change_box_container('false')" data-bs-toggle="tooltip"
                                            title="Full Width"><img
                                            src="{{url('')}}/public/assets/images/customizer/full.svg" alt="img"
                                            class="img-fluid"></button>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-grid">
                                    <button class="preset-btn btn-img btn" data-value="true"
                                            onclick="change_box_container('true')" data-bs-toggle="tooltip"
                                            title="Fixed Width"><img
                                            src="{{url('')}}/public/assets/images/customizer/fixed.svg"
                                            alt="img" class="img-fluid"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="d-grid">
                        <button class="btn btn-light-danger" id="layoutreset">Reset Layout</button>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<script>preset_change('preset-4');</script>
<script>main_layout_change('vertical');</script>


{{--<style>--}}
{{--    .float {--}}
{{--        position: fixed;--}}
{{--        width: 60px;--}}
{{--        height: 60px;--}}
{{--        bottom: 40px;--}}
{{--        right: 40px;--}}
{{--        background-color: #000000;--}}
{{--        color: #FFF;--}}
{{--        border-radius: 50px;--}}
{{--        text-align: center;--}}
{{--        font-size: 30px;--}}
{{--        box-shadow: 2px 2px 3px #999;--}}
{{--        z-index: 100;--}}
{{--    }--}}

{{--    .my-float {--}}
{{--        margin-top: 16px;--}}
{{--    }--}}
{{--</style>--}}

{{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">--}}
{{--<a href="https://t.me/verifyasapp" class="float" target="_blank">--}}
{{--    <i class="fa fa-comment my-float"></i>--}}
{{--</a>--}}


<script>function changebrand(presetColor) {
        removeClassByPrefix(document.querySelector('body'), 'preset-');
        document.querySelector('body').classList.add(presetColor);
    }

    localStorage.setItem('layout', 'color-header');</script>
</body>
<!-- [Body] end -->
</html>
