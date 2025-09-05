<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- currently using it -->
    <title>{{ env('APP_NAME') }} | @yield('title')</title>
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ Module::asset('Saas:css/bootstrap/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{ Module::asset('Saas:css/animate.min.css')}}">
    <link rel="stylesheet" href="{{ Module::asset('Saas:css/fontawesome/css/all.min.css')}}">
    <link rel="stylesheet" href="{{ Module::asset('Saas:css/frontend.css')}}">
    <link rel="stylesheet" href="{{ Module::asset('Saas:css/saas.css') }}">
    <link rel="icon" type="image/x-icon" href="{{ Module::asset('Saas:images/favicon.png') }}">
</head>

<body class="default-theme">
