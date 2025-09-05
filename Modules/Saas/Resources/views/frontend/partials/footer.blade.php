<footer>
    <div class="container">
        <div class="footer-middle">
            <div class="row gy-5">
                <div class="col-xl-5 col-sm-8">
                    <div class="footer-logo">
                        <img class="full-logo light_logo " src="{{ logo_dark(@base_settings('company_logo_frontend')) }}" alt="white">
                        <p class="my-3">
                            {{ base_settings('company_description') }}
                        </p>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-4">
                    <ul class="footer-list">
                        <li>
                            <div class="footer-title">
                                <h6>{{ _trans('frontend.Useful Links') }}</h6>
                            </div>
                        </li>
                        @foreach($contents as $key => $value)
                            <li class="footer-list-item">
                                <a href="{{ route('saascontent', $value->slug)  }}">{{ $value->title }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-xl-5 col-sm-12">
                    <ul class="footer-list float-end float-sm-start">
                        <li>
                            <div class="footer-title">
                                <h6>{{ _trans('frontend.Subscribe to Newsletter') }}</h6>
                            </div>
                        </li>
                        <li class="footer-list-item">
                            <form action="{{ route('saas.storeSubscriber') }}" method="POST" class="footer-mail-section flex-wrap">
                                @csrf
                                <input class="footer-mailbox" type="email" name="email" placeholder="{{ _trans('frontend.Enter your email') }}" required>
                                <button class="subscribe-btn">{{ _trans('frontend.Subscribe') }}</button>
                            </form>
                        </li>
                        <li class="footer-list-item">
                            <ul class="social-link flex-wrap">
                                @if (base_settings('twitter_link'))
                                    <li class="social-link-item">
                                        <a href="{{ base_settings('twitter_link') }}">
                                            <div class="social-link-box"><i class="fa-brands fa-twitter"></i></div>
                                        </a>
                                    </li>
                                @endif
                                
                                @if (base_settings('linkedin_link'))
                                    <li class="social-link-item">
                                        <a href="{{ base_settings('linkedin_link') }}">
                                            <div class="social-link-box"><i class="fa-brands fa-linkedin"></i></div>
                                        </a>
                                    </li>
                                @endif
                                
                                @if (base_settings('facebook_link'))
                                    <li class="social-link-item">
                                        <a href="{{ base_settings('facebook_link') }}">
                                            <div class="social-link-box"><i class="fa-brands fa-facebook-f"></i></div>
                                        </a>
                                    </li>
                                @endif
                                
                                @if (base_settings('instagram_link'))
                                    <li class="social-link-item">
                                        <a href="{{ base_settings('instagram_link') }}">
                                            <div class="social-link-box"><i class="fa-brands fa-instagram"></i></div>
                                        </a>
                                    </li>
                                @endif
                                
                                @if (base_settings('dribbble_link'))
                                    <li class="social-link-item">
                                        <a href="{{ base_settings('dribbble_link') }}">
                                            <div class="social-link-box"><i class="fa-brands fa-dribbble"></i></div>
                                        </a>
                                    </li>
                                @endif
                                
                                @if (base_settings('behance_link'))
                                    <li class="social-link-item">
                                        <a href="{{ base_settings('behance_link') }}">
                                            <div class="social-link-box"><i class="fa-brands fa-behance"></i></div>
                                        </a>
                                    </li>
                                @endif
                                
                                @if (base_settings('pinterest_link'))
                                    <li class="social-link-item">
                                        <a href="{{ base_settings('pinterest_link') }}">
                                            <div class="social-link-box"><i class="fa-brands fa-pinterest-p"></i></div>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="copy-right">
                            <p> &copy; {{ date('Y') }} <span>{{ @base_settings('company_name') }}</span> {{ _trans('frontend.All right Reserved') }}</p>
                        </div>
                        <div class="payment-image">
                            <img src="{{ global_asset('vendor/Saas/Assets/images/ssl.png') }}" alt="img">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>