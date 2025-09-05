@extends('saas::frontend.layouts.master')

@section('title', @$data['title'])

@section('styles')
    <style>
        .radio-inputs {
            display: flex;
            align-items: center;
            gap: 10px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .radio-input:checked+.radio-tile {
            border-color: #2260ff;
            color: #2260ff;
        }

        .radio-input:checked+.radio-tile:before {
            transform: scale(1);
            opacity: 1;
            background-color: #2260ff;
            border-color: #2260ff;
        }

        .radio-icon {
            width: 25px
        }

        .radio-input:checked+.radio-tile .radio-icon svg {
            fill: #2260ff;
        }

        .radio-input:checked+.radio-tile .radio-label {
            color: #2260ff;
        }

        .radio-input:focus+.radio-tile {
            border-color: #2260ff;
        }

        .radio-input:focus+.radio-tile:before {
            transform: scale(1);
            opacity: 1;
        }

        .radio-tile {
            display: flex;
            gap: 10px;
            align-items: center;
            padding: 15px 20px;
            justify-content: center;
            border-radius: 0.5rem;
            border: 2px solid #b5bfd9;
            background-color: #fff;
            transition: 0.15s ease;
            cursor: pointer;
            position: relative;
        }

        .radio-tile:before {
            content: "";
            position: absolute;
            display: block;
            width: 0.75rem;
            height: 0.75rem;
            border: 2px solid #b5bfd9;
            background-color: #fff;
            border-radius: 50%;
            top: 0.25rem;
            left: 0.25rem;
            opacity: 0;
            transform: scale(0);
            transition: 0.25s ease;
        }

        .radio-tile:hover {
            border-color: #2260ff;
        }

        .radio-tile:hover:before {
            transform: scale(1);
            opacity: 1;
        }

        .radio-label {
            color: #707070;
            transition: 0.375s ease;
            text-align: center;
            font-size: 16px;
        }

        .radio-input {
            clip: rect(0 0 0 0);
            -webkit-clip-path: inset(100%);
            clip-path: inset(100%);
            height: 1px;
            overflow: hidden;
            position: absolute;
            white-space: nowrap;
            width: 1px;
        }

        .stripe-wrapper {
            padding: 0px 15px 18px !important;
            background: #1e1c9711
        }
    </style>
@endsection
 

@section('content')
    <div class="checkout-area section-padding">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 col-lg-12">
                    <div class="ot-card box-shadow border-0 p-0">
                        <form action="{{ route('saas.checkout') }}" method="POST" id="checkout-form">
                            @csrf

                            @php
                                $startDate  = \Carbon\Carbon::now()->format('M d, Y');

                                if (\Modules\Saas\Enums\PricingPlanDurationType::MONTHLY == @$data['pricingPlanPrice']->planDurationType->name) {
                                    $endDate = \Carbon\Carbon::now()->addMonths(1)->format('M d, Y');
                                } elseif (\Modules\Saas\Enums\PricingPlanDurationType::QUARTERLY == @$data['pricingPlanPrice']->planDurationType->name) {
                                    $endDate = \Carbon\Carbon::now()->addMonths(3)->format('M d, Y');
                                } elseif (\Modules\Saas\Enums\PricingPlanDurationType::HALF_YEARLY == @$data['pricingPlanPrice']->planDurationType->name) {
                                    $endDate = \Carbon\Carbon::now()->addMonths(6)->format('M d, Y');
                                } elseif (\Modules\Saas\Enums\PricingPlanDurationType::YEARLY == @$data['pricingPlanPrice']->planDurationType->name) {
                                    $endDate = \Carbon\Carbon::now()->addMonths(12)->format('M d, Y');
                                }
                            @endphp 

                            <input type="hidden" name="pricing_plan_price_id" value="{{ old('pricing_plan_price_id', request('pricing_plan_price_id')) }}">
                            <input type="hidden" name="expiry_date" value="{{ old('expiry_date', date('Y-m-d', strtotime($endDate))) }}">
                            
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="section-tittle-two mb-20">
                                        <h2 class="title text-capitalize font-600 mb-0">{{ _trans('frontend.Billing Details') }}</h2>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="ot-contact-form mb-24">
                                        <label class="ot-contact-label">{{ _trans('frontend.Owner Name') }} <span class="text-danger">*</span></label>
                                        <input class="form-control ot-contact-input" type="text" name="name" value="{{ old('name') }}" placeholder="{{ _trans('frontend.Owner Name') }}" required>
                                        @if ($errors->has('name'))
                                            <div class="text-danger fw-bold">{{ $errors->first('name') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="ot-contact-form mb-24">
                                        <label class="ot-contact-label">{{ _trans('frontend.Company Name') }} <span class="text-danger">*</span></label>
                                        <input onkeyup="checkCompanyName(this)" class="form-control ot-contact-input" type="text" name="company_name" value="{{ old('company_name') }}" placeholder="{{ _trans('frontend.Company Name') }}" required>
                                        <span class="text-danger fw-bold" id="companyNameError"></span>
                                        @if ($errors->has('company_name'))
                                            <div class="text-danger fw-bold error">{{ $errors->first('company_name') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="ot-contact-form mb-24">
                                        <label class="ot-contact-label">{{ _trans('frontend.Phone') }} <span class="text-danger">*</span></label>
                                        <input onkeyup="checkPhone(this)" class="form-control ot-contact-input" type="text" name="phone" value="{{ old('phone') }}" placeholder="{{ _trans('frontend.Phone') }}" required>
                                        <span class="text-danger fw-bold" id="phoneError"></span>
                                        @if ($errors->has('phone'))
                                            <div class="text-danger fw-bold error">{{ $errors->first('phone') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="ot-contact-form mb-24">
                                        <label class="ot-contact-label">{{ _trans('frontend.Email') }} <span class="text-danger">*</span></label>
                                        <input onkeyup="checkEmail(this)" class="form-control ot-contact-input" type="email" name="email" value="{{ old('email') }}" placeholder="{{ _trans('frontend.Email') }}" required>
                                        <span class="text-danger fw-bold" id="emailError"></span>
                                        @if ($errors->has('email'))
                                            <div class="text-danger fw-bold error">{{ $errors->first('email') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="ot-contact-form mb-24">
                                        <label class="ot-contact-label">{{ _trans('frontend.Trade Licence Number') }} <span class="text-danger">*</span></label>
                                        <input onkeyup="checkTradeLicenceNumber(this)" class="form-control ot-contact-input" type="text" name="trade_licence_number" value="{{ old('trade_licence_number') }}" placeholder="{{ _trans('frontend.Trade Licence Number') }}" required>
                                        <span class="text-danger fw-bold" id="tradeLicenceNumberError"></span>
                                        @if ($errors->has('trade_licence_number'))
                                            <div class="text-danger fw-bold error">{{ $errors->first('trade_licence_number') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="ot-contact-form mb-24">
                                        <label class="ot-contact-label">{{ _trans('frontend.Subdomain') }} <span class="text-danger">*</span></label>
                                        <input onkeyup="checkSubdomain(this, event)" class="form-control ot-contact-input" type="text" name="subdomain" value="{{ old('subdomain') }}" id="subdomain" placeholder="{{ _trans('frontend.Subdomain') }}" required>
                                        <span class="text-danger fw-bold" id="subdomainError"></span>
                                        @if ($errors->has('subdomain'))
                                            <div class="text-danger fw-bold error">{{ $errors->first('subdomain') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="ot-contact-form mb-24">
                                        <label class="ot-contact-label">{{ _trans('frontend.Total Employee') }} <span class="text-danger">*</span></label>
                                        <input class="form-control ot-contact-input" type="number" name="total_employee" value="{{ old('total_employee') }}" required placeholder="{{ _trans('frontend.Total Employee') }}">
                                        @if ($errors->has('total_employee'))
                                            <div class="text-danger fw-bold">{{ $errors->first('total_employee') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="ot-contact-form mb-24">
                                        <label class="ot-contact-label">{{ _trans('frontend.Business Type') }} <span class="text-danger">*</span></label>
                                        <input class="form-control ot-contact-input" type="text" name="business_type" value="{{ old('business_type') }}" placeholder="{{ _trans('frontend.Business Type') }}" required>
                                        @if ($errors->has('business_type'))
                                            <div class="text-danger fw-bold">{{ $errors->first('business_type') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <div class="ot-contact-form mb-24">
                                        <label class="ot-contact-label">{{ _trans('frontend.Country') }} <span class="text-danger">*</span></label>
                                        <select  class="select2 ot-input" name="country_id" required>
                                            <option></option>
                                            @foreach ($data['countries'] as $id => $name)
                                                <option value="{{ $id }}" {{ old('country_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('country_id'))
                                            <div class="text-danger fw-bold">{{ $errors->first('country_id') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-12 payment-gateway-wrapper">
                                <div class="p-4 bg-light rounded mb-20">
                                    <div class="radio-inputs">
                                        <label>
                                            <input class="radio-input" type="radio" name="payment_gateway" value="Stripe" checked>
                                            <span class="radio-tile">
                                                <span class="radio-icon">
                                                    <img src="{{ asset('vendor/Saas/Assets/images/payment-icon/credit-card.png') }}" alt="img">
                                                </span>
                                                <span class="radio-label">{{ _trans('frontend.Credit Card') }}</span>
                                            </span>
                                        </label>
                                        <label>
                                            <input class="radio-input" type="radio" name="payment_gateway" value="Offline Payment" {{ old('payment_gateway') == 'Offline Payment' ? 'checked' : '' }}>
                                            <span class="radio-tile">
                                                <span class="radio-icon">
                                                    <img src="{{ asset('vendor/Saas/Assets/images/payment-icon/offline-payment.png') }}" alt="img">
                                                </span>
                                                <span class="radio-label">{{ _trans('frontend.Offline Payment') }}</span>
                                            </span>
                                        </label>
                                        @if (@base_settings('is_demo_checkout'))
                                            <label>
                                                <input class="radio-input" type="radio" name="payment_gateway" value="Demo Checkout" {{ old('payment_gateway') == 'Demo Checkout' ? 'checked' : '' }}>
                                                <span class="radio-tile">
                                                    <span class="radio-icon">
                                                        <img src="{{ asset('vendor/Saas/Assets/images/payment-icon/fast-forward.png') }}" alt="img">
                                                    </span>
                                                    <span class="radio-label">{{ _trans('frontend.Demo Checkout') }}</span>
                                                </span>
                                            </label>
                                        @endif
                                    </div>
                                    <div class="rounded-3 stripe-wrapper mt-4" style="display: {{ !old('payment_gateway') == 'Stripe' ? 'block' : 'none' }}">
                                        <input type='hidden' name='stripeToken' id='stripe-token-id'>
                                        <br>
                                        <div id="card-element"></div>
                                    </div>

                                    <div class="rounded-3 offline-wrapper mt-4 d-none" style="display: {{ old('payment_gateway') == 'Offline Payment' ? 'block' : 'none' }}">
                                        <div class="row">
                                            @if (@base_settings('is_payment_type_cash') || @base_settings('is_payment_type_cheque') || @base_settings('is_payment_type_bank_transfer'))
                                            <div class="col-lg-6 mt-2">
                                                <div class="ot-contact-form mb-24">
                                                    <label class="ot-contact-label">{{ _trans('frontend.Payment Type') }} <span class="text-danger">*</span></label>

                                                    <select  class="select2 ot-input" name="offline_payment_type">
                                                        <option value="" selected></option>
                                                        @if (@base_settings('is_payment_type_cash'))
                                                            <option value="cash" {{ old('offline_payment_type') == 'cash' ? 'selected' : '' }}>{{ _trans('frontend.Cash') }}</option>
                                                        @endif
                                                        @if (@base_settings('is_payment_type_cheque'))
                                                            <option value="cheque" {{ old('offline_payment_type') == 'cheque' ? 'selected' : '' }}>{{ _trans('frontend.Cheque') }}</option>
                                                        @endif
                                                        @if (@base_settings('is_payment_type_bank_transfer'))
                                                            <option value="bank_transfer" {{ old('offline_payment_type') == 'bank_transfer' ? 'selected' : '' }}>{{ _trans('frontend.Bank Transfer') }}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="col-lg-6 mt-2">
                                                <div class="ot-contact-form mb-24">
                                                    <label class="ot-contact-label">{{ _trans('frontend.Additional Details') }} <span class="text-danger">*</span></label>
                                                    <textarea class="form-control ot-contact-textarea" id="offlinePaymentAdditionalDetails" name="offline_payment_details" placeholder="{{ _trans('frontend.e') . '.' . _trans('frontend.g') . '.' . _trans('frontend.Transaction ID: XXXX-XXXX-XXXX-XXXX') }}">{{ old('offline_payment_details') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if (isMainCompany() && isRecaptchaEnable())
                                    {{ loadRecaptcha() }}
                                    <div class="form-group mb-20">
                                        {!! NoCaptcha::renderJs() !!}
                                        {!! NoCaptcha::display() !!}
                                        @if ($errors->has('g-recaptcha-response'))
                                            <span class="text-danger fw-bold">
                                                {{ $errors->first('g-recaptcha-response') }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                <div class="col-lg-12 mb-10">
                                    <div class="check-wrap style-seven mb-10">
                                        <input id="2" type="checkbox" checked required>
                                        <label for="2">{{ _trans('frontend.I agree to all the') }} <a href="{{ route('saascontent', ['slug' => 'terms-of-use']) }}" target="_blank">{{ _trans('frontend.Terms') }}</a> {{ _trans('frontend.and') }} <a href="{{ route('saascontent', ['slug' => 'privacy-policy']) }}" target="_blank">{{ _trans('frontend.Privacy policy') }}</a></label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" onclick="createToken()" id='stripe-pay-btn' class="primary_btn w-100 mt-30 btn-color2 submit-btn">{{ _trans('common.Complete order') }}</button>
                                    <button id='demo-checkout-btn' class="d-none primary_btn w-100 mt-30 btn-color2 submit-btn">{{ _trans('common.Complete order') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-12">
                    <div class="paymentDetails ot-card box-shadow border-0">
                        @if (@$data['pricingPlanPrice']->pricingPlan->is_popular)
                            <div class="text-end">
                                <span class="badge bg-info text-dark mb-10">{{ _trans('common.Most Popular') }}</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between ">
                            <h4 class="priceTittle">
                                {{ @$data['pricingPlanPrice']->pricingPlan->name }}
                                <small class="month text-15"> / {{ @$data['pricingPlanPrice']->planDurationType->name }}</small>
                            </h4>
                            <div class="prices">
                                <h4 class="price">
                                    {{ currency_format(number_format($data['pricingPlanPrice']->price, 2)) }}
                                </h4>
                            </div>
                        </div>
                        <div class="priceListing">
                            <ul class="listing">
                                <li class="listItem">
                                    <p class="leftCap">{{ _trans('common.Start Date') }}</p>
                                    <p class="rightCap text-title font-700">{{ showDate($startDate) }}</p>
                                </li>
                                <li class="listItem">
                                    <p class="leftCap">{{ _trans('common.End Date') }}</p>
                                    <p class="rightCap text-title font-700">{{ showDate($endDate) }}</p>
                                </li>
                            </ul>
                            <div class="info text-center">
                                <p class="pera text-18 fw-bold">{{ _trans('common.Total') }} {{ currency_format(number_format($data['pricingPlanPrice']->price, 2)) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



@section('scripts')
    @if (old('payment_gateway') == 'Offline Payment')
        <script>
            $('.stripe-wrapper').hide();
            $('.offline-wrapper').removeClass('d-none');
            $('#stripe-pay-btn').addClass('d-none');
            $('#demo-checkout-btn').removeClass('d-none');
            $('#offlinePaymentAdditionalDetails').prop('required', true);
            
            setTimeout(() => {
                $('.error').addClass('d-none');
            }, 7000);
        </script>
    @endif

    @if (old('payment_gateway') == 'Demo Checkout')
        <script>
            $('.stripe-wrapper').hide();
            $('.offline-wrapper').hide();
            $('.offline-wrapper').removeClass('d-none');
            $('#stripe-pay-btn').addClass('d-none');
            $('#demo-checkout-btn').removeClass('d-none');
            
            setTimeout(() => {
                $('.error').addClass('d-none');
            }, 7000);
        </script>
    @endif

    @if (old('payment_gateway') == 'Stripe')
        <script>
            $('.offline-wrapper').hide();
            $('#stripe-pay-btn').removeClass('d-none');
            $('#demo-checkout-btn').addClass('d-none');
            
            setTimeout(() => {
                $('.error').addClass('d-none');
            }, 7000);
        </script>
    @endif


    <script src="https://js.stripe.com/v3/"></script>
    <script>
        let isCompanyNameError = false;
        let isTradeLicenceNumberError = false;
        let isPhoneError = false;
        let isEmailError = false;
        let isSubdomainError = false;


        const togglePaymentGateway = (paymentMethod) => {
            if (paymentMethod === 'Stripe') {
                $('.stripe-wrapper').show();
                $('.offline-wrapper').hide();
                $('.offline-wrapper').addClass('d-none');
                $('#stripe-pay-btn').removeClass('d-none');
                $('#demo-checkout-btn').addClass('d-none');
                $('#offlinePaymentAdditionalDetails').prop('required', false);
            } else if (paymentMethod === 'Demo Checkout') {
                $('.stripe-wrapper').show();
                $('.offline-wrapper').hide();
                 $('.stripe-wrapper').hide();
                $('.offline-wrapper').removeClass('d-none');
                $('#stripe-pay-btn').addClass('d-none');
                $('#demo-checkout-btn').removeClass('d-none');
                $('#offlinePaymentAdditionalDetails').prop('required', false);
            } else {
                $('.stripe-wrapper').hide();
                $('.offline-wrapper').show();
                $('#stripe-pay-btn').addClass('d-none');
                $('#demo-checkout-btn').removeClass('d-none');
                $('.offline-wrapper').removeClass('d-none');
                $('#offlinePaymentAdditionalDetails').prop('required', true);
            }
        }


        $(document).on('change', '.radio-input', function() {
            let paymentMethod = $(this).val();

            togglePaymentGateway(paymentMethod);
        });

    
        let stripe = Stripe(`{{ @base_settings('stripe_key') }}`)
        let elements = stripe.elements();
        let cardElement = elements.create('card');
        cardElement.mount('#card-element');


        function createToken() {
            document.getElementById("stripe-pay-btn").disabled = true;
            stripe.createToken(cardElement).then(function(result) {

                if (typeof result.error != 'undefined') {
                    document.getElementById("stripe-pay-btn").disabled = false;
                    alert(result.error.message);
                }

                if (typeof result.token != 'undefined') {
                    document.getElementById("stripe-token-id").value = result.token.id;
                    document.getElementById('checkout-form').submit();
                }
            });
        }


        const debounce = (func, delay) => {
            let timeoutId;
            return function () {
                const context = this;
                const args = arguments;

                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    func.apply(context, args);
                }, delay);
            };
        }


        const checkSubdomain = debounce(async (obj, e) => {
            $('.submit-btn').prop('disabled', false);

            let subdomain = $(obj).val().trim();
            subdomain = subdomain.replace(/\s/g, ''); // Remove spaces
            $('#subdomain').val(subdomain);
            const pattern = /^[a-zA-Z0-9-]+$/;

            if (!subdomain) {
                $('#subdomainError').text('Subdomain is required');
                $('.submit-btn').prop('disabled', true);
            } else if (!pattern.test(subdomain)) {
                $('#subdomainError').text('Invalid subdomain format. Only letters (both uppercase and lowercase), numbers, and hyphens are allowed.');
                $('.submit-btn').prop('disabled', true);
            } else {
                await checkIsUniqueValueByCompanyColumn('subdomain', subdomain);
            }
        }, 300);



        const checkCompanyName = debounce(async (obj) => {
            const companyName = $(obj).val().trim();
            await checkIsUniqueValueByCompanyColumn('company_name', companyName);
        }, 200);


        const checkTradeLicenceNumber = debounce(async (obj) => {
            const tradeLicenceNumber = $(obj).val().trim();
            await checkIsUniqueValueByCompanyColumn('trade_licence_number', tradeLicenceNumber);
        }, 200);


        const checkEmail = debounce(async (obj) => {
            const email = $(obj).val().trim();
            await checkIsUniqueValueByCompanyColumn('email', email);
        }, 200);


        const checkPhone = debounce(async (obj) => {
            $('.submit-btn').prop('disabled', false);

            const phone     = $(obj).val().trim();
            const pattern   = /^(?:\+|\d{1,4})[ -]?(?:\(\d{1,}\)[ -]?)?\d+(?:[ -]?\d+)*$/;

            if (!phone) {
                $('#phoneError').text('Phone number is required');
                $('.submit-btn').prop('disabled', true);
            } else if (!pattern.test(phone)) {
                $('#phoneError').text('Invalid phone number format. Please enter a valid phone number.');
                $('.submit-btn').prop('disabled', true);
            } else {
                await checkIsUniqueValueByCompanyColumn('phone', phone);
            }
        }, 200);


        const checkIsUniqueValueByCompanyColumn = async (column, value) => {

            await $.ajax({
                url: `{{ route('saas.check-is-unique-value-by-company-column') }}`,
                method: "POST",
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: { column, value },
                success: function ({ status, message }) {
                    if (column == 'company_name') {
                        if (!status) {
                            isCompanyNameError = true;
                            $('#companyNameError').text(message);
                        } else {
                            isCompanyNameError = false;
                            $('#companyNameError').text('');
                        }
                    }

                    if (column == 'trade_licence_number') {
                        if (!status) {
                            isTradeLicenceNumberError = true;
                            $('#tradeLicenceNumberError').text(message);
                        } else {
                            isTradeLicenceNumberError = false;
                            $('#tradeLicenceNumberError').text('');
                        }
                    }

                    if (column == 'email') {
                        if (!status) {
                            isEmailError = true;
                            $('#emailError').text(message);
                        } else {
                            isEmailError = false;
                            $('#emailError').text('');
                        }
                    }

                    if (column == 'phone') {
                        if (!status) {
                            isPhoneError = true;
                            $('#phoneError').text(message);
                        } else {
                            isPhoneError = false;
                            $('#phoneError').text('');
                        }
                    }

                    if (column == 'subdomain') {
                        if (!status) {
                            isSubdomainError = true;
                            $('#subdomainError').text(message);
                        } else {
                            isSubdomainError = false;
                            $('#subdomainError').text('');
                        }
                    }

                    disableSubmitBtn();
                },
                error: function (error) {
                    console.log(error)
                },
            });
        };


        const disableSubmitBtn = () => {
            if (isCompanyNameError || isTradeLicenceNumberError || isPhoneError || isEmailError || isSubdomainError) {
                $('.submit-btn').prop('disabled', true);
            } else {
                $('.submit-btn').prop('disabled', false);
            }
        }
    </script>
@endsection
