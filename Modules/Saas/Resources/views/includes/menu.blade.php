<div class="login-dashboard-header responsive-homepage-menubar list-style-none">
    <div class="container">

        <div class="navigation-wrap bg-light start-header start-style">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <nav class="navbar navbar-expand-md navbar-light">
                            @include('backend.auth.backend_logo')

                            <button class="navbar-toggler navbar-toggler-custom collapsed" type="button" data-toggle="collapse"
                                data-target="#navbar" aria-controls="navbarSupportedContent" aria-expanded="false"
                                aria-controls="navbar">
                                <span class="navbar-toggler-icon"></span>
                            </button>

                            <div class="collapse navbar-collapse" id="navbar">
                                <ul class="navbar-nav navbar-nav-list ml-auto py-4 py-md-0 align-items-center">

                                    @if (auth()->check())
                                        <li class="nav-item pl-4 pl-md-0 ml-0 ml-md-4">
                                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#"
                                                role="button" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false" v-pre>
                                                {{ auth()->user()->name }} <span class="caret"></span>
                                            </a>

                                            <div class="dropdown-menu dropdown-menu-right"
                                                aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                                 document.getElementById('logout-form').submit();">
                                                    {{ __('Logout') }}
                                                </a>

                                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                </form>
                                            </div>
                                        </li>
                                        <li>
                                            <li class="loginbtn pl-4 pl-md-0 ml-0 ml-md-4">
                                                <a class="login-panel-btn gradient-btn" href="{{ route('admin.dashboard') }}">Dashboard</a>
                                            </li>
                                        </li>
                                   
                                    @endif
                                    {{-- enable demo button in landing page --}}
                                    @if(config('app.style') === 'demo')
                                        <li class="loginbtn pl-4 pl-md-0 ml-0 ml-md-4">
                                            <a class="login-panel-btn gradient-red-btn" href="{{ url('/landing-page') }}">Demo</a>
                                        </li>
                                    @endif

                                </ul>
                            </div>

                        </nav>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>

