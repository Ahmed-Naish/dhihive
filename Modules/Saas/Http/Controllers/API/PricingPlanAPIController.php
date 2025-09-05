<?php

namespace Modules\Saas\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Company\Company;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Saas\Entities\PlanFeature;
use Modules\Saas\Entities\PlanDurationType;
use Modules\Saas\Entities\PricingPlanPrice;
use App\Models\coreApp\Setting\CompanyConfig;
use Modules\Saas\Enums\PricingPlanDurationType;
use Modules\Saas\Http\Requests\UpgradePlanRequest;
use Modules\Saas\Services\Frontend\CheckoutService;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;

class PricingPlanAPIController extends Controller
{
    use ApiReturnFormatTrait;
    protected $checkoutService;


    public function __construct()
    {
        $this->checkoutService = new CheckoutService;
    }
    
    public function planFeatures()
    {
        try {
            $planFeatures = PlanFeature::where('status_id', 1)->pluck('title', 'id');

            return $this->responseWithSuccess(_trans('message.Success'), $planFeatures);

        } catch (\Throwable $th) {
            return $this->responseWithError(_trans('message.Something went wrong'), $th->getMessage());
        }
    }
    
    public function pricingPlans()
    {
        try {
            $planDurationTypes  = PlanDurationType::query()
                                ->where('status_id', 1)
                                ->whereHas('pricingPlanPrices', function ($q) {
                                    $q->where('status_id', 1)
                                    ->whereHas('pricingPlan', fn ($q) => $q->where('status_id', 1));
                                })
                                ->with(['pricingPlanPrices' => function ($q) {
                                    $q->where('status_id', 1)
                                    ->whereHas('pricingPlan', fn ($q) => $q->where('status_id', 1))
                                    ->with(['pricingPlan' => function ($q) {
                                        $q->where('status_id', 1)
                                        ->select('id', 'name', 'employee_limit', 'is_employee_limit', 'basic_price', 'is_popular');
                                    }])
                                    ->select('id', 'pricing_plan_id', 'plan_duration_type_id', 'price');
                                }])
                                ->get(['id', 'name']);

            return $this->responseWithSuccess(_trans('message.Success'), $planDurationTypes);

        } catch (\Throwable $th) {
            return $this->responseWithError(_trans('message.Something went wrong'), $th->getMessage());
        }
    }

    public function pricingPlanDetails($pricing_plan_price_id)
    {
        try {
            $pricingPlanPrice   = PricingPlanPrice::query()
                                ->where('id', $pricing_plan_price_id)
                                ->whereHas('pricingPlan', fn ($q) => $q->where('status_id', 1))
                                ->whereHas('planDurationType', fn ($q) => $q->where('status_id', 1))
                                ->with(['pricingPlan' => function ($q) {
                                    $q->where('status_id', 1);
                                }])
                                ->with(['planDurationType' => function ($q) {
                                    $q->where('status_id', 1);
                                }])
                                ->first();

            $data = [];

            if ($pricingPlanPrice) {
                $data['pricing_plan_price_id']  = $pricing_plan_price_id;
                $data['plan_id']                = @$pricingPlanPrice->pricing_plan_id;
                $data['plan_name']              = @$pricingPlanPrice->pricingPlan->name;
                $data['duration_type']          = @$pricingPlanPrice->planDurationType->name;
                $data['employee_limit']         = @$pricingPlanPrice->pricingPlan->is_employee_limit ? @$pricingPlanPrice->pricingPlan->employee_limit : _trans('common.Unlimited');
                $data['is_popular']             = @$pricingPlanPrice->pricingPlan->is_popular ? _trans('common.Yes') : _trans('common.No');

                if (PricingPlanDurationType::QUARTERLY == @$pricingPlanPrice->planDurationType->name) {
                    $data['basic_price'] = number_format(@$pricingPlanPrice->pricingPlan->basic_price * 3, 2);
                } elseif (PricingPlanDurationType::HALF_YEARLY == @$pricingPlanPrice->planDurationType->name) {
                    $data['basic_price'] = number_format(@$pricingPlanPrice->pricingPlan->basic_price * 6, 2);
                } elseif (PricingPlanDurationType::YEARLY == @$pricingPlanPrice->planDurationType->name) {
                    $data['basic_price'] = number_format(@$pricingPlanPrice->pricingPlan->basic_price * 12, 2);
                } else {
                    $data['basic_price'] = number_format(@$pricingPlanPrice->pricingPlan->basic_price, 2);
                }

                $data['price']          = @$pricingPlanPrice->price;
                $data['currency']       = @CompanyConfig::where('key', 'currency_symbol')->first()->value;

                $planFeatures           = PlanFeature::query()
                                        ->whereHas('pricingPlanFeatures', fn ($q) => $q->where('pricing_plan_id', @$pricingPlanPrice->pricing_plan_id))
                                        ->where('status_id', 1)
                                        ->pluck('title', 'id');

                $data['features']       = $planFeatures;
            }

            return $this->responseWithSuccess(_trans('message.Success'), $data);

        } catch (\Throwable $th) {
            return $this->responseWithError(_trans('message.Something went wrong'), $th->getMessage());
        }
    }

    public function upgradePlan(UpgradePlanRequest $request, $subdomain)
    {
        try {
            $company    = Company::query()
                        ->where(function ($q) use ($subdomain) {
                            $q->where('subdomain', $subdomain)
                            ->orWhere('subdomain', $subdomain . '.' . @base_settings('company_domain'));
                        })
                        ->first();

            if (!$company) {
                return $this->responseWithError(_trans('message.Company not found!'));
            }

            $request->request->add(['subdomain' => $company->subdomain]);

            if ($request->payment_gateway == 'Stripe') {
                $this->checkoutService->payWithStripe($request);
            }
            
            $this->checkoutService->storeSubscriptionCompany($request);

            $subscription = $this->checkoutService->storeSubscription($request);

            $pricingPlanPrice = PricingPlanPrice::where('id', $subscription->pricing_plan_price_id)->with('pricingPlan')->first();

            $subscription['subscription_id_in_main_company'] = @$subscription->id;
            $subscription['plan_name'] = @$pricingPlanPrice->pricingPlan->name;

            return $this->responseWithSuccess(_trans('message.Success'), $subscription);

        } catch (\Throwable $th) {
            return $this->responseWithError(_trans('message.Something went wrong'), $th->getMessage());
        }
    }
}
