<?php

namespace Modules\Saas\Http\Controllers\Single;
 
use GuzzleHttp\Client;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\Company\Company;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Modules\Saas\Entities\PricingPlanPrice;
use Modules\Saas\Http\Requests\UpgradePlanRequest;
use Modules\Saas\Services\Frontend\CheckoutService;


class SingleCompanyPricingPlanConroller extends Controller
{

    protected $checkoutService;
    public function __construct()
    {
        $this->checkoutService = new CheckoutService;
        if (env('APP_24hourworx')) {
            abort(404);
        }
    }

    public function upgradePlan()
    {
        if (
            !isMainCompany() && 
            config('app.mood') == 'Saas' && 
            isModuleActive('Saas') && 
            checkSingleCompanyIsDeactivated()
        ) {
            return redirect()->route('single-company.deactivated');
        }

        try {
            $data['title']          = _trans('common.Upgrade Plan');
            $data['planFeatures']   = fetchDataViaAPI(env("APP_URL") . '/api/saas/main-company/plan-features');
            $data['pricingPlans']   = fetchDataViaAPI(env("APP_URL") . '/api/saas/main-company/pricing-plans');

            return view('saas::backend.subscription.plan.upgrade-plan', $data);
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }

    public function upgradePlanModal($pricing_plan_price_id)
    {
        $data['title']                  = _trans('common.Upgrade Plan Payment');
        $data['pricingPlan']            = fetchDataViaAPI(env("APP_URL") . '/api/saas/main-company/pricing-plans-details/' . $pricing_plan_price_id);
        $data['offlinePaymentTypes']    = fetchDataViaAPI(env("APP_URL") . '/api/saas/main-company/offline-payment-types');
        $data['stripeToken']            = decrypt(fetchDataViaAPI(env("APP_URL") . '/api/saas/main-company/stripe-token'));

        return view('saas::backend.subscription.plan.modal', $data);
    }

    public function upgradePlanStore(UpgradePlanRequest $request)
    {
        try {
           if(config('app.single_db')){
                $subdomain  = Company::where('id',auth()->user()->company_id)->first()->subdomain;
                $company    = Company::query()
                            ->where(function ($q) use ($subdomain) {
                                $q->where('subdomain', $subdomain)
                                ->orWhere('subdomain', $subdomain . '.' . @base_settings('company_domain'));
                            })
                            ->first();
                $request->request->add(['subdomain' => $company->subdomain]);
                if ($request->payment_gateway == 'Stripe') {
                    $this->checkoutService->payWithStripe($request);
                }
                
                $this->checkoutService->storeSubscriptionCompany($request);
                $subscription = $this->checkoutService->storeSubscription($request);
                $pricingPlanPrice = PricingPlanPrice::where('id', $subscription->pricing_plan_price_id)->with('pricingPlan')->first();
                Toastr::success(_trans('message.Upgrade Plan request sent successfully.'), 'Success');
               return redirect()->route('single-company.subscriptions.invoice', ['id' => $subscription->id]);
                
           }
           else{
            try {
                    $subdomain      = Company::first()->subdomain;
                    $client         = new Client();
                    $apiUrl         = env("APP_URL") . '/api/saas/main-company/upgrade-plan/' . $subdomain;
                    $response       = $client->request('POST', $apiUrl, [
                                        'form_params' => $request->except('_token'),
                                    ]);
        
                    $responseBody   = $response->getBody()->getContents();
                    $data           = json_decode($responseBody, true);
            
                    $result         = [];
        
                    $subscription   = null;
            
                    if (@$data['result']) {
                        $result = $data['data'];
        
                        $subscription = Subscription::create([
                            'subscription_id_in_main_company' => $result['subscription_id_in_main_company'],
                            'invoice_no'        => $result['invoice_no'],
                            'plan_name'         => $result['plan_name'],
                            'price'             => $result['price'],
                            'payment_gateway'   => $result['payment_gateway'],
                            'trx_id'            => $result['trx_id'],
                            'offline_payment'   => $result['offline_payment'],
                            'employee_limit'    => $result['employee_limit'],
                            'is_employee_limit' => $result['is_employee_limit'],
                            'expiry_date'       => $result['expiry_date'],
                            'features'          => $result['features'],
                            'features_key'      => $result['features_key'],
                            'is_demo_checkout'  => $result['is_demo_checkout'],
                            'source'            => $result['source'],
                            'status_id'         => $result['status_id'],
                            'payment_status_id' => $result['payment_status_id'],
                        ]);
                    }
        
                    
                } catch (\Throwable $th) {
                    Toastr::error(_trans('response.Something went wrong.'), 'Error');
                    return redirect()->back();
                }

           }

           Toastr::success(_trans('message.Upgrade Plan request sent successfully.'), 'Success');
            return redirect()->route('single-company.subscriptions.invoice', ['id' => $subscription->id]);
        } catch (\Throwable $th) {
            dd($th);
            Toastr::error(_trans('response.Something went wrong.'), 'Error');
            return redirect()->back();
        }
    }
}