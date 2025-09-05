<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" id="base-url" content="{{ url('/') }}">
    <!-- currently using it -->
    <title>@yield('title') | {{ @base_settings('meta_title') }}</title>
    <!-- Theme style -->

    <meta name="keywords" content="{{ @base_settings('meta_keywords') }}">
    <meta name="description" content="{{ @base_settings('meta_description') }}">

    <meta property="og:title" content="{{ @base_settings('meta_title') }}">
    <meta property="og:description" content="{{ @base_settings('meta_description') }}">
    <meta property="og:image" content="{{ metaImage(@base_settings('meta_image')) }}">
    <meta property="og:url" content="{{ url('/') }}">

    <link rel="stylesheet" href="{{ global_asset('vendor/Saas/Assets/css/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ global_asset('vendor/Saas/Assets/css/slick.min.css') }}">
    <link rel="stylesheet" href="{{ global_asset('vendor/Saas/Assets/css/slick-theme.min.css') }}">
    <link rel="stylesheet" href="{{ global_asset('vendor/Saas/Assets/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ global_asset('vendor/Saas/Assets/css/select2.css') }}">
    <link rel="stylesheet" href="{{ global_asset('vendor/Saas/Assets/css/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ global_asset('vendor/Saas/Assets/css/frontend.css') }}">
    <link rel="stylesheet" href="{{ global_asset('vendor/Saas/Assets/css/saas.css') }}">
    <link rel="stylesheet" href="{{ global_asset('css/toastr.css') }}">
    <link rel="stylesheet" href="{{ global_asset('vendor/Saas/Assets/css/custom.css') }}">
    <link rel="icon" type="image/x-icon" href="{{ company_fav_icon(@base_settings('company_icon')) }}">

    <style>
        #contact-whatsApp {
            bottom: 123px;
            position: fixed;
            right: 12px;
        }

        #contact-whatsApp img {
            max-width: 80px;
        }
    </style>
    @yield('styles')
    @stack('styles')
</head>

<body class="default-theme">


    <!-- start menu -->
    @include('saas::frontend.partials.menu')
    <!-- end menu -->


    <!-- strat breadcrumbs -->
    @include('saas::frontend.partials.breadcrumbs')
    <!-- end breadcrumbs -->



    @yield('content')


    <!-- strat footer -->
    @include('saas::frontend.partials.footer')
    <!-- end footer -->
    


    @if (isMainCompany() && isWhatsAppChatEnable())
        <a href="https://api.whatsapp.com/send?phone={{ whatsAppChatNumber() }}" target="_blank" id="contact-whatsApp">
            <img src="{{ global_asset('vendor/Saas/Assets/images/whatsApp.png') }}" alt="">
        </a>
    @endif




    <script src="{{  global_asset('vendor/Saas/Assets/js/jquery.min.js') }}"></script>
    <script src="{{  global_asset('vendor/Saas/Assets/js/bootstrap.min.js') }}"></script>
    <script src="{{  global_asset('vendor/Saas/Assets/js/slick.min.js') }}"></script>
    <script src="{{  global_asset('vendor/Saas/Assets/js/__header.js') }}"></script>
    <script src="{{  global_asset('vendor/Saas/Assets/js/__accordion.js') }}"></script>
    <script src="{{  global_asset('vendor/Saas/Assets/js/__scrollUp.js') }}"></script>
    <script src="{{  global_asset('vendor/Saas/Assets/js/__sideMenuLang.js') }}"></script>
    <script src="{{  global_asset('vendor/Saas/Assets/js/__mobileViewNavMenu.js') }}"></script>
    <script src="{{  global_asset('vendor/Saas/Assets/js/select2.js') }}"></script>
    <script src="{{  global_asset('vendor/Saas/Assets/js/niceScroll.js') }}"></script>
    <script src="{{  global_asset('vendor/Saas/Assets/js/slider.js') }}"></script>
    <script src="{{ global_asset('js/toastr.js') }}"></script>
    <script src="{{  global_asset('vendor/Saas/Assets/js/custom.js') }}"></script>
    
    {!! Toastr::message() !!}

    @yield('scripts')
    @stack('scripts')

    @if (isMainCompany() && isTawkEnable())
        {!! tawkChatWidgetScript() !!}
    @endif
</body>
</html>
