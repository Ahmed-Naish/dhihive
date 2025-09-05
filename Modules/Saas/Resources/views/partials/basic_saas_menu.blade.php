<div class="header-shape"></div>
<div class="landing_header">
    <nav class="navbar navbar-expand-lg navbar-light bg-light bg-transparent">
        <div class="container">
            <a class="logo" href="#">
                <img class="full-logo  light_logo " src="{{ global_asset('assets/images/white.png') }}" alt="white">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mx-auto">
                    <li class=" nav-item ocrm-item active">
                        <a class="nav-link" href="{{ route('saas.homePage') }}">Home</a>
                    </li>
                    <li class=" nav-item ocrm-item">
                        <a class="nav-link" href="{{ route('saas.pricingPage') }}">Pricing</a>
                    </li>
                    <li class=" nav-item ocrm-item">
                        <a class="nav-link" href="{{ route('saas.solutions') }}">Solutions</a>
                    </li>
                    <li class=" nav-item ocrm-item">
                        <a class="nav-link" href="{{ route('saas.contact') }}">Contact Us</a>
                    </li>
                </ul>
                <div class="responsive-collapse-btn">
                    <a class="primary_btn" href="{{ url('login') }}">Login</a>
                </div>
            </div>
        </div>
    </nav>
</div>
@include('saas::partials.breadcumbs')
