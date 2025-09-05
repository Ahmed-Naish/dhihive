<?php

namespace Modules\Saas\Http\Controllers\Single;
 
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\Company\Company;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Modules\Saas\Entities\SaasSubscription;
use Modules\Saas\Repositories\SingleCompanySubscriptionRepository;


class SingleCompanySubscriptionController extends Controller
{
    protected $subscriptionRepository;

    public function __construct(SingleCompanySubscriptionRepository $subscriptionRepository)
    {
        if (env('APP_24hourworx')) {
            abort(404);
        }
        
        $this->subscriptionRepository = $subscriptionRepository;
    }
    
    public function index(Request $request)
    {
       
        if (
            !isMainCompany() && 
            config('app.mood') == 'Saas' && 
            isModuleActive('Saas') && 
            checkSingleCompanyIsDeactivated()
        ) {
            return redirect()->route('single-company.deactivated');
        }
        
        if ($request->ajax()) {
            return $this->subscriptionRepository->table($request);
        }

        $data['title']  = _trans('common.Subscriptions');
        $data['class']  = 'single_company_subscription_table';
        $data['fields'] = $this->subscriptionRepository->fields();
        
        return view('saas::backend.subscription.single-company.index', compact('data'));
    }

    public function invoice($id)
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
            if(config('app.single_db')){
                $data['subscription'] = SaasSubscription::with('company')->find($id);
            }else{
                $data['subscription'] = Subscription::with('company')->find($id);
            }
            
            $data['mainCompany'] = Company::first();
            $data['title'] = _trans('common.Invoice ' . $data['subscription']->invoice_no);
    
            // dd(getMainCompanyInfo());
    
            return view('saas::backend.subscription.single-company.invoice', $data);
        } catch (\Throwable $th) {
            
        }
    }
    
}