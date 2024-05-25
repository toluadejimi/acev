<!doctype html>
<html lang="en"><!-- [Head] start -->
<head><title>ACESMSVERIFY | LOGIN</title><!-- [Meta] -->
    <meta charset="utf-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description"
          content="ACESMSVERIFY NUMBER VERIFICATION">
    <meta name="keywords"
          content="Bootstrap admin template, Dashboard UI Kit, Dashboard Template, Backend Panel, react dashboard, angular dashboard">
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
</head><!-- [Head] end --><!-- [Body] Start -->
<body data-pc-preset="preset-4" data-pc-sidebar-caption="true" data-pc-layout="vertical" data-pc-direction="ltr"
      data-pc-theme_contrast="" data-pc-theme="light"><!-- [ Pre-loader ] start -->
<div class="page-loader">
    <div class="bar"></div>
</div><!-- [ Pre-loader ] End -->
<div class="auth-main">
    <div class="auth-wrapper v1">
        <div class="auth-form">


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


            <div class="card my-5" style="box-shadow: 4px 2px 28px rgba(94,94,94,0.37);">
                <div class="card-body ">
                    <div class="d-flex justify-content-start">
                        <h3 class="my-4">Create Account</h3>
                    </div>

                    <p class="mb-5 text-muted"> Enter your information below</p>


                    <form action="login_now" method="post">
                        @csrf


                    </form>

                    <form action="register_now" method="post">
                        @csrf


                        <div class="mb-3">
                            <input type="text" placeholder="Username" name="username" class="form-control" autofocus
                                   id="floatingInput">
                        </div>

                        <div class="mb-3">
                            <input name="email" type="email" autofocus class="form-control" id="floatingInput"
                                   placeholder="Email Address">
                        </div>


                        <div class="mb-3">
                            <input name="password" type="password" class="form-control" id="floatingInput1"
                                   placeholder="Password">
                        </div>

                        <div class="mb-3">
                            <input name="password_confirmation" type="password" class="form-control" id="floatingInput1"
                                   placeholder="Confirm Password">
                        </div>




                        <div class="d-grid mt-4">
                            <button type="submit" style="background: rgba(23, 69, 132, 1); border: 0px" class="btn btn-primary">Register</button>
                        </div>




                        <div class="d-flex justify-content-center mt-5 align-items-end text-dark mt-4">
                            <h6 class="f-w-500 text-dark mb-0">Already have an account? </h6>
                            <a href="login" class="text-primary">Login</a></div>




                    </form>

                </div>
            </div>
        </div>
    </div>
</div><!-- [ Main Content ] end --><!-- Required Js -->
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
<script>preset_change('preset-1');</script>
<script>main_layout_change('vertical');</script>
<div class="pct-c-btn"><a href="login-v1.html#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_pc_layout"><i
            class="ph-duotone ph-gear-six"></i></a></div>
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
                    <div class="theme-color preset-color"><a href="login-v1.html#!" data-bs-toggle="tooltip"
                                                             title="Blue" class="active" data-value="preset-1"><i
                                class="ti ti-checks"></i></a> <a href="login-v1.html#!" data-bs-toggle="tooltip"
                                                                 title="Indigo" data-value="preset-2"><i
                                class="ti ti-checks"></i></a> <a href="login-v1.html#!" data-bs-toggle="tooltip"
                                                                 title="Purple" data-value="preset-3"><i
                                class="ti ti-checks"></i></a> <a href="login-v1.html#!" data-bs-toggle="tooltip"
                                                                 title="Pink" data-value="preset-4"><i
                                class="ti ti-checks"></i></a> <a href="login-v1.html#!" data-bs-toggle="tooltip"
                                                                 title="Red" data-value="preset-5"><i
                                class="ti ti-checks"></i></a> <a href="login-v1.html#!" data-bs-toggle="tooltip"
                                                                 title="Orange" data-value="preset-6"><i
                                class="ti ti-checks"></i></a> <a href="login-v1.html#!" data-bs-toggle="tooltip"
                                                                 title="Yellow" data-value="preset-7"><i
                                class="ti ti-checks"></i></a> <a href="login-v1.html#!" data-bs-toggle="tooltip"
                                                                 title="Green" data-value="preset-8"><i
                                class="ti ti-checks"></i></a> <a href="login-v1.html#!" data-bs-toggle="tooltip"
                                                                 title="Teal" data-value="preset-9"><i
                                class="ti ti-checks"></i></a> <a href="login-v1.html#!" data-bs-toggle="tooltip"
                                                                 title="Cyan" data-value="preset-10"><i
                                class="ti ti-checks"></i></a></div>
                </li>
                <li class="list-group-item"><h6 class="mb-1">Theme layout</h6>
                    <p class="text-muted text-sm">Choose your layout</p>
                    <div class="theme-main-layout d-flex align-center gap-1 w-100"><a href="login-v1.html#!"
                                                                                      data-bs-toggle="tooltip"
                                                                                      title="Vertical" class="active"
                                                                                      data-value="vertical"><img
                                src="{{url('')}}/public/assets/images/customizer/caption-on.svg" alt="img"
                                class="img-fluid"> </a><a
                            href="login-v1.html#!" data-bs-toggle="tooltip" title="Horizontal"
                            data-value="horizontal"><img
                                src="{{url('')}}/public/assets/images/customizer/horizontal.svg" alt="img"
                                class="img-fluid"> </a><a href="login-v1.html#!"
                                                          data-bs-toggle="tooltip"
                                                          title="Color Header"
                                                          data-value="color-header"><img
                                src="{{url('')}}/public/assets/images/customizer/color-header.svg" alt="img"
                                class="img-fluid"> </a><a
                            href="login-v1.html#!" data-bs-toggle="tooltip" title="Compact" data-value="compact"><img
                                src="{{url('')}}/public/assets/images/customizer/compact.svg" alt="img"
                                class="img-fluid"> </a><a
                            href="login-v1.html#!" data-bs-toggle="tooltip" title="Tab" data-value="tab"><img
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
</body><!-- [Body] end --></html>













