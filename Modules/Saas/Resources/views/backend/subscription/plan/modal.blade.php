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
        width: 25px !important;
    }

    .radio-icon img {
        width: 100% !important;
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
        font-size: 14px;
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


<div class="modal fade lead-modal" tabindex="-1" id="lead-modal" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content data">
            <div class="modal-header modal-header-image mb-3">
                <h5 class="modal-title text-white">{{ @$title }} </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times text-white" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="checkout-form" action="{{ route('single-company.upgrade-plan.store') }}" class="row p-2" method="POST" enctype="multipart/form-data">
                    @csrf

                    @php
                        $startDate  = \Carbon\Carbon::now()->format('M d, Y');

                        if (\Modules\Saas\Enums\PricingPlanDurationType::MONTHLY == @$pricingPlan['duration_type']) {
                            $endDate = \Carbon\Carbon::now()->addMonths(1)->format('M d, Y');
                        } elseif (\Modules\Saas\Enums\PricingPlanDurationType::QUARTERLY == @$pricingPlan['duration_type']) {
                            $endDate = \Carbon\Carbon::now()->addMonths(3)->format('M d, Y');
                        } elseif (\Modules\Saas\Enums\PricingPlanDurationType::HALF_YEARLY == @$pricingPlan['duration_type']) {
                            $endDate = \Carbon\Carbon::now()->addMonths(6)->format('M d, Y');
                        } elseif (\Modules\Saas\Enums\PricingPlanDurationType::YEARLY == @$pricingPlan['duration_type']) {
                            $endDate = \Carbon\Carbon::now()->addMonths(12)->format('M d, Y');
                        }
                    @endphp 

                    <input type="hidden" name="pricing_plan_price_id" value="{{ $pricingPlan['pricing_plan_price_id'] }}">
                    <input type="hidden" name="expiry_date" value="{{ date('Y-m-d', strtotime($endDate)) }}">
                    <input type="hidden" name="source" value="Company">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <td>{{ _trans('common.Plan Name') }}</td>
                                        <td>: {{ $pricingPlan['plan_name'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ _trans('common.Duration Type') }}</td>
                                        <td>: {{ $pricingPlan['duration_type'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ _trans('common.Employee Limit') }}</td>
                                        <td>: {{ $pricingPlan['employee_limit'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ _trans('common.Is Popular') }}</td>
                                        <td>: {{ $pricingPlan['is_popular'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ _trans('common.Basic Price') }}</td>
                                        <td>: {{ $pricingPlan['currency'] }}{{ $pricingPlan['basic_price'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ _trans('common.Price') }}</td>
                                        <td>: {{ $pricingPlan['currency'] }}{{ $pricingPlan['price'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ _trans('common.Start Date') }}</td>
                                        <td>: {{ showDate($startDate) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ _trans('common.End Date') }}</td>
                                        <td>: {{ showDate($endDate) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="radio-inputs">
                                <label>
                                    <input class="radio-input" type="radio" name="payment_gateway" value="Stripe" checked>
                                    <span class="radio-tile">
                                        <span class="radio-icon">
                                            <img src="{{ global_asset('vendor/Saas/Assets/images/payment-icon/credit-card.png') }}" alt="img">
                                        </span>
                                        <span class="radio-label">Credit Card</span>
                                    </span>
                                </label>
                                <label>
                                    <input class="radio-input" type="radio" name="payment_gateway" value="Offline Payment">
                                    <span class="radio-tile">
                                        <span class="radio-icon">
                                            <img src="{{ global_asset('vendor/Saas/Assets/images/payment-icon/offline-payment.png') }}" alt="img">
                                        </span>
                                        <span class="radio-label">Offline Payment</span>
                                    </span>
                                </label>
                            </div>
                            <div class="rounded-3 stripe-wrapper mt-3" style="display: block">
                                <input type='hidden' name='stripeToken' id='stripe-token-id'>
                                <br>
                                <div id="card-element"></div>
                            </div>
                            <div class="rounded-3 offline-wrapper mt-3 d-none" style="display: none">
                                <div class="form-group mb-3">
                                    <label class="form-label">
                                        {{ _trans('common.Payment Type') }} 
                                    </label>
                                    <select name="offline_payment_type" class="form-select">
                                        <option value="">{{ _trans('common.Choose One') }}</option>
                                        @foreach ($offlinePaymentTypes ?? [] as $offlinePaymentType)
                                            <option value="{{ $offlinePaymentType }}">{{ $offlinePaymentType }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="details" class="form-label">
                                        {{ _trans('common.Additional Details') }} 
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control mt-0" id="offlinePaymentAdditionalDetails" name="offline_payment_details" placeholder="{{ _trans('common.Ex: Transaction ID: XXXX-XXXX-XXXX-XXXX') }}"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group d-flex justify-content-end">
                        <button type="button" onclick="createToken()" id='stripe-pay-btn' class="btn btn-gradian pull-right">{{ _trans('common.Submit') }}</button>
                        <button id='offline-checkout-btn' class="d-none btn btn-gradian pull-right">{{ _trans('common.Submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<script src="{{ global_asset('backend/js/fs_d_ecma/modal/__modal.min.js') }}"></script>
<script src="{{ global_asset('backend/js/__loader.js') }}"></script>
<script src="https://js.stripe.com/v3/"></script>
<script>
    function togglePaymentGateway (paymentMethod) {
        if (paymentMethod === 'Stripe') {
            $('.stripe-wrapper').show();
            $('.offline-wrapper').hide();
            $('.offline-wrapper').addClass('d-none');
            $('#stripe-pay-btn').removeClass('d-none');
            $('#offline-checkout-btn').addClass('d-none');
            $('#offlinePaymentAdditionalDetails').prop('required', false);
        } else {
            $('.stripe-wrapper').hide();
            $('.offline-wrapper').show();
            $('#stripe-pay-btn').addClass('d-none');
            $('.offline-wrapper').removeClass('d-none');
            $('#offline-checkout-btn').removeClass('d-none');
            $('#offlinePaymentAdditionalDetails').prop('required', true);
        }
    }


    $(document).on('change', '.radio-input', function() {
        var paymentMethod = $(this).val();

        togglePaymentGateway(paymentMethod);
    });


    var stripe = Stripe(`{{ $stripeToken }}`)
    var elements = stripe.elements();
    var cardElement = elements.create('card');
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