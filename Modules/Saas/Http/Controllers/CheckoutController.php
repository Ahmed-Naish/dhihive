<?php

namespace Modules\Saas\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Models\Company\Company;
use Illuminate\Routing\Controller;
use App\Models\Hrm\Country\Country;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Saas\Entities\EmailTemplate;
use Modules\Saas\Entities\PricingPlanPrice;
use Modules\Saas\Entities\SaasSubscription;
use Modules\Saas\Jobs\SubscriptionSuccessJob;
use Modules\Saas\Emails\SubscriptionSuccessMail;
use Modules\Saas\Services\Frontend\CheckoutService;
use Modules\Saas\Http\Requests\CheckoutStoreRequest;
use Modules\Saas\Repositories\SubscriptionRepository;
use Modules\Saas\Events\SendSubscriptionSuccessMailEvent;

class CheckoutController extends Controller
{
    protected $checkoutService;
    protected $subscription;
    protected $subscriptionRepository;


    public function __construct()
    {
        $this->checkoutService = new CheckoutService;
        $this->subscriptionRepository = new SubscriptionRepository(new SaasSubscription);
    }

    public function checkoutPage(Request $request)
    {
        $data['title'] = _trans('common.Checkout');
        $data['pricingPlanPrice'] = PricingPlanPrice::with('pricingPlan', 'planDurationType')->where('id', $request->pricing_plan_price_id)->where('status_id', 1)->first();

        if (!$data['pricingPlanPrice']) {
            return redirect()->route('saas.homePage');
        }

        $data['breadcrumbs'] = [
            [
                'title' => 'Home',
                'links' => route('saas.homePage'),
                'is_last' => false,
            ],

            [
                'title' => 'Checkout',
                'links' => route('saas.checkoutPage'),
                'is_last' => true,
            ],

        ];

        $data['countries'] = Country::pluck('name', 'id');
        
        return view('saas::frontend.pages.checkout', compact('data'));

    }

    public function checkout(CheckoutStoreRequest $request)
    {
        set_time_limit(300);
        
        try {
            if ($request->payment_gateway == 'Stripe') {
                $this->checkoutService->payWithStripe($request);
            }
            
            $this->checkoutService->storeSubscriptionCompany($request);

            $this->subscription = $this->checkoutService->storeSubscription($request);

            $subscriptionSuccessTemplate = EmailTemplate::where('slug', 'subscription-success')->first();
            $content = str_replace('[name]', $request->name, $subscriptionSuccessTemplate->content);
            $content = str_replace('[saas_admin_company]', @base_settings('company_name'), $content);
            $data = [
                'email'     => @$this->subscription->company->email,
                'subject'   => $subscriptionSuccessTemplate->subject,
                'content'   => $content
            ];

            if (config('app.single_db')) {
                try {
                    Mail::to($data['email'])->send(new SubscriptionSuccessMail($data));
                } catch (\Throwable $th) {
                    Log::error('SubscriptionSuccessMail Error: ' . $th);
                }
            } else {
                event(new SendSubscriptionSuccessMailEvent($data));
            }
            
            if ($this->subscription->is_demo_checkout) {
                $this->subscriptionRepository->subscriptionApprove($this->subscription->id);
            }
        
            return redirect()->route('saas.invoice', ['invoice_id' => encrypt($this->subscription->id)]);

        } catch (\Throwable $th) {
            return redirect()->back();
        }
    }

    public function invoice($invoice_id)
    {
        try {
            $id = decrypt($invoice_id);

            $data['subscription']   = SaasSubscription::with('company')->find($id);
            $data['mainCompany']    = Company::first();

            if (!$data['subscription']) {
                return redirect()->route('saas.homePage');
            }

            return view('saas::frontend.pages.invoice', $data);
        } catch (\Throwable $th) {
            return redirect()->route('saas.homePage');
        }
    }

    public function checkIsUniqueValueByCompanyColumn(Request $request)
    {
        if ($request->ajax())
        {
            $message = '';

            if ($request->column == 'company_name') {
                $company = Company::where($request->column, $request->value)->first();
                $message = _trans('common.Company Name already Exists');
            } elseif ($request->column == 'trade_licence_number') {
                $company = Company::where($request->column, $request->value)->first();
                $message = _trans('common.Trade Licence Number already Exists');
            } elseif ($request->column == 'email') {
                $company = Company::where($request->column, $request->value)->first();
                $message = _trans('common.Email already Exists');
            } elseif ($request->column == 'phone') {
                $company = Company::where($request->column, $request->value)->first();
                $message = _trans('common.Phone already Exists');
            } elseif ($request->column == 'subdomain') {
                $company = Company::where($request->column, $request->value)->first();
                $message = _trans('common.Subdomain already Exists');
            }

            if ($request->column == 'subdomain') {
                $company = Company::where($request->column, $request->value.'.'.@base_settings('company_domain'))->first();
                $message = _trans('common.Subdomain already Exists');
            }

            if (!$company && $request->filled('column')) {
                return response()->json([
                    'status'    => 1,
                    'message'   => _trans('common.Not Found'),
                ]);
            } elseif (!$request->filled('column')) {
                return response()->json([
                    'status'    => 0,
                    'message'   => _trans('common.Column not Found'),
                ]);
            } else {
                return response()->json([
                    'status'    => 0,
                    'message'   => $message,
                ]);
            }
        }
    }
}
