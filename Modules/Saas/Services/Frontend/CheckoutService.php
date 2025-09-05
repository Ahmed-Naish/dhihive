<?php

namespace Modules\Saas\Services\Frontend;

use Stripe\Charge;
use Stripe\Stripe;
use App\Models\Company\Company;
use Modules\Saas\Entities\PricingPlanPrice;
use Modules\Saas\Entities\SaasSubscription;

class CheckoutService
{
    protected $company;
    protected $stripeTrxID;

    public function payWithStripe($request)
    {
        Stripe::setApiKey(@getMainCompanyStripeInfo('stripe_secret'));

        $pricingPlanPrice   = PricingPlanPrice::where('id', $request->pricing_plan_price_id)->with('pricingPlan')->first();
        $description        = 'Pay ' . $pricingPlanPrice->price . ' for ' . @$pricingPlanPrice->pricingPlan->name;
        
        $charge = Charge::create([
            "amount"        => $pricingPlanPrice->price * 100,
            "currency"      => "usd",
            "source"        => $request->stripeToken,
            "description"   => $description 
        ]);

        $this->stripeTrxID  = @$charge->balance_transaction;
    }


    public function storeSubscriptionCompany($request)
    {
        
        $this->company              = Company::firstOrCreate([
            'subdomain'             => $request->subdomain
        ], [
            'country_id'            => $request->country_id,
            'name'                  => $request->name,
            'company_name'          => $request->company_name,
            'email'                 => $request->email,
            'phone'                 => $request->phone,
            'total_employee'        => $request->total_employee,
            'business_type'         => $request->business_type,
            'trade_licence_number'  => $request->trade_licence_number,
            'status_id'             => 4,
            'is_subscription'       => 1,
        ]);
    }


    public function storeSubscription($request)
    {
        
        $pricingPlanPrice   = PricingPlanPrice::where('id', $request->pricing_plan_price_id)->with('pricingPlan.pricingPlanFeatures.planFeature')->first();
        $features           = [];
        $features_key       = [];

        foreach(@$pricingPlanPrice->pricingPlan->pricingPlanFeatures ?? [] as $pricingPlanFeature) {
            $features[]     = $pricingPlanFeature->planFeature->title;
            $features_key[] = $pricingPlanFeature->planFeature->key;
        }

        $isDemoCheckout     = $request->payment_gateway == 'Demo Checkout' ? 1 : 0;

        $offlinePayment = null;

        if ($request->payment_gateway == 'Offline Payment') {
            $offlinePayment = json_encode([
                'payment_type' => $request->offline_payment_type,
                'details' => $request->offline_payment_details,
            ]);
        }

        return SaasSubscription::create([
            'invoice_no'            => str()->upper(str()->random(8)),
            'company_id'            => $this->company->id,
            'pricing_plan_price_id' => $request->pricing_plan_price_id,
            'price'                 => $pricingPlanPrice->price,
            'payment_gateway'       => !$isDemoCheckout ? $request->payment_gateway : null,
            'trx_id'                => $request->payment_gateway == 'Stripe' ? $this->stripeTrxID : null,
            'offline_payment'       => $offlinePayment,
            'employee_limit'        => @$pricingPlanPrice->pricingPlan->employee_limit,
            'is_employee_limit'     => @$pricingPlanPrice->pricingPlan->is_employee_limit,
            'expiry_date'           => $request->expiry_date,
            'features'              => json_encode($features),
            'features_key'          => json_encode($features_key),
            'is_demo_checkout'      => $isDemoCheckout,
            'source'                => $request->source != '' ? $request->source : 'Website',
            'status_id'             => 2,
            'payment_status_id'     => $isDemoCheckout ? 9 : 8
        ]);
    }
}