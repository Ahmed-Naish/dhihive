<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="base-url" content="{{ url('/') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('settings.app.company_name') }} - @yield('title')</title>


    <link rel="manifest" href="{{ url('assets/fav.ico') }}/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ url('assets/fav.ico') }}/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    {{-- <link rel="icon" href="" type="image/x-icon" /> --}}
    @if (env('FILESYSTEM_DRIVER') == 'server')
        <link rel="shortcut icon" href="{{ Storage::disk('s3')->url(config('settings.app')['company_icon']) }}"
            type="image/x-icon" />
    @elseif (env('FILESYSTEM_DRIVER') == 's3')
        <link rel="shortcut icon" href="{{ Storage::disk('s3')->url(config('settings.app')['company_icon']) }}"
            type="image/x-icon" />
    @elseif(env('FILESYSTEM_DRIVER') == 'local')
        <link rel="shortcut icon" href="{{ Storage::url(config('settings.app')['company_icon']) }}"
            type="image/x-icon" />
    @else
        <link rel="shortcut icon" href="{{ Storage::url(config('settings.app')['company_icon']) }}"
            type="image/x-icon" />
    @endif

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ global_asset('backend/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ global_asset('backend/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ global_asset('backend/css/adminlte.min.css') }}">
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="{{ global_asset('frontend/css/style.css') }}">
    <link rel="stylesheet" href="{{ global_asset('frontend/css/custom.css') }}">
    <link rel="stylesheet" href="{{ global_asset('frontend/css/frontend.css') }}">
    <link rel="stylesheet" href="{{ global_asset('css/main.css') }}">
    {{-- iziToast cdn --}}
    <link rel="stylesheet" href="{{ global_asset('frontend/assets/css/iziToast.css') }}">

    <link rel="stylesheet" href="{{ global_asset('/') }}public/css/bootstrap-datetimepicker.min.css">


    <!-- Global site tag (gtag.js) - Google Analytics - GA4 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-XLCE04HTY2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-XLCE04HTY2');
    </script>

    <!-- Global site tag (gtag.js) - Google Analytics - Universal -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-224313698-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-224313698-1');
    </script>

    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-PFGX7Q4');
    </script>
    <!-- End Google Tag Manager -->

    {{-- from adgari --}}

    <link rel="stylesheet" href="{{ global_asset('/') }}public/frontend/css/font-icons.css">
    <link rel="stylesheet" type="text/css" href="{{ global_asset('/') }}public/font/flaticon.css">


    <link rel="stylesheet" href="{{ global_asset('/') }}public/frontend/assets/bootstrap/bootstrap.min.css">

    <link rel="stylesheet" href="{{ global_asset('/') }}public/frontend/assets/animate.min.css">
    <!-- plugins css -->
    <link rel="stylesheet" href="{{ global_asset('/') }}public/frontend/css/plugins.css">

    <!-- Main Stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


    <link rel="stylesheet" href="{{ global_asset('/') }}public/frontend/assets/slick-theme.min.css">
    <link rel="stylesheet" href="{{ global_asset('/') }}public/frontend/assets/slick.min.css">

    <link rel="stylesheet" href="{{ global_asset('/') }}public/frontend/css/style.css">
    <link rel="stylesheet" href="{{ global_asset('/') }}public/css/main.css">
    <link rel="stylesheet" href="{{ global_asset('frontend/css/frontend.css') }}">
    <link rel="stylesheet" href="{{ global_asset('frontend/css/newfrontend.css') }}">

    <link rel="stylesheet" href="{{ global_asset('css/toastr.css') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.maateen.me/solaiman-lipi/font.css" rel="stylesheet">


    <link rel="stylesheet" type="text/css" href="{{ global_asset('/') }}public/fonts/icofont/icofont.css">

    <!-- Responsive css -->
    <link rel="stylesheet" href="{{ global_asset('/') }}public/frontend/css/responsive.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.6.1/font/bootstrap-icons.min.css" />


    <link href="{{ global_asset('/') }}public/backend/css/select2.min.css" rel="stylesheet" />



    <link rel="stylesheet" href="{{ global_asset('/') }}public/frontend/assets/css/iziToast.css">
    <link rel="stylesheet" href="{{ global_asset('/') }}public/frontend/assets/css/kobir.css">

    <input type="text" hidden id="url" value="{{ url('/') }}">



    <link rel="stylesheet" href="{{ global_asset('/') }}public/css/bootstrap-datetimepicker.min.css">


    {{-- movie to  --}}
    <link rel="stylesheet" href="{{ global_asset('/') }}public/frontend/assets/css/header.css">


    @yield('style')
</head>

<body class="hold-transition ">
    @include('frontend.includes.menu')
