<section class="d-flex flex-column gap-4">
    <div class="tab-button style-one">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                @foreach ($data['pricingPlans'] ?? [] as $planDurationType)
                    <a 
                        class="nav-link {{ $loop->first ? 'active' : '' }}" 
                        id="nav-{{ Str::slug(@$planDurationType->name) }}-tab"
                        data-bs-toggle="tab" 
                        href="#nav-{{ Str::slug(@$planDurationType->name) }}" 
                        role="tab"
                        aria-controls="nav-{{ Str::slug(@$planDurationType->name) }}" 
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                    >
                        {{ @$planDurationType->name }}
                    </a>
                @endforeach
            </div>
        </nav>
    </div>

    <div class="tab-content" id="nav-tabContent">
        @foreach ($data['pricingPlans'] ?? [] as $planDurationType)
            <div 
                class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                id="nav-{{ Str::slug(@$planDurationType->name) }}" 
                role="tabpanel"
                aria-labelledby="nav-{{ Str::slug(@$planDurationType->name) }}-tab"
            >
                <div class="row">
                    @foreach ($planDurationType->pricingPlanPrices ?? [] as $item)
                        <div class="col-md-6 col-lg-4 col-xl-3 mb-3">
                            <div class="card position-relative border-0 shadow {{ @$item->pricingPlan->is_popular ? 'popular' : '' }}">
                                <span class="position-absolute popular-badge">
                                    {{ @$item->pricingPlan->is_popular ? _trans('common.Most Popular') : '' }}
                                </span>
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between gap-1">
                                        <h4 class="card-title">{{ @$item->pricingPlan->name }}</h4>
                                        <span class="h2">
                                            {{ currency_format(number_format(@$item['price'], 2)) }}
                                            @if (\Modules\Saas\Enums\PricingPlanDurationType::QUARTERLY == $planDurationType->name)
                                                <del class="month fw-bold text-danger">{{ currency_format(number_format(@$item['pricing_plan']['basic_price'] * 3, 2)) }}</del>
                                            @elseif (\Modules\Saas\Enums\PricingPlanDurationType::HALF_YEARLY == $planDurationType->name)
                                                <del class="month fw-bold text-danger">{{ currency_format(number_format(@$item['pricing_plan']['basic_price'] * 6, 2)) }}</del>
                                            @elseif (\Modules\Saas\Enums\PricingPlanDurationType::YEARLY == $planDurationType->name)
                                                <del class="month fw-bold text-danger">{{ currency_format(number_format(@$item['pricing_plan']['basic_price'] * 12, 2)) }}</del>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="d-grid my-3">
                                        <a 
                                            href="{{ route('saas.company.create') }}?pricing_plan_price_id={{ $item->id }}"
                                            class="btn btn-outline-dark btn-block"
                                        >
                                            {{ _trans('common.Select Plan') }}
                                        </a>
                                    </div>
                                    <ul class="features">
                                        <li>
                                            <span>{{ _trans('common.Employee Limit') }}</span>
                                            {{ @$item->pricingPlan->is_employee_limit ? @$item->pricingPlan->employee_limit : 'Unlimited' }}
                                        </li>
                                        @foreach ($data['planFeatures'] as $id => $feature)
                                            @php
                                                $pricingPlanFeatures = collect(@$item->pricingPlan->pricingPlanFeatures)->pluck('plan_feature_id')->toArray();
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
                                            href="{{ route('saas.company.create') }}?pricing_plan_price_id={{ $item->id }}"
                                            class="btn btn-outline-dark btn-block"
                                        >
                                            {{ _trans('common.Select Plan') }}
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