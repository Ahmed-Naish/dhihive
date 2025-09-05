@extends('backend.layouts.app')
@section('title', @$title)

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
@endsection

@section('content')
    {!! breadcrumb([
        'title' => @$title,
        route('admin.dashboard') => _trans('common.Dashboard'),
        '#' => @$title,
    ]) !!}

    <section class="d-flex flex-column gap-4">
        <div class="tab-button style-one">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    @foreach ($pricingPlans ?? [] as $planDurationType)
                        <a 
                            class="nav-link {{ $loop->first ? 'active' : '' }}" 
                            id="nav-{{ Str::slug(@$planDurationType['name']) }}-tab"
                            data-bs-toggle="tab" 
                            href="#nav-{{ Str::slug(@$planDurationType['name']) }}" 
                            role="tab"
                            aria-controls="nav-{{ Str::slug(@$planDurationType['name']) }}" 
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                        >
                            {{ @$planDurationType['name'] }}
                        </a>
                    @endforeach
                </div>
            </nav>
        </div>

        <div class="tab-content" id="nav-tabContent">
            @foreach ($pricingPlans ?? [] as $planDurationType)
                <div 
                    class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                    id="nav-{{ Str::slug(@$planDurationType['name']) }}" 
                    role="tabpanel"
                    aria-labelledby="nav-{{ Str::slug(@$planDurationType['name']) }}-tab"
                >
                    <div class="row">
                        @foreach ($planDurationType['pricing_plan_prices'] ?? [] as $item)
                            <div class="col-md-6 col-lg-4 col-xl-3 mb-3">
                                <div class="card position-relative border-0 shadow {{ @$item['pricing_plan']['is_popular'] ? 'popular' : '' }}">
                                    <span class="position-absolute popular-badge">
                                        {{ @$item['pricing_plan']['is_popular'] ? _trans('common.Most Popular') : '' }}
                                    </span>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between gap-1">
                                            <h4 class="card-title">{{ @$item['pricing_plan']['name'] }}</h4>
                                            <span class="h2">
                                                {{ currency_format(number_format(@$item['price'], 2)) }}
                                                @if (\Modules\Saas\Enums\PricingPlanDurationType::QUARTERLY == $planDurationType['name'])
                                                    <del class="month fw-bold text-danger">{{ currency_format(number_format(@$item['pricing_plan']['basic_price'] * 3, 2)) }}</del>
                                                @elseif (\Modules\Saas\Enums\PricingPlanDurationType::HALF_YEARLY == $planDurationType['name'])
                                                    <del class="month fw-bold text-danger">{{ currency_format(number_format(@$item['pricing_plan']['basic_price'] * 6, 2)) }}</del>
                                                @elseif (\Modules\Saas\Enums\PricingPlanDurationType::YEARLY == $planDurationType['name'])
                                                    <del class="month fw-bold text-danger">{{ currency_format(number_format(@$item['pricing_plan']['basic_price'] * 12, 2)) }}</del>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="d-grid my-3">
                                            <a 
                                                href="javascript:;"
                                                onclick="mainModalOpen(`{{ route('single-company.upgrade-plan.modal', $item['id']) }}`)" 
                                                class="btn btn-outline-dark btn-block"
                                            >
                                                {{ _trans('common.Upgrade Plan') }}
                                            </a>
                                        </div>
                                        <ul class="features">
                                            <li>
                                                <span>{{ _trans('common.Employee Limit') }}</span>
                                                {{ @$item['pricing_plan']['is_employee_limit'] ? @$item['pricing_plan']['employee_limit'] : 'Unlimited' }}
                                            </li>
                                            @foreach ($planFeatures as $id => $feature)
                                                @php
                                                    $pricingPlanFeatures = collect(@$item['pricing_plan']['pricing_plan_features'])->pluck('plan_feature_id')->toArray();
                                                @endphp
                                                @if (in_array($id, $pricingPlanFeatures))
                                                    <li>
                                                        {{ $feature }}
                                                        <i class="fa-solid fa-check text-primary"></i>
                                                    </li>
                                                @else
                                                    <li>
                                                        {{ $feature }}
                                                        <i class="fa-solid fa-times text-danger"></i>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                        <div class="d-grid mt-3 mb-2">
                                            <a 
                                                href="javascript:;"
                                                onclick="mainModalOpen(`{{ route('single-company.upgrade-plan.modal', $item['id']) }}`)" 
                                                class="btn btn-outline-dark btn-block"
                                            >
                                                {{ _trans('common.Upgrade Plan') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection


@section('script')
    
<script src="https://js.stripe.com/v3/"></script>
@endsection