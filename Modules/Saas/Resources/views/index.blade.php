@extends('saas::frontend.layouts.master')
@section('title', @$data['title'])
@section('saas_menu')
<div class="crm-header">
  @include('saas::partials.basic_saas_menu')
  @include('saas::partials.sass_extended_menu')
</div>
@endsection
@section('content')
    <div class="new-main-content">
        {{-- top section --}}
        <div class="bg-gradient-frontend">
            <div class="container">
                <div class="row mt-5 py-5 align-items-center mx-text-md-center">
                    <div class="col-lg-6">
                        <div class="side-img">
                            <img src="{{ url('images/banner-1-1.svg') }}" alt="">
                        </div>

                    </div>
                    <div class="offset-lg-1 col-lg-5 mx-mt-md">
                        <div class="side-content">
                            <h2>Welcome To {{ env('APP_NAME') }}</h2> 
                            <p>An intuitive HR Technology to keep track of employee attendance reporting, stress-free
                                payroll services, employee development to performance reviews, payroll_items, compliance and
                                more
                            </p>
                            <div
                                class="button-for-app d-flex flex-column flex-sm-row flex-lg-row flex-xl-row flex-md-row justify-content-center justify-content-lg-start justify-content-xl-start front-page-store-btn">
                                <a href="{{ config('settings.app')['android_url'] }}" class="app-button-wrapper">Google
                                    Play</a>
                                <a href="{{ config('settings.app')['ios_url'] }}" class="app-button-wrapper">Apple
                                    Store</a>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
        {{-- end --}}


        {{-- middle-section --}}
        <div class="bg-lightt">
            <div class="container">

                <div class="row py-5 align-items-center mx-text-md-center">
                    <div class="col-lg-5 mx-mb-md">
                        <div class="side-img">
                            <img src="{{ url('images/banner-2-1.svg') }}" alt="">
                        </div>
                    </div>
                    <div class="col-lg-6 offset-lg-1">
                        <div class="side-content">
                            <h2>Evolve Into A More Robust Company</h2>
                            <p>Imagine being busy, always stressed, and using the old-fashioned way to validate gross hours
                                of each employee, and then generating an export file to transmit the file by importing it
                                into payroll system and later spending more productive hours to figure out what went wrong.
                                You must put an end to it from today!</p>
                            <p>
                                Get yourself introduced with this single system for
                                the time and pay to save time eliminate errors, improved accuracy and take your smaller to
                                larger organizations journey to easy, accurate and stress-free payroll services.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- end --}}


        {{--  --}}


        {{-- middle-section --}}
        <div class="bg-gradient-frontend">
            <div class="container">

                <div class="row py-5 align-items-center mx-text-md-center">
                    <div class="col-lg-6 mx-md-order-2">
                        <div class="side-content">
                            <h2>Manage People And Related Processes</h2>
                            <p>You can easily manage a wide-range of people related activities all in one place, increasing
                                efficiency, and most importantly, enabling you to better understand your workforce and make
                                more informed decisions around it and the end result is happier, more engaged employees who
                                ultimately perform better for your business.</p>

                        </div>
                    </div>
                    <div class="offset-lg-1 col-lg-5 mx-mb-md mx-md-order-1">

                        <div class="side-img">
                            <img src="{{ url('images/banner-3-1.svg') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- end --}}


        {{-- middle-section --}}
        <div class="opposite-bg-gradient-frontend">
            <div class="container">

                <div class="row py-5 align-items-center mx-text-md-center">
                    <div class="col-lg-5 mx-mb-md">
                        <div class="side-img">
                            <img src="{{ url('images/SuccessionPlanning.png') }}" alt="">
                        </div>
                    </div>
                    <div class="col-lg-6 offset-lg-1">
                        <div class="side-content">
                            <h2>Succession Planning</h2>
                            <p>This is very much crucial to develop people in your company the way you desire to so that
                                they can carry the same mind-set to lead the company in future. To make it work for real and
                                make it convenient for the both management people’s and employees’ better work efficiency,
                                this HRMS management system can become a great intercession.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- end --}}

        {{--  --}}
        @include('saas::includes.upcoming_feature')

        {{-- Slider Section --}}
        <div class="bg-lightt">
            <div class="container">
                <div class="row py-5">
                    <div class="col-lg-12 mb-5">
                        <div class="title-middle-section-img">
                            <h3 class="text-center">Companies Using</h3>
                            <h2 class="text-center">The Best HR Software in the World </h2>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="clients-slider">
                            <div class="d-flex justify-content-between trusted-company-slider">
                                <div class="client-img">
                                    <a href="">
                                        <img src="{{ url('images/Easytrax-Limited-Logo.png') }}" alt="">
                                    </a>
                                </div>
                                <div class="client-img">
                                    <a href="">
                                        <img src="{{ url('images/One-Ummah-BD-new.png') }}" alt="">
                                    </a>
                                </div>
                                <div class="client-img">
                                    <a href="">
                                        <img src="{{ url('images/SS-International-Travels.jpeg') }}"
                                            alt="">
                                    </a>
                                </div>
                                <div class="client-img">
                                    <a href="">
                                        <img src="{{ url('images/Step-Footware.jpeg') }}" alt="">
                                    </a>
                                </div>
                                <div class="client-img">
                                    <a href="">
                                        <img src="{{ url('images/Woodhouse-Grill.png') }}" alt="">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- end --}}


        {{-- slider --}}
        <div class="bg-gradient-frontend">
            <div class="container">
                <div class="row py-5">
                    <div class="slick-client">
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="slider-img">
                                        <img src="{{ url('images/Easytrax-Limited-Logo.png') }}" alt="">
                                    </div>
                                    <div class="card-text mb-5">
                                        <p class="descript"> Never felt this much relaxed in last couple of years It’s quiet
                                            comprehensible and helped me manage things very easily. A great software indeed!
                                        </p>
                                    </div>
                                    <div class="card-text mb-3">
                                        <p class="designationz">Rashed</p>
                                        <p>Director, Easytrax GPS Tracking </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="slider-img">
                                        <img src="{{ url('images/Woodhouse-Grill.png') }}" alt="">

                                    </div>
                                            ever.</p>
                                    </div>
                                    <div class="card-text mb-3">
                                        <p class="designationz">Rashed</p>
                                        <p>Director, Woodhouse-Grill </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="slider-img">
                                        <img src="{{ url('images/Step-Footware.jpeg') }}" alt="">

                                    </div>
                                    <div class="card-text mb-5">
                                        <p class="descript">Thanks to 24hourworx for this amazing software. Now I can
                                            easily manage my employee’s payrolls, track hours and many other cool stuff.
                                        </p>
                                    </div>
                                    <div class="card-text mb-3">
                                        <p class="designationz">Rashed</p>
                                        <p>Director, Step Footwear</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="slider-img">

                                        <img src="{{ url('images/Step-Footware.jpeg') }}" alt="">

                                    </div>
                                    <div class="card-text mb-5">
                                        <p class="descript">Majorly it has improved the psychosocial environment for us.
                                            This is a smooth software that has covered all significant matters and also very
                                            user friendly.</p>
                                    </div>
                                    <div class="card-text mb-3">
                                        <p class="designationz">Rashed</p>
                                        <p>Director, Easytrax GPS Tracking </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        {{-- end --}}

    </div>

@endsection
