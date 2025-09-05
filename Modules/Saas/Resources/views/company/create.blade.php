@extends('backend.layouts.app')
@section('title', @$data['title'])

@section('style')
<style>

    .popular {
        background: #f4f4ff;
        border: 5px solid #4f46e5 !important
    }

    .popular-badge {
        background: #4f46e5;
        padding-inline: 8px;
        color: #ffffff;
        border-radius: 5px;
        left: -55px;
        top: 50px;
        transform: rotate(270deg);
    }

    .card:hover .btn-outline-dark{
        color: #ffffff;
        background: #4f46e5;
        border: 1px solid #4f46e5;
    }

    .tab-button.style-one nav .nav-tabs {
        padding-bottom: 0px;
        border: 0;
        background: #ffffff;
        padding: 10px;
        border-radius: 30px;
        width: fit-content;
        margin: 0 auto;
    }

    .tab-button.style-one nav .nav-tabs .nav-link {
        font-size: 15px;
        border: 0;
        display: block;
        padding: 7px 20px;
        text-align: center;
        border-radius: 20px;
        color: #333333;
    }

    .tab-button.style-one nav .nav-tabs .nav-link.active {
        color: #ffffff;
        background: #4f46e5;
    }

    del {
        font-size: 14px;
    }

    .features {
        list-style: none;
        padding: 0;
    }

    .features li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 5px;
        font-size: 15px;
        padding: 5px;
        border-radius: 5px;
    }

    .features li:hover {
        background: #f7f7f7;
        color: #4F46E5
    }
  </style>


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

        .radio-inputs>* {
            /* margin: 6px; */
        }

        .radio-input:checked+.radio-tile {
            border-color: #2260ff;
            /* box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1); */
            color: #2260ff;
        }

        .radio-input:checked+.radio-tile:before {
            transform: scale(1);
            opacity: 1;
            background-color: #2260ff;
            border-color: #2260ff;
        }

        .radio-icon img {
            width: 25px !important
        }

        .radio-input:checked+.radio-tile .radio-icon svg {
            fill: #2260ff;
        }

        .radio-input:checked+.radio-tile .radio-label {
            color: #2260ff;
        }

        .radio-input:focus+.radio-tile {
            border-color: #2260ff;
            /* box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1), 0 0 0 4px #b5c9fc; */
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
            /* box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1); */
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
    {!! breadcrumb([
        'title' => @$data['title'],
        route('admin.dashboard') => _trans('common.Dashboard'),
        '#' => @$data['title'],
    ]) !!}

    @if (request()->filled('pricing_plan_price_id'))
        <form action="{{ route('saas.company.store') }}" class="p-0" method="POST" enctype="multipart/form-data" autocomplete="off" id="checkout-form">
            @csrf
            <input type="hidden" name="pricing_plan_price_id" value="{{ request('pricing_plan_price_id') }}">
            <input type="hidden" name="source" value="Admin">
            <input type="hidden" name="expiry_date" value="{{ old('expiry_date', date('Y-m-d', strtotime($data['endDate']))) }}">
            <div class="row">
                <div class="col-lg-8">
                    <div class="table-content table-basic mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h3>{{ _trans('common.General Info') }}</h3>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">
                                                {{ _trans('common.Company Name') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                name="company_name" 
                                                class="form-control ot-form-control ot-input"
                                                placeholder="{{ _trans('common.Company Name') }}"
                                                value="{{ old('company_name') }}" 
                                                required
                                            >
                                            @if($errors->has('company_name'))
                                                <div class="invalid-feedback d-block">{{ $errors->first('company_name') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">
                                                {{ _trans('common.Owner Name') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                name="name" 
                                                class="form-control ot-form-control ot-input"
                                                placeholder="{{ _trans('common.Owner Name') }}"
                                                value="{{ old('name') }}" 
                                                required
                                            >
                                            @if($errors->has('name'))
                                                <div class="invalid-feedback d-block">{{ $errors->first('name') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">
                                                {{ _trans('common.Phone') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                name="phone" 
                                                class="form-control ot-form-control ot-input"
                                                placeholder="{{ _trans('common.Phone') }}"
                                                value="{{ old('phone') }}" 
                                                required
                                            >
                                            @if($errors->has('phone'))
                                                <div class="invalid-feedback d-block">{{ $errors->first('phone') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">
                                                {{ _trans('common.Email') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input 
                                                type="email" 
                                                name="email" 
                                                class="form-control ot-form-control ot-input"
                                                placeholder="{{ _trans('common.Email') }}"
                                                value="{{ old('email') }}" 
                                                required
                                            >
                                            @if($errors->has('email'))
                                                <div class="invalid-feedback d-block">{{ $errors->first('email') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">
                                                {{ _trans('common.Trade Licence Number') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                name="trade_licence_number" 
                                                class="form-control ot-form-control ot-input"
                                                placeholder="{{ _trans('common.Trade Licence Number') }}"
                                                value="{{ old('trade_licence_number') }}" 
                                                required
                                            >
                                            @if($errors->has('trade_licence_number'))
                                                <div class="invalid-feedback d-block">{{ $errors->first('trade_licence_number') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">
                                                {{ _trans('common.Subdomain') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                name="subdomain" 
                                                class="form-control ot-form-control ot-input"
                                                placeholder="{{ _trans('common.Subdomain') }}"
                                                value="{{ old('subdomain') }}" 
                                                required
                                            >
                                            @if($errors->has('subdomain'))
                                                <div class="invalid-feedback d-block">{{ $errors->first('subdomain') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">
                                                {{ _trans('common.Total Employee') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input 
                                                type="number" 
                                                name="total_employee" 
                                                class="form-control ot-form-control ot-input"
                                                placeholder="{{ _trans('common.Total Employee') }}"
                                                value="{{ old('total_employee') }}" 
                                                required
                                            >
                                            @if($errors->has('total_employee'))
                                                <div class="invalid-feedback d-block">{{ $errors->first('total_employee') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">
                                                {{ _trans('common.Business Type') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input 
                                                type="text" 
                                                name="business_type" 
                                                class="form-control ot-form-control ot-input"
                                                placeholder="{{ _trans('common.Business Type') }}"
                                                value="{{ old('business_type') }}" 
                                                required
                                            >
                                            @if($errors->has('business_type'))
                                                <div class="invalid-feedback d-block">{{ $errors->first('business_type') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">
                                                {{ _trans('common.Country') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select name="country_id" class="form-control select2" required>
                                                <option value=""></option>
                                                @foreach ($data['countries'] ?? [] as $id => $name)
                                                    <option value="{{ $id }}" {{ old('status_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('country_id'))
                                                <div class="invalid-feedback d-block">{{ $errors->first('country_id') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">
                                                {{ _trans('common.Status') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select name="status_id" class="form-control select2">
                                                <option value="1" {{ old('status_id') == 1 ? 'selected' : '' }}>{{ _trans('common.Active') }}</option>
                                                <option value="4" {{ old('status_id') == 4 ? 'selected' : '' }}>{{ _trans('common.Inactive') }}</option>
                                            </select>
                                            @if($errors->has('status_id'))
                                                <div class="invalid-feedback d-block">{{ $errors->first('status_id') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="table-content table-basic mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                    <h4 class="fs-4">
                                        {{ @$data['pricingPlanPrice']->pricingPlan->name }}
                                        <small> / {{ @$data['pricingPlanPrice']->planDurationType->name }}</small>
                                    </h4>
                                    @if (@$data['pricingPlanPrice']->pricingPlan->is_popular)
                                        <span class="badge bg-info text-dark">{{ _trans('common.Most Popular') }}</span>
                                    @endif
                                </div>
                                <div class="d-flex flex-column gap-0 bg-light p-3 rounded">
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <p>{{ _trans('common.Start Date') }}</p>
                                        <p>{{ showDate($data['startDate']) }}</p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <p>{{ _trans('common.End Date') }}</p>
                                        <p>{{ showDate($data['endDate']) }}</p>
                                    </div>
                                    <div class="text-center fs-5 fw-bold">
                                        {{ _trans('common.Total') }} {{ currency_format(number_format($data['pricingPlanPrice']->price, 2)) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-content table-basic mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h3>{{ _trans('common.Payment') }}</h3>
                                <div class="form-group col-12 payment-gateway-wrapper">
                                    <div class="radio-inputs">
                                        <label>
                                            <input class="radio-input" type="radio" name="payment_gateway" value="Stripe" checked>
                                            <span class="radio-tile">
                                                <span class="radio-icon">
                                                    <img src="{{ asset('vendor/Saas/Assets/images/payment-icon/credit-card.png') }}" alt="img">
                                                </span>
                                                <span class="radio-label">{{ _trans('common.Credit Card') }}</span>
                                            </span>
                                        </label>
                                        <label>
                                            <input class="radio-input" type="radio" name="payment_gateway" value="Offline Payment" {{ old('payment_gateway') == 'Offline Payment' ? 'checked' : '' }}>
                                            <span class="radio-tile">
                                                <span class="radio-icon">
                                                    <img src="{{ asset('vendor/Saas/Assets/images/payment-icon/offline-payment.png') }}" alt="img">
                                                </span>
                                                <span class="radio-label">{{ _trans('common.Offline Payment') }}</span>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="rounded-3 stripe-wrapper mt-3" style="display: {{ old('payment_gateway') == 'Offline Payment' ? 'none' : '' }}">
                                        <input type='hidden' name='stripeToken' id='stripe-token-id'>
                                        <br>
                                        <div id="card-element"></div>
                                    </div>

                                    <div class="offline-wrapper mt-3" style="display: {{ old('payment_gateway') == 'Offline Payment' ? '' : 'none' }}">
                                        @if (@base_settings('is_payment_type_cash') || @base_settings('is_payment_type_cheque') || @base_settings('is_payment_type_bank_transfer'))
                                        
                                        <div class="form-group mb-3">
                                            <label class="form-label">
                                                {{ _trans('common.Payment Type') }}
                                            </label>
                                            <select class="form-control select2" name="offline_payment_type">
                                                <option value=""></option>
                                                @if (@base_settings('is_payment_type_cash'))
                                                    <option value="cash" {{ old('offline_payment_type') == 'cash' ? 'selected' : '' }}>{{ _trans('common.Cash') }}</option>
                                                @endif
                                                @if (@base_settings('is_payment_type_cheque'))
                                                    <option value="cheque" {{ old('offline_payment_type') == 'cheque' ? 'selected' : '' }}>{{ _trans('common.Cheque') }}</option>
                                                @endif
                                                @if (@base_settings('is_payment_type_bank_transfer'))
                                                    <option value="bank_transfer" {{ old('offline_payment_type') == 'bank_transfer' ? 'selected' : '' }}>{{ _trans('common.Bank Transfer') }}</option>
                                                @endif
                                            </select>
                                        </div>
                                        @endif
                                        <div class="form-group mb-3">
                                            <label class="form-label">
                                                {{ _trans('common.Additional Details') }} 
                                                <span class="text-danger">*</span>
                                            </label>
                                            <textarea class="form-control ot-contact-textarea mt-0" id="offlinePaymentAdditionalDetails" name="offline_payment_details" placeholder="e.g. Transaction ID: XXXX-XXXX-XXXX-XXXX">{{ old('offline_payment_details') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="d-grid mt-3">
                                        <button type="button" onclick="createToken()" id='stripe-pay-btn' class="btn btn-gradian submit-btn">{{ _trans('common.Submit') }}</button>
                                        <button id='demo-checkout-btn' class="d-none btn btn-gradian submit-btn">{{ _trans('common.Submit') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @else
        @include('saas::company.plans', ['data' => $data])
    @endif
@endsection



@section('script')
    <script src="https://js.stripe.com/v3/"></script>

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
    @elseif (old('payment_gateway') == 'Stripe')
        <script>
            $('.offline-wrapper').hide();
            $('#stripe-pay-btn').removeClass('d-none');
            $('#demo-checkout-btn').addClass('d-none');
            
            setTimeout(() => {
                $('.error').addClass('d-none');
            }, 7000);
        </script>
    @endif

    <script>
         const togglePaymentGateway = (paymentMethod) => {
            if (paymentMethod === 'Stripe') {
                $('.stripe-wrapper').show();
                $('.offline-wrapper').hide();
                $('.offline-wrapper').addClass('d-none');
                $('#stripe-pay-btn').removeClass('d-none');
                $('#offlinePaymentAdditionalDetails').prop('required', false);
                $('#demo-checkout-btn').addClass('d-none');
            } else {
                $('.stripe-wrapper').hide();
                $('.offline-wrapper').show();
                $('#stripe-pay-btn').addClass('d-none');
                $('.offline-wrapper').removeClass('d-none');
                $('#offlinePaymentAdditionalDetails').prop('required', true);
                $('#demo-checkout-btn').removeClass('d-none');
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
    </script>
@endsection
