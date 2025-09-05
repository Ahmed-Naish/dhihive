@extends('saas::frontend.layouts.master')

@section('title', @$data['title'])

@section('content')
    <section class="ot-courses-area section-padding">
        <div class="container">
            <div class="gray-bg contact-padding radius-8">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="col-12">
                            <div class="without-bg-page-title">
                                <h3 class="text-left">{{ base_settings('contact_title') }}</h3>
                            </div>
                            <div class="without-bg-page-content">
                                <p class="text-left">{{ base_settings('contact_short_description') }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-8 col-lg-6">
                                <div class="d-flex align-items-center p-3  white-bg mb-32 radius-4">
                                    <div class="contact-icon radius-4 badge-secondary-soft mr-24">
                                        <i class="fa-solid fa-envelope"></i>
                                    </div>
                                    <div class="contact-info ">
                                        <h5 class="text-18 font-600 mb-6">{{ _trans('frontend.Email Address') }}</h5>
                                        <p class="text-16 font-400 m-0">{{ base_settings('email') }}</p>
                                    </div>
                                </div>
                                <div class="d-flex p-3 white-bg  mb-32 radius-4">
                                    <div class="contact-icon radius-4 badge-secondary-soft mr-24">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>
                                    <div class="contact-info ">
                                        <p class="text-16 font-400 m-0">{{ base_settings('address') }}</p>
                                    </div>
                                </div>
                                <div class="d-flex p-3 white-bg  mb-32 radius-4">
                                    <div class="contact-icon radius-4 badge-secondary-soft mr-24">
                                        <i class="fa-solid fa-phone"></i>
                                    </div>
                                    <div class="contact-info ">
                                        <h5 class="text-18 font-600 mb-6">{{ _trans('frontend.Phone Number') }}</h5>
                                        <p class="text-16 font-400 m-0">{{ base_settings('phone') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="from-wraps white-bg p-5 radius-6">
                            <form action="{{ route('saas.storeContact') }}" method="POST">
                                @csrf
                                <div class="position-relative ot-contact-form mb-24">
                                    <label class="ot-contact-label">{{ _trans('frontend.Subject') }} <span class="text-danger">*</span></label>
                                    <input class="form-control ot-contact-input" type="text" name="subject" placeholder="{{ _trans('frontend.Enter Subject') }}" required>
                                </div>
                                <div class="position-relative ot-contact-form mb-24">
                                    <label class="ot-contact-label">{{ _trans('frontend.Name') }} <span class="text-danger">*</span></label>
                                    <input class="form-control ot-contact-input" type="text" name="name" placeholder="{{ _trans('frontend.Enter Name') }}" required>
                                </div>
                                <div class="position-relative ot-contact-form mb-24">
                                    <label class="ot-contact-label">{{ _trans('frontend.Email') }} <span class="text-danger">*</span></label>
                                    <input class="form-control ot-contact-input" type="email" name="email" placeholder="{{ _trans('frontend.Enter Email') }}" required>
                                </div>
                                <div class="position-relative ot-contact-form mb-24">
                                    <label class="ot-contact-label">{{ _trans('frontend.Message') }} <span class="text-danger">*</span></label>
                                    <textarea class="ot-contact-textarea" cols="3" rows="3" name="message" placeholder="{{ _trans('frontend.Enter Message') }}" required></textarea>
                                </div>
                                <div class="check-wrap style-seven mb-10 d-flex gap-10">
                                    <input id="1" type="checkbox" name="is_subscribe" value="1"/>
                                    <label for="1"></label>
                                    <p>{{ _trans('frontend.I\'d like to occasionally receive other communication from Stellar, such as contact and product news') }}</p>
                                </div>
                                <button class="primary_btn">{{ _trans('frontend.Submit') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection