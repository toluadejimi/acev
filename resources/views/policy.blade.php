<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{url('')}}/public/assets/storage/images/favicon.png" type="image/png">
    <title>ACEVERIFY</title>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">



    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA==" crossorigin="anonymous" />
    <link rel="stylesheet" href="{{url('')}}/public/assets/css/vendor.css">
    <link rel="stylesheet" href="{{url('')}}/public/assets/css/common.css">
    <script src="{{url('')}}/public/assets/vendor/Shortcode/Shortcode.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <script src="{{url('')}}/public/assets/js/app.js" defer></script>
    <style>
        [wire\:loading],
        [wire\:loading\.delay],
        [wire\:loading\.inline-block],
        [wire\:loading\.inline],
        [wire\:loading\.block],
        [wire\:loading\.flex],
        [wire\:loading\.table],
        [wire\:loading\.grid],
        [wire\:loading\.inline-flex] {
            display: none;
        }

        [wire\:loading\.delay\.shortest],
        [wire\:loading\.delay\.shorter],
        [wire\:loading\.delay\.short],
        [wire\:loading\.delay\.long],
        [wire\:loading\.delay\.longer],
        [wire\:loading\.delay\.longest] {
            display: none;
        }

        [wire\:offline] {
            display: none;
        }

        [wire\:dirty]:not(textarea):not(input):not(select) {
            display: none;
        }

        input:-webkit-autofill,
        select:-webkit-autofill,
        textarea:-webkit-autofill {
            animation-duration: 50000s;
            animation-name: livewireautofill;
        }

        @keyframes livewireautofill {
            from {}
        }


        /* line 27, bootstrap-select.scss */
        select.bs-select-hidden,
        select.selectpicker {
            display: none !important;
        }

        /* line 32, bootstrap-select.scss */
        .bootstrap-select {
            width: 220px \0;
            /*IE9 and below*/
        }

        /* line 37, bootstrap-select.scss */
        .bootstrap-select.btn-group>.dropdown-toggle {
            height: 100%;
        }

        /* line 43, bootstrap-select.scss */
        .bootstrap-select>.dropdown-toggle {
            width: 100%;
            padding-right: 25px;
            z-index: 1;
        }

        /* line 48, bootstrap-select.scss */
        .bootstrap-select>.dropdown-toggle.bs-placeholder,
        .bootstrap-select>.dropdown-toggle.bs-placeholder:hover,
        .bootstrap-select>.dropdown-toggle.bs-placeholder:focus,
        .bootstrap-select>.dropdown-toggle.bs-placeholder:active {
            color: #999;
        }

        /* line 56, bootstrap-select.scss */
        .bootstrap-select>select {
            position: absolute !important;
            bottom: 0;
            left: 50%;
            display: block !important;
            width: 0.5px !important;
            height: 100% !important;
            padding: 0 !important;
            opacity: 0 !important;
            border: none;
        }

        /* line 67, bootstrap-select.scss */
        .bootstrap-select>select.mobile-device {
            top: 0;
            left: 0;
            display: block !important;
            width: 100% !important;
            z-index: 2;
        }

        /* line 77, bootstrap-select.scss */
        .has-error .bootstrap-select .dropdown-toggle,
        .error .bootstrap-select .dropdown-toggle {
            border-color: #b94a48;
        }

        /* line 82, bootstrap-select.scss */
        .bootstrap-select.fit-width {
            width: auto !important;
        }

        /* line 86, bootstrap-select.scss */
        .bootstrap-select:not([class*="col-"]):not([class*="form-control"]):not(.input-group-btn) {
            width: 220px;
        }

        /* line 90, bootstrap-select.scss */
        .bootstrap-select .dropdown-toggle:focus {
            outline: thin dotted #333333 !important;
            outline: 5px auto -webkit-focus-ring-color !important;
            outline-offset: -2px;
        }

        /* line 97, bootstrap-select.scss */
        .bootstrap-select.form-control {
            margin-bottom: 0;
            padding: 0;
            border: none;
        }

        /* line 102, bootstrap-select.scss */
        .bootstrap-select.form-control:not([class*="col-"]) {
            width: 100%;
        }

        /* line 106, bootstrap-select.scss */
        .bootstrap-select.form-control.input-group-btn {
            z-index: auto;
        }

        /* line 110, bootstrap-select.scss */
        .bootstrap-select.form-control.input-group-btn:not(:first-child):not(:last-child)>.btn {
            border-radius: 0;
        }

        /* line 119, bootstrap-select.scss */
        .bootstrap-select.btn-group:not(.input-group-btn),
        .bootstrap-select.btn-group[class*="col-"] {
            float: none;
            display: inline-block;
            margin-left: 0;
        }

        /* line 130, bootstrap-select.scss */
        .bootstrap-select.btn-group.dropdown-menu-right,
        .bootstrap-select.btn-group[class*="col-"].dropdown-menu-right,
        .row .bootstrap-select.btn-group[class*="col-"].dropdown-menu-right {
            float: right;
        }

        /* line 135, bootstrap-select.scss */
        .form-inline .bootstrap-select.btn-group,
        .form-horizontal .bootstrap-select.btn-group,
        .form-group .bootstrap-select.btn-group {
            margin-bottom: 0;
        }

        /* line 141, bootstrap-select.scss */
        .form-group-lg .bootstrap-select.btn-group.form-control,
        .form-group-sm .bootstrap-select.btn-group.form-control {
            padding: 0;
        }

        /* line 145, bootstrap-select.scss */
        .form-group-lg .bootstrap-select.btn-group.form-control .dropdown-toggle,
        .form-group-sm .bootstrap-select.btn-group.form-control .dropdown-toggle {
            height: 100%;
            font-size: inherit;
            line-height: inherit;
            border-radius: inherit;
        }

        /* line 155, bootstrap-select.scss */
        .form-inline .bootstrap-select.btn-group .form-control {
            width: 100%;
        }

        /* line 159, bootstrap-select.scss */
        .bootstrap-select.btn-group.disabled,
        .bootstrap-select.btn-group>.disabled {
            cursor: not-allowed;
        }

        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 1px solid #3f78e0;
            border-radius: 4px
        }

        /* line 163, bootstrap-select.scss */
        .bootstrap-select.btn-group.disabled:focus,
        .bootstrap-select.btn-group>.disabled:focus {
            outline: none !important;
        }

        /* line 168, bootstrap-select.scss */
        .bootstrap-select.btn-group.bs-container {
            position: absolute;
            height: 0 !important;
            padding: 0 !important;
        }

        /* line 173, bootstrap-select.scss */
        .bootstrap-select.btn-group.bs-container .dropdown-menu {
            z-index: 1060;
        }

        /* line 180, bootstrap-select.scss */
        .bootstrap-select.btn-group .dropdown-toggle .filter-option {
            display: inline-block;
            overflow: hidden;
            width: 100%;
            text-align: left;
        }

        /* line 187, bootstrap-select.scss */
        .bootstrap-select.btn-group .dropdown-toggle .caret {
            position: absolute;
            top: 50%;
            right: 12px;
            margin-top: -2px;
            vertical-align: middle;
        }

        /* line 196, bootstrap-select.scss */
        .bootstrap-select.btn-group[class*="col-"] .dropdown-toggle {
            width: 100%;
        }

        /* line 201, bootstrap-select.scss */
        .bootstrap-select.btn-group .dropdown-menu {
            min-width: 100%;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        /* line 205, bootstrap-select.scss */
        .bootstrap-select.btn-group .dropdown-menu.inner {
            display: block;
            position: static;
            float: none;
            border: 0;
            padding: 0;
            margin: 0;
            border-radius: 0;
            box-shadow: none;
        }

        /* line 216, bootstrap-select.scss */
        .bootstrap-select.btn-group .dropdown-menu a.dropdown-item {
            position: relative;
            cursor: pointer;
            user-select: none;
        }

        /* line 221, bootstrap-select.scss */
        .bootstrap-select.btn-group .dropdown-menu a.dropdown-item.active small {
            color: #fff;
        }

        /* line 225, bootstrap-select.scss */
        .bootstrap-select.btn-group .dropdown-menu a.dropdown-item.disabled a {
            cursor: not-allowed;
        }

        /* line 229, bootstrap-select.scss */
        .bootstrap-select.btn-group .dropdown-menu a.dropdown-item.hidden {
            display: none;
        }

        /* line 233, bootstrap-select.scss */
        .bootstrap-select.btn-group .dropdown-menu a.dropdown-item span.dropdown-item-inner {
            display: block;
        }

        /* line 236, bootstrap-select.scss */
        .bootstrap-select.btn-group .dropdown-menu a.dropdown-item span.dropdown-item-inner.opt {
            position: relative;
            padding-left: 2.25em;
        }

        /* line 241, bootstrap-select.scss */
        .bootstrap-select.btn-group .dropdown-menu a.dropdown-item span.dropdown-item-inner span.check-mark {
            display: none;
        }

        /* line 245, bootstrap-select.scss */
        .bootstrap-select.btn-group .dropdown-menu a.dropdown-item span.dropdown-item-inner span.text {
            display: inline-block;
        }

        /* line 250, bootstrap-select.scss */
        .bootstrap-select.btn-group .dropdown-menu a.dropdown-item small {
            padding-left: 0.5em;
        }

        /* line 257, bootstrap-select.scss */
        .bootstrap-select.btn-group .dropdown-menu .dropdown-item .span.check-mark {
            display: none;
        }

        /* line 261, bootstrap-select.scss */
        .bootstrap-select.btn-group .dropdown-menu .dropdown-item .span.text {
            display: inline-block;
        }

        /* line 266, bootstrap-select.scss */
        .bootstrap-select.btn-group .dropdown-menu .notify {
            position: absolute;
            bottom: 5px;
            width: 96%;
            margin: 0 2%;
            min-height: 26px;
            padding: 3px 5px;
            background: whitesmoke;
            border: 1px solid #e3e3e3;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
            pointer-events: none;
            opacity: 0.9;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        /* line 282, bootstrap-select.scss */
        .bootstrap-select.btn-group .no-results {
            padding: 3px;
            background: #f5f5f5;
            margin: 0 5px;
            white-space: nowrap;
        }

        /* line 290, bootstrap-select.scss */
        .bootstrap-select.btn-group.fit-width .dropdown-toggle .filter-option {
            position: static;
        }

        /* line 294, bootstrap-select.scss */
        .bootstrap-select.btn-group.fit-width .dropdown-toggle .caret {
            position: static;
            top: auto;
            margin-top: -1px;
        }

        /* line 302, bootstrap-select.scss */
        .bootstrap-select.btn-group.show-tick .dropdown-menu a.selected span.dropdown-item-inner span.check-mark {
            position: absolute;
            display: inline-block;
            right: 15px;
            margin-top: 5px;
        }

        /* line 309, bootstrap-select.scss */
        .bootstrap-select.btn-group.show-tick .dropdown-menu a a span.text {
            margin-right: 34px;
        }

        /* line 316, bootstrap-select.scss */
        .bootstrap-select.show-menu-arrow.open>.dropdown-toggle {
            z-index: 1061;
        }

        /* line 321, bootstrap-select.scss */
        .bootstrap-select.show-menu-arrow .dropdown-toggle:before {
            content: '';
            border-left: 7px solid transparent;
            border-right: 7px solid transparent;
            border-bottom: 7px solid rgba(204, 204, 204, 0.2);
            position: absolute;
            bottom: -4px;
            left: 9px;
            display: none;
        }

        /* line 332, bootstrap-select.scss */
        .bootstrap-select.show-menu-arrow .dropdown-toggle:after {
            content: '';
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-bottom: 6px solid white;
            position: absolute;
            bottom: -4px;
            left: 10px;
            display: none;
        }

        /* line 345, bootstrap-select.scss */
        .bootstrap-select.show-menu-arrow.dropup .dropdown-toggle:before {
            bottom: auto;
            top: -3px;
            border-top: 7px solid rgba(204, 204, 204, 0.2);
            border-bottom: 0;
        }

        /* line 352, bootstrap-select.scss */
        .bootstrap-select.show-menu-arrow.dropup .dropdown-toggle:after {
            bottom: auto;
            top: -3px;
            border-top: 6px solid white;
            border-bottom: 0;
        }

        /* line 361, bootstrap-select.scss */
        .bootstrap-select.show-menu-arrow.pull-right .dropdown-toggle:before {
            right: 12px;
            left: auto;
        }

        /* line 366, bootstrap-select.scss */
        .bootstrap-select.show-menu-arrow.pull-right .dropdown-toggle:after {
            right: 13px;
            left: auto;
        }

        /* line 373, bootstrap-select.scss */
        .bootstrap-select.show-menu-arrow.open>.dropdown-toggle:before,
        .bootstrap-select.show-menu-arrow.open>.dropdown-toggle:after {
            display: block;
        }

        /* line 380, bootstrap-select.scss */
        .bs-searchbox,
        .bs-actionsbox,
        .bs-donebutton {
            padding: 4px 8px;
        }

        /* line 386, bootstrap-select.scss */
        .bs-actionsbox {
            width: 100%;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        /* line 390, bootstrap-select.scss */
        .bs-actionsbox .btn-group button {
            width: 50%;
        }

        /* line 395, bootstrap-select.scss */
        .bs-donebutton {
            float: left;
            width: 100%;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        /* line 400, bootstrap-select.scss */
        .bs-donebutton .btn-group button {
            width: 100%;
        }

        /* line 406, bootstrap-select.scss */
        .bs-searchbox+.bs-actionsbox {
            padding: 0 8px 4px;
        }

        /* line 410, bootstrap-select.scss */
        .bs-searchbox .form-control {
            margin-bottom: 0;
            width: 100%;
            float: none;
        }

        /* line 417, bootstrap-select.scss */
        .input-group .bs-searchbox .form-control {
            width: 100%;
        }

    </style>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Kadwa:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --head-font: "Kadwa";
            --body-font: "Poppins";
        }

        .bg-primary {
            background-color: #6633db;
        }

        .text-primary {
            color: #6633db;
        }

        .border-primary {
            border-color: #6633db;
        }

        .bg-secondary {
            background-color: #c1dc78;
        }

        .text-secondary {
            color: #c1dc78;
        }

        .border-secondary {
            border-color: #c1dc78;
        }

        .bg-tertiary {
            background-color: #d2ab3e;
        }

        .text-tertiary {
            color: #d2ab3e;
        }

        .border-tertiary {
            border-color: #d2ab3e;
        }

        input:checked~.toggle-path {
            background-color: #6633db;
        }

    </style>
    <style>
        #span-3 {
            color: #6633db;
            white-space: nowrap;
            justify-content: center;
            border-radius: 20px;
            background-color: #1e1e1e;
            padding: 21px 50px;
            font: 700 16px Inter, sans-serif;
        }

        #div-2 {
            white-space: nowrap;
            justify-content: center;
            color: #6633db;
            border-radius: 20px;
            padding: 21px 50px;
            font: 700 16px Inter, sans-serif;
        }

        #myDiv3 {
            display: none;
        }


        .input_copy_wrapper {
            width: 315px;
            padding: 0px;
            margin: auto;
        }

        .input_copy_wrapper2 {
            width: 315px;
            padding: 0px;
            margin: auto;
        }

        .input_copy {
            padding: 15px 25px;
            background: #ffffff;
            border: 2px solid #aaa;
            color: #000;
            font-size: .8em;
            border-radius: 10px;
            border: solid #032460;
            margin-bottom: 30px;
        }

        .input_copy2 {
            padding: 15px 25px;
            background: #ffffff;
            border: 2px solid #aaa;
            color: #000;
            font-size: .8em;
            border-radius: 10px;
            border: solid #032460;
            margin-bottom: 30px;
        }

        .input_copy .icon {
            display: block;
            max-width: 25px;
            cursor: pointer;
            float: right;
        }

        .input_copy2 .icon2 {
            display: block;
            max-width: 25px;
            cursor: pointer;
            float: right;
        }

        .input_copy .icon img {
            max-width: 15px;
        }



        .input_copy2 .txt2 {
            width: 80%;
            display: inline-block;
            overflow: hidden;
        }

        .input_copy .txt {
            width: 80%;
            display: inline-block;
            overflow: hidden;
        }



        /* click animation */

        .flashBG {
            animation-name: flash;
            animation-timing-function: ease-out;
            animation-duration: 1s;
        }

        @keyframes flash {
            0% {
                background: #28a745;
            }

            100% {
                background: transparent;
            }
        }

    </style>
</head>

<body>
    <div class="default-theme">



        <!DOCTYPE html>
        <html wire:id="KYyr7BXHmAukicbrQqma" wire:initial-data="{&quot;fingerprint&quot;:{&quot;id&quot;:&quot;KYyr7BXHmAukicbrQqma&quot;,&quot;name&quot;:&quot;frontend.home&quot;,&quot;locale&quot;:&quot;en&quot;,&quot;path&quot;:&quot;\/&quot;,&quot;method&quot;:&quot;GET&quot;,&quot;v&quot;:&quot;acj&quot;},&quot;effects&quot;:{&quot;listeners&quot;:[]},&quot;serverMemo&quot;:{&quot;children&quot;:[],&quot;errors&quot;:[],&quot;htmlHash&quot;:&quot;295eff0e&quot;,&quot;data&quot;:{&quot;numbers&quot;:[],&quot;features&quot;:[],&quot;sections&quot;:[],&quot;assigned&quot;:[],&quot;user&quot;:[],&quot;orders&quot;:[],&quot;filters&quot;:{&quot;number&quot;:{&quot;country&quot;:[]},&quot;private&quot;:{&quot;country&quot;:[]},&quot;rented&quot;:{&quot;country&quot;:[]}},&quot;private&quot;:[],&quot;rented&quot;:[],&quot;types&quot;:[&quot;Open&quot;,&quot;Register Only&quot;,&quot;Private&quot;,&quot;Shared Buy&quot;,&quot;Private Buy&quot;],&quot;bg&quot;:[&quot;bg-gray-200&quot;,&quot;bg-lime-200&quot;,&quot;bg-red-200&quot;,&quot;bg-purple-200&quot;,&quot;bg-pink-200&quot;],&quot;text&quot;:[&quot;text-gray-800&quot;,&quot;text-lime-800&quot;,&quot;text-red-800&quot;,&quot;text-purple-800&quot;,&quot;text-pink-800&quot;]},&quot;dataMeta&quot;:{&quot;modelCollections&quot;:{&quot;numbers&quot;:{&quot;class&quot;:null,&quot;id&quot;:[],&quot;relations&quot;:[],&quot;connection&quot;:null,&quot;collectionClass&quot;:null},&quot;features&quot;:{&quot;class&quot;:&quot;App\\Models\\Feature&quot;,&quot;id&quot;:[1,2,3],&quot;relations&quot;:[],&quot;connection&quot;:&quot;mysql&quot;,&quot;collectionClass&quot;:null},&quot;sections&quot;:{&quot;class&quot;:&quot;App\\Models\\Section&quot;,&quot;id&quot;:[1,2],&quot;relations&quot;:[],&quot;connection&quot;:&quot;mysql&quot;,&quot;collectionClass&quot;:null},&quot;assigned&quot;:{&quot;class&quot;:null,&quot;id&quot;:[],&quot;relations&quot;:[],&quot;connection&quot;:null,&quot;collectionClass&quot;:null},&quot;orders&quot;:{&quot;class&quot;:null,&quot;id&quot;:[],&quot;relations&quot;:[],&quot;connection&quot;:null,&quot;collectionClass&quot;:null}},&quot;models&quot;:{&quot;user&quot;:{&quot;class&quot;:&quot;App\\Models\\User&quot;,&quot;id&quot;:7,&quot;relations&quot;:[],&quot;connection&quot;:&quot;mysql&quot;,&quot;collectionClass&quot;:null}},&quot;collections&quot;:[&quot;private&quot;,&quot;rented&quot;]},&quot;checksum&quot;:&quot;cf962efb8d221e5ebe86618f48f0f85d335196fa78ebeb696ca9cf0e7856a18d&quot;}}" lang="en">

        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="description" content="An impressive and flawless site template that includes various UI elements and countless features, attractive ready-made blocks and rich pages, basically everything you need to create a unique and professional website.">
            <meta name="keywords" content="bootstrap 5, business, corporate, creative, gulp, marketing, minimal, modern, multipurpose, one page, responsive, saas, sass, seo, startup, html5 template, site template">
            <meta name="author" content="elemis">
            <title>ACEVERIFY | SMS Verification Website.</title>
            <link rel="shortcut icon" href="{{url('')}}/public/assets/img/favicon.png">
            <link rel="stylesheet" href="{{url('')}}/public/assets/css/plugins.css">
            <link rel="stylesheet" href="{{url('')}}/public/assets/css/style.css">
            <link rel="preload" href="{{url('')}}/public/assets/css/fonts/space.css" as="style" onload="this.rel='stylesheet'">
        </head>

        <body>
            <div class="content-wrapper">
                <header class="wrapper bg-light">

                    <nav class="navbar navbar-expand-lg center-nav transparent navbar-light">
                        <div class="container flex-lg-row flex-nowrap align-items-center">
                            <div class="navbar-brand w-100">
                                <a href="/">
                                    <img src="{{url('')}}/public/assets/img/logo.png" alt="">
                                </a>
                            </div>
                            <div class="navbar-collapse offcanvas offcanvas-nav offcanvas-start">
                                <div class="offcanvas-header d-lg-none">
                                    <h3 class="text-white fs-30 mb-0">VerifyAsap</h3>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                </div>
                                <div class="offcanvas-body ms-lg-auto d-flex flex-column h-100">
                                    <ul class="navbar-nav">


                                        <li class="nav-item dropdown">
                                            <a class="nav-link " href="/">Home</a>

                                        </li>



                                    </ul>
                                    <!-- /.navbar-nav -->
                                    <div class="offcanvas-footer d-lg-none">
                                        <div>
                                            <nav class="nav social social-white mt-4">
                                                <a href="#"><i class="uil uil-twitter"></i></a>
                                                <a href="#"><i class="uil uil-facebook-f"></i></a>
                                                <a href="#"><i class="uil uil-dribbble"></i></a>
                                                                                    <a href="https://www.instagram.com/verifyasap.co?igsh=MWtkanlibnhwYzdyag%3D%3D&utm_source=qr"><i class="uil uil-instagram"></i></a>
                                                <a href="#"><i class="uil uil-youtube"></i></a>
                                            </nav>
                                            <!-- /.social -->
                                        </div>
                                    </div>
                                    <!-- /.offcanvas-footer -->
                                </div>
                                <!-- /.offcanvas-body -->
                            </div>
                            <!-- /.navbar-collapse -->
                            <div class="navbar-other w-100 d-flex ms-auto">
                                <ul class="navbar-nav flex-row align-items-center ms-auto">

                                    {{-- <li class="nav-item d-none d-md-block">
                                        <a href="contact.html" class="btn btn-sm btn-primary rounded">Contact</a>
                                    </li> --}}
                                    <li class="nav-item d-lg-none">
                                        <button class="hamburger offcanvas-nav-btn"><span></span></button>
                                    </li>
                                </ul>
                                <!-- /.navbar-nav -->
                            </div>
                            <!-- /.navbar-other -->
                        </div>
                        <!-- /.container -->
                    </nav>
                    <!-- /.navbar -->
                </header>
                <!-- /header -->
                <section class="wrapper">
                    <div class="container pt-11 pt-md-13 pb-19 pb-md-17">
                        <div class="row">


                            <div class="col-lg-9 col-xl-8 col-xxl-7 mx-auto  text-center" data-cues="slideInDown" data-group="page-title" data-delay="500">
                                <h5 class="display-1 ls-sm fs-40 mb-4 px-md-8 px-lg-0">About VERIFYASAP</h5>

                                    <p class="lead fs-19 lh-sm mb-7  text-center">
                                    <strong>How Does verifyasap.com Protect Your Privacy?</strong><br>
                                    Verifyasap ensures your privacy by allowing you to use our disposable numbers instead of your personal number.
                                   <br> This way, you never have to share your personal information with any website or app. We provide real sim-card numbers from countries such as the US, UK, China, India, Australia, and many more, ensuring that your identity is protected.</p>

                                    <p class="lead fs-19 lh-sm mb-7  text-center">  <strong>What is a Disposable Number?</strong><br>
                                    A disposable phone number, also known as a temporary number, is a phone number that is only active for a short period of time, usually 10 minutes. <br>
                                    These numbers are typically used to receive SMS verification codes on websites or apps where users don’t want to share their personal number.<br></p>

                                     <p class="lead fs-19 lh-sm mb-7  text-center"> You can now verify your online accounts without using your personal, work or business numbers.<br> Verifyasap provides real sim card numbers for you to create and verify your accounts.<br>
                                      Using our platform , we offer a wide variety of countries for SMS Verifications</p>


                                    STOP GIVING OUT YOUR REAL NUMBERS ONLINE

                                    <p class="lead fs-19 lh-sm mb-7  text-center">
                                    <strong>Why Do You Need Virtual Phone Numbers</strong><br>
                                    A considerable number of accounts for commercial and personal purposes are being opened.<br> When establishing anaccount, the website requires a legitimate mobile number:<br> most of the time, only one account may beestablished by a single phone. <br>
                                    If you use virtual numbers, you will be able to create pages on social networkswithout limitation.
                                    <br>Webmasters and SMM/ORM professionals, for whom profiles are important tools for leadingand promoting campaigns, would appreciate this.
                                   <br> Keep your anonymity. A well-known fact is that if you know the number, you may learn information about theowner, such as surname, name, passport number, and dwelling location. When using the SMS activation service,information leakage is avoided.
                                    Keep an eye out for con artists. You've probably encountered websites that need you to provide your phonenumber in order to download a file that doesn't have a speed limit. By submitting your phone number on suchsites, you risk linking paid services and subscriptions. As a result, we propose using a throwaway temporaryphone number.
                                    Take part in events to get savings. Promotional activities are carried out by a variety of enterprises andonline shops, such as the distribution of free items in exchange for phone numbers. In such cases, the "onegift to one hand" rule applies, which is why a phone number and SMS confirmation are required. Virtual numbersfor SMS online allow you to escape restrictions and enjoy an infinite amount of free incentives.
                                    Remove all country limitations. Some Internet projects exclude residents of specific states from enrolling.The reasons for this might vary; for example, a Russian user may be unable to access a Ukrainian site, andvice versa. A Belarusian operator's SMS message may not always reach its intended target. Then, on comparableInternet sites, a temporary SMS number will come in useful.</p>


                                </p>
                            </div>



                        </div>



                    </div>

                </section>
                <!-- /section -->






                </section>
                <!-- /section -->
            </div>
            <!-- /.content-wrapper -->
            <footer class="bg-soft-primary">
                <div class="container pt-17 pt-md-19 pb-14 pb-md-16">
                    <div class="row gy-6 gy-lg-0">
                        <div class="col-md-4 col-lg-3">
                            <div class="widget">
                                <img class="mb-4" src="{{url('')}}/public/assets/img/logo.png" alt="">
                                <p class="mb-4">© 2024 ACEVERIFY. <br class="d-none d-lg-block">All rights reserved.
                                </p>
                                <nav class="nav social ">
                                    <a href="#"><i class="uil uil-twitter"></i></a>
                                    <a href="#"><i class="uil uil-facebook-f"></i></a>
                                    <a href="#"><i class="uil uil-dribbble"></i></a>
                                    <a href="https://www.instagram.com/verifyasap.co?igsh=MWtkanlibnhwYzdyag%3D%3D&utm_source=qr"><i class="uil uil-instagram"></i></a>
                                    <a href="#"><i class="uil uil-youtube"></i></a>
                                </nav>
                                <!-- /.social -->


                            </div>
                            <!-- /.widget -->
                        </div>
                        <!-- /column -->
                        <div class="col-md-4 col-lg-3">
                            <div class="widget">
                                <h4 class="widget-title  mb-3">Get in Touch</h4>
                                <address class="pe-xl-15 pe-xxl-17">Moonshine St. 14/05 Light City, London, United
                                    Kingdom</address>
                                <a href="" class="link-body">support@verifyasap.com</a>
                            </div>
                            <!-- /.widget -->
                        </div>
                        <!-- /column -->
                        <div class="col-md-4 col-lg-3">
                            <div class="widget">
                                <h4 class="widget-title  mb-3">Learn More</h4>
                                <ul class="list-unstyled text-reset mb-0">
                                    <li><a href="about-us">About Us</a></li>
                                       <li><a href="policy">Policies</a></li>


                                </ul>
                            </div>
                            <!-- /.widget -->
                        </div>
                        <!-- /column -->
                        <div class="col-md-12 col-lg-3">
                            <div class="widget">
                                <h4 class="widget-title  mb-3">Our Newsletter</h4>
                                <p class="mb-5">Subscribe to our newsletter to get our news & deals delivered to you.
                                </p>
                                <div class="newsletter-wrapper">
                                    <!-- Begin Mailchimp Signup Form -->
                                    <div id="mc_embed_signup2">
                                        <form action="https://elemisfreebies.us20.list-manage.com/subscribe/post?u=aa4947f70a475ce162057838d&amp;id=b49ef47a9a" method="post" id="mc-embedded-subscribe-form2" name="mc-embedded-subscribe-form" class="validate " target="_blank" novalidate="">
                                            <div id="mc_embed_signup_scroll2">
                                                <div class="mc-field-group input-group form-floating">
                                                    <input type="email" value="" name="EMAIL" class="required email form-control" placeholder="Email Address" id="mce-EMAIL2">
                                                    <label for="mce-EMAIL2">Email Address</label>
                                                    <input type="submit" value="Join" name="subscribe" id="mc-embedded-subscribe2" class="btn btn-primary ">
                                                </div>
                                                <div id="mce-responses2" class="clear">
                                                    <div class="response" id="mce-error-response2" style="display:none">
                                                    </div>
                                                    <div class="response" id="mce-success-response2" style="display:none"></div>
                                                </div>
                                                <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                                                <div style="position: absolute; left: -5000px;" aria-hidden="true">
                                                    <input type="text" name="b_ddc180777a163e0f9f66ee014_4b1bcfa0bc" tabindex="-1" value="">
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                        </form>
                                    </div>
                                    <!--End mc_embed_signup-->
                                </div>
                                <!-- /.newsletter-wrapper -->
                            </div>
                            <!-- /.widget -->
                        </div>
                        <!-- /column -->
                    </div>
                    <!--/.row -->
                </div>
                <!-- /.container -->
            </footer>
            <div class="progress-wrap">
                <svg class="progress-circle svg-content" width="100%" height="100%" viewbox="-1 -1 102 102">
                    <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"></path>
                </svg>
            </div>
            <script data-cfasync="false" src="cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
            <script src="{{url('')}}/public/assets/js/plugins.js"></script>
            <script src="{{url('')}}/public/assets/js/theme.js"></script>
        </body>

        </html>
        <!-- Livewire Component wire-end:KYyr7BXHmAukicbrQqma -->
    </div>
    </div>
    <script src="/livewire/livewire.js?id=90730a3b0e7144480175" data-turbo-eval="false" data-turbolinks-eval="false">
    </script>
    <script data-turbo-eval="false" data-turbolinks-eval="false">
        window.livewire = new Livewire();
        window.Livewire = window.livewire;
        window.livewire_app_url = '';
        window.livewire_token = 'JBt4aOzGju0YuBweWShPMRkAkmVxvzZzG4XOMx7V';
        window.deferLoadingAlpine = function(callback) {
            window.addEventListener('livewire:load', function() {
                callback();
            });
        };
        let started = false;
        window.addEventListener('alpine:initializing', function() {
            if (!started) {
                window.livewire.start();
                started = true;
            }
        });
        document.addEventListener("DOMContentLoaded", function() {
            if (!started) {
                window.livewire.start();
                started = true;
            }
        });

    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.getElementById('data-table');
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const countdownElement = row.cells[2]; // Assumes "Expires" is in the third column (index 2)
                let seconds = parseInt(countdownElement.getAttribute('data-seconds'), 10);

                const countdownInterval = setInterval(function() {
                    countdownElement.textContent = seconds + 's';

                    if (seconds <= 0) {
                        clearInterval(countdownInterval);
                        // Add your logic to handle the expiration, e.g., sendPostRequest(row);
                        console.log('Expired:', row);
                    }

                    seconds--;
                }, 1000);
            });

            // You may add the sendPostRequest function here or modify the code accordingly
        });

    </script>

    <script>
        $(document).ready(function() {
            //change selectboxes to selectize mode to be searchable
            $("select").select2();
        });

    </script>


    <style>
        .float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 40px;
            right: 40px;
            background-color: #000000;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 3px #999;
            z-index: 100;
        }

        .my-float {
            margin-top: 16px;
        }

    </style>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <a href="https://t.me/verifyasapp" class="float" target="_blank">
        <i class="fa fa-comment my-float"></i>
    </a>






</body>

</html>
