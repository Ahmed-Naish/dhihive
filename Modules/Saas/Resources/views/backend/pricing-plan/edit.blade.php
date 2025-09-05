@extends('backend.layouts.app')
@section('title', @$title)

@section('style')
    <style>
        .form-control:disabled, .form-check-input:disabled {
            background: #e9ecef !important
        }
    </style>
@endsection

@section('content')
    {!! breadcrumb([
        'title' => @$title,
        route('admin.dashboard') => _trans('common.Dashboard'),
        '#' => @$title,
    ]) !!}

    <form action="{{ $url }}" class="p-0" method="post" enctype="multipart/form-data" autocomplete="off">
        @csrf

        <input type="hidden" name="pricing_plan_id" value="{{ @$pricingPlan->id }}" id="pricingPlanID">

        <div class="row">
            <div class="col-lg-8">
                <div class="table-content table-basic mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h3>{{ _trans('common.General Info') }}</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label">
                                            {{ _trans('common.Name') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            name="name" 
                                            class="form-control ot-form-control ot-input"
                                            placeholder="{{ _trans('common.Name') }}"
                                            value="{{ old('name', @$pricingPlan->name) }}" 
                                            required
                                            onkeyup="isPlanNameExist(this)"
                                        >
                                        @if($errors->has('name'))
                                            <div class="invalid-feedback d-block">{{ $errors->first('name') }}</div>
                                        @endif
                                        <span class="text-danger d-none" id="planNameError">{{ _trans('common.Plan Name already exists!') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">
                                            {{ _trans('common.Employee Limit') }}
                                            <i class="las la-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ _trans('If you set the value to 0 or leave it blank, the employee limit should be considered unlimited') }}"></i>
                                        </label>
                                        <input 
                                            type="number" 
                                            name="employee_limit" 
                                            class="form-control ot-form-control ot-input"
                                            placeholder="{{ _trans('common.Employee Limit') }}"
                                            value="{{ old('employee_limit', @$pricingPlan->employee_limit) }}"
                                        >
                                        @if($errors->has('employee_limit'))
                                            <div class="invalid-feedback d-block">{{ $errors->first('employee_limit') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">
                                            {{ _trans('common.Basic Price') }}
                                            <span class="text-danger">*</span>
                                            <i class="las la-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ _trans('Basic Price intially count for 1 month') }}"></i>
                                        </label>
                                        <input 
                                            type="number" 
                                            step="any"
                                            name="basic_price" 
                                            class="form-control ot-form-control ot-input"
                                            placeholder="{{ _trans('common.Basic Price') }}"
                                            value="{{ old('basic_price', @$pricingPlan->basic_price) }}" 
                                            required
                                            id="basicPrice"
                                            onkeyup="basicPriceHandler(this)"
                                        >
                                        @if($errors->has('basic_price'))
                                            <div class="invalid-feedback d-block">{{ $errors->first('basic_price') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">
                                            {{ _trans('common.Popular') }}
                                            <i class="las la-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ _trans('Opting for this plan as the preferred choice could render the existing popular plan obsolete, given the evolving definition of what is considered popular') }}"></i>
                                        </label>
                                        <select name="is_popular" class="form-control select2">
                                            <option></option>
                                            <option value="1" {{ old('is_popular', @$pricingPlan->is_popular) == 1 ? 'selected' : '' }}>{{ _trans('common.Yes') }}</option>
                                            <option value="0" {{ old('is_popular', @$pricingPlan->is_popular) == 0 ? 'selected' : '' }}>{{ _trans('common.No') }}</option>
                                        </select>
                                        @if($errors->has('is_popular'))
                                            <div class="invalid-feedback d-block">{{ $errors->first('is_popular') }}</div>
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
                                            <option value="1" {{ old('status_id', @$pricingPlan->status_id) == 1 ? 'selected' : '' }}>{{ _trans('common.Active') }}</option>
                                            <option value="4" {{ old('status_id', @$pricingPlan->status_id) == 4 ? 'selected' : '' }}>{{ _trans('common.Inactive') }}</option>
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
                <div class="table-content table-basic mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h3>
                                {{ _trans('common.Pricing Plan Prices') }}
                                <i class="las la-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ _trans('At least one pricing plan must have a defined price') }}"></i>
                            </h3>
                            <div class="alert alert-warning d-none" id="basicPriceWarning">
                                {{ _trans('common.Please add Basic Price first!') }}
                            </div>
                            <div class="alert alert-warning d-none" id="pricingPlanWarning">
                                {{ _trans('common.At least one pricing plan required') }}
                            </div>
                            <table class="table table-borderless" id="table">
                                <tbody>
                                    @foreach ($planDurationTypes ?? [] as $id => $name)
                                        <tr>
                                            <td width="20%">
                                                <div class="check-box">
                                                    <div class="form-check d-flex align-items-center gap-2">
                                                        <input 
                                                            class="form-check-input cursor-pointer plan-duration-type-id" 
                                                            type="checkbox"
                                                            name="plan_duration_type_id[]"
                                                            onclick="togglePricingPlanPrice(this)"
                                                            id="priceDurationType{{ $id }}"
                                                            value="{{ $id }}"
                                                            duration-month="
                                                                @if (\Modules\Saas\Enums\PricingPlanDurationType::MONTHLY == $name)
                                                                    1
                                                                @elseif (\Modules\Saas\Enums\PricingPlanDurationType::QUARTERLY == $name)
                                                                    3
                                                                @elseif (\Modules\Saas\Enums\PricingPlanDurationType::HALF_YEARLY == $name)
                                                                    6
                                                                @elseif (\Modules\Saas\Enums\PricingPlanDurationType::YEARLY == $name)
                                                                    12
                                                                @endif
                                                            "
                                                            {{ 
                                                                in_array($id, @$pricingPlan->pricingPlanPrices->pluck('plan_duration_type_id')->toArray()) 
                                                                && @$pricingPlan->pricingPlanPrices->where('plan_duration_type_id', $id)->first()->status_id == 1
                                                                ? 'checked'
                                                                : ''
                                                            }}                                                                                                                                                                                                                                                                                                                                                                `
                                                        >
                                                        <label for="priceDurationType{{ $id }}" class="pt-1 ms-1 cursor-pointer">{{ $name }}</label> 
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <input 
                                                    type="text" 
                                                    step="any"
                                                    name="pricing_plan_prices[{{ $id }}]" 
                                                    class="form-control ot-form-control ot-input priceing-plan-price"
                                                    placeholder="{{ _trans('common.Price') }}"
                                                    value="{{ 
                                                            in_array($id, @$pricingPlan->pricingPlanPrices->pluck('plan_duration_type_id')->toArray()) 
                                                            ? @$pricingPlan->pricingPlanPrices->where('plan_duration_type_id', $id)->first()->price 
                                                            : 0
                                                        }}"
                                                    required
                                                >
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="table-content table-basic">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive mt-3">
                                <h3>{{ _trans('common.Feature') }}</h3>
                                <div class="alert alert-warning d-none" id="featureWarning">
                                    {{ _trans('common.At least one feature required') }}
                                </div>
                                <table class="table table-bordered" id="table">
                                    <thead>
                                        <tr>
                                            <td colspan="2">
                                                <div class="check-box">
                                                    <div class="form-check d-flex align-items-center gap-2">
                                                        <input 
                                                            class="form-check-input cursor-pointer" 
                                                            type="checkbox" 
                                                            id="all_check" 
                                                            onclick="toggleAllFeature(this)"
                                                            {{ count(@$pricingPlan->pricingPlanFeatures) == count($planFeatures) ? 'checked' : '' }}
                                                        >
                                                        <label for="all_check" class="pt-1 ms-1 cursor-pointer">{{ _trans('common.All Feature') }}</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($planFeatures as $id => $title)
                                            <tr>
                                                <td width="30px">
                                                    <div class="check-box">
                                                        <div class="form-check">
                                                            <input 
                                                                class="form-check-input cursor-pointer feature" 
                                                                type="checkbox" 
                                                                name="feature_ids[]" 
                                                                value="{{ $id }}" 
                                                                {{ in_array($id, @$pricingPlan->pricingPlanFeatures->pluck('plan_feature_id')->toArray()) ? 'checked' : '' }}
                                                            >
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $title }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group d-flex justify-content-center mt-3 mb-5">
                <button type="submit" class="btn btn-gradian mr-3 py-3 px-5 fs-5" id="submitBtn">
                    <i class="las la-check-circle me-1"></i> {{ @$button }}
                </button>
            </div>
        </div>
    </form>
@endsection
@section('script')
    <script>
        const toggleAllFeature = (obj) => {
            $('.feature').prop('checked', $(obj).is(":checked"))

            if ($(obj).is(":checked")) {
                $('#featureWarning').addClass('d-none');
                $('#submitBtn').prop('disabled', false);
            } else {
                $('#featureWarning').removeClass('d-none');
                $('#submitBtn').prop('disabled', true);
            }
        }

        $('.feature').on('click', function () {

            let count = 0;
            $('.feature').each(function () {
                if($(this).is(":checked")){
                    count++;
                }
            });

            if (count == $('.feature').length) {
                $('#all_check').prop('checked', true);
            } else {
                $('#all_check').prop('checked', false);
                $('#submitBtn').prop('disabled', true);
            }

            if (count == 0) {
                $('#featureWarning').removeClass('d-none');
                $('#submitBtn').prop('disabled', true);
            } else {
                $('#featureWarning').addClass('d-none');
                $('#submitBtn').prop('disabled', false);
            }
        })

        const togglePricingPlanPrice = (obj) => {
            if ($(obj).is(":checked")) {
                $(obj).closest('tr').find('.priceing-plan-price').prop('disabled', false);
            } else {
                $(obj).closest('tr').find('.priceing-plan-price').prop('disabled', true);
            }

            pricingPlanRequired();
        }

        const basicPriceHandler = (obj, isEdit = true) => {
            let basicPrice = Number($(obj).val());
            
            $('.plan-duration-type-id').each(function () {
                if (basicPrice > 0) {
                    $('#basicPriceWarning').addClass('d-none');

                    if (isEdit) {
                        $(this).prop('disabled', false);
                        $(this).prop('checked', true);
                        $(this).closest('tr').find('.priceing-plan-price').val(Number($(this).attr('duration-month')) * basicPrice);
                    }
                } else {
                    $('#basicPriceWarning').removeClass('d-none');

                    if (isEdit) {
                        $(this).prop('disabled', true);
                        $(this).prop('checked', false);

                        $(this).closest('tr').find('.priceing-plan-price').val('')
                    }
                }

                togglePricingPlanPrice($(this));
            }); 
        }

        $(document).ready(function () {
            $('.plan-duration-type-id').each(function () {
                togglePricingPlanPrice($(this));
            });


            if ($('#pricingPlanID').val()) {
                basicPriceHandler($('#basicPrice'), false)
            } else {
                basicPriceHandler($('#basicPrice'))
            }
        });

        const isPlanNameExist = async (obj) => {
            
            $('#planNameError').addClass('d-none');
            $('#submitBtn').prop('disabled', false);

            let name = $(obj).val();

            await $.ajax({
                url: `{{ route('saas.is-plan-name-exist') }}`,
                method: 'GET',
                data: {name},
                success: function({status}) {
                    if (status) {
                        $('#planNameError').removeClass('d-none');
                        $('#submitBtn').prop('disabled', true);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX request failed:', error); 
                }
            });
        }

        const pricingPlanRequired = () => {
            let count = 0;
            $('.plan-duration-type-id').each(function () {
                if ($(this).is(":checked")) {
                    count++;
                }
            });

            if (count == 0 && Number($('#basicPrice').val())) {
                $('#pricingPlanWarning').removeClass('d-none');
                $('#submitBtn').prop('disabled', true);
            } else {
                $('#pricingPlanWarning').addClass('d-none');
                $('#submitBtn').prop('disabled', false);
            }
        }
    </script>
@endsection
