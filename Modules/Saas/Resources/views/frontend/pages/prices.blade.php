@extends('saas::frontend.layouts.master')

@section('title', @$data['title'])

@section('content')
    <section class="adds-pricing-section without-bg-page-screen pb-0">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="without-bg-page-title">
                        <h3>{{ _trans('frontend.Ready To get Started?') }}</h3>
                    </div>
                    <div class="without-bg-page-content">
                        <p>{{ _trans('frontend.Choose a plan tailed to your needs') }}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="tab-button style-one mb-70">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                @foreach ($data['planDurationTypes'] ?? [] as $planDurationType)
                                    <a 
                                        class="nav-link {{ $loop->first ? 'active' : '' }}" 
                                        id="nav-{{ Str::slug($planDurationType->name) }}-tab"
                                        data-bs-toggle="tab" 
                                        href="#nav-{{ Str::slug($planDurationType->name) }}" 
                                        role="tab"
                                        aria-controls="nav-{{ Str::slug($planDurationType->name) }}" 
                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                    >
                                        {{ $planDurationType->name }}
                                    </a>
                                @endforeach
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="container mb-5">
            <div class="row">
                <div class="col-lg--12">
                    <div class="tab-content" id="nav-tabContent">
                        @foreach ($data['planDurationTypes'] ?? [] as $planDurationType)
                            <div 
                                class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                id="nav-{{ Str::slug($planDurationType->name) }}" 
                                role="tabpanel"
                                aria-labelledby="nav-{{ Str::slug($planDurationType->name) }}-tab"
                            >
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="pricing-wrapper d-flex">
                                            <div class="left-wrapper">
                                                <div class="single-card mb-24">
                                                    <div class="card-top  mt-2">
                                                    </div>
                                                    <div class="card-bottom">
                                                        <div class="pricing-list">
                                                            <ul class="listing">
                                                                <li class="single-list">{{ _trans('frontend.Employee Limit') }}</li>
                                                                @foreach ($data['planFeatures'] as $feature)
                                                                    <li class="single-list">{{ $feature }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @foreach ($planDurationType->pricingPlanPrices ?? [] as $item)
                                                <div class="right-wrapper d-flex flex-fill">
                                                    <div class="single-card flex-fill position-relative">
                                                        @if (@$item->pricingPlan->is_popular)
                                                            <div class="popular-badge " data-label="Most Popular"></div>
                                                        @endif
                                                        <div class="card-top mt-2 ">
                                                            <strong class="title-name text-capitalize">{{ @$item->pricingPlan->name }}</strong>
                                                            <div class="prices">
                                                                <h4 class="price">
                                                                    <span class="currency"></span>{{ currency_format(number_format(@$item->price, 2)) }}
                                                                    @if (\Modules\Saas\Enums\PricingPlanDurationType::QUARTERLY == $planDurationType->name)
                                                                        <del class="month fw-bold text-danger">{{ currency_format(number_format(@$item->pricingPlan->basic_price * 3, 2)) }}</del>
                                                                    @elseif (\Modules\Saas\Enums\PricingPlanDurationType::HALF_YEARLY == $planDurationType->name)
                                                                        <del class="month fw-bold text-danger">{{ currency_format(number_format(@$item->pricingPlan->basic_price * 6, 2)) }}</del>
                                                                    @elseif (\Modules\Saas\Enums\PricingPlanDurationType::YEARLY == $planDurationType->name)
                                                                        <del class="month fw-bold text-danger">{{ currency_format(number_format(@$item->pricingPlan->basic_price * 12, 2)) }}</del>
                                                                    @endif
                                                                </h4>
                                                            </div>
                                                            <a class="primary_btn w-80" href="{{ route('saas.checkoutPage') }}?pricing_plan_price_id={{ @$item->id }}">{{ _trans('frontend.Choose Your Plan') }}</a>
                                                        </div>
                                                        <div class="card-bottom">
                                                            <div class="pricing-list">
                                                                <ul class="listing">
                                                                    <li class="single-list text-primary fw-bold">
                                                                        {{ @$item->pricingPlan->is_employee_limit ? @$item->pricingPlan->employee_limit : 'Unlimited' }}
                                                                    </li>
                                                                    @foreach ($data['planFeatures'] as $id => $feature)
                                                                        @if (in_array($id, @$item->pricingPlan->pricingPlanFeatures->pluck('plan_feature_id')->toArray()))
                                                                            <li class="single-list">
                                                                                <i class="fa-solid fa-check text-primary"></i>
                                                                            </li>
                                                                        @else
                                                                            <li class="single-list">
                                                                                <i class="fa-solid fa-times text-danger"></i>
                                                                            </li>
                                                                        @endif
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                            <a class="primary_btn w-80 mb-3" href="{{ route('saas.checkoutPage') }}?pricing_plan_price_id={{ @$item->id }}">{{ _trans('frontend.Choose Your Plan') }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection