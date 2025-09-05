<!-- Top Navbar -->
<div class="ot-top-navbar">
    <div class="container">
        <div class="d-flex justify-content-between">
            <div class="top-nav-item">
                <ul class="top-navbar-nav mx-auto">
                    <!-- Phone number -->
                    <li>
                        <a href="tel:080-46971075">
                            <i class="fa-solid fa-phone"></i>
                            <span>{{ base_settings('phone') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="mailto:sales@onesttech.com">
                            <i class="fa-solid fa-envelope"></i>
                            <span>{{ base_settings('email') }}</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="top-nav-item">
                <ul class="top-navbar-nav mx-auto">
                    <li>
                        <a href="{{ url('sign-in') }}">
                            {{ auth()->check() ? _trans('frontend.Dashboard') : _trans('frontend.Log In') }}
                        </a>
                    </li>
                    <li>
                        <x-frontend-language-dropdown />
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>



<!-- Landing Header -->
<div class="landing_header">
    <nav class="navbar ot-navbar navbar-expand-lg navbar-light bg-light bg-transparent">
        <div class="container">
            <!-- Logo -->
            <a class="logo" href="{{ url('/') }}">
                <img class="full-logo light_logo saas-logo" src="{{ logo_dark(@base_settings('company_logo_frontend')) }}" alt="white">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left navigation items -->
                <ul class="navbar-nav left-nav mx-auto">
                    <li class="nav-item ocrm-item active">
                        <a class="nav-link" href="{{ route('saas.homePage') }}">{{ _trans('frontend.Home') }}</a>
                    </li>
                    <li class="nav-item ocrm-item">
                        <a class="nav-link" href="{{ route('saas.featurePage') }}">{{ _trans('frontend.Features') }}</a>
                    </li>
                    <li class="nav-item ocrm-item">
                        <a class="nav-link" href="{{ route('saas.pricingPage') }}">{{ _trans('frontend.Pricing') }}</a>
                    </li>
                    <li class="nav-item ocrm-item">
                        <a class="nav-link" href="{{ route('saas.contactPage') }}">{{ _trans('frontend.Contact Us') }}</a>
                    </li>
                </ul>
                <div class="mb-3 d-md-none">
                    <x-frontend-language-dropdown />
                </div>
                <!-- Responsive collapse button -->
                <div class="responsive-collapse-btn">
                    <a class="primary_btn" href="{{ url('pricing') }}">{{ _trans('frontend.Get Started') }}</a>
                </div>
                <div class="responsive-collapse-btn ot-responsive-login mt-2">
                    <a class="primary_btn" href="{{ url('sign-in') }}">{{ _trans('frontend.Login') }}</a>
                </div>
            </div>
        </div>
    </nav>
</div>
