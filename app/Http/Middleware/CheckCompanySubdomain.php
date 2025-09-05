<?php 

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckCompanySubdomain
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the application is using a single database
        if (!config('app.single_db')) {
            return $next($request);
        }

        // Check the current domain company and tenancy settings
        $currentDomainCompany = getCurrentDomainCompany();
        $centralDomains = config('tenancy.central_domains');
        $currentUrl = url('/');

        if (!in_array(url('/'), config('tenancy.central_domains'))){
            if ($currentDomainCompany &&  @$currentDomainCompany->is_main_company == 'no' && (@$currentDomainCompany->status_id == 4 || isExpiredRunningSubscription())) {

                // Check company is inactive
                if (@$currentDomainCompany->status_id == 4) {
                    abort(429);
                }

                // Check subscription was expire
                if (isExpiredRunningSubscription()) {
                    if (
                        !request()->routeIs('single-company.upgrade-plan.index') &&
                        !request()->routeIs('single-company.upgrade-plan.modal') &&
                        !request()->routeIs('single-company.upgrade-plan.store') &&
                        !request()->routeIs('single-company.subscriptions.invoice')
                    ) {
                         return response()->json([
                                'message' => _trans('common.Subscription of your company has been expired, Please contact with system admin.'),
                                'status'  => 'error'
                            ], 403);
                    }
                }
           }

           if (!$currentDomainCompany && !in_array($currentUrl, $centralDomains) && config('app.mood') === 'Saas' && isModuleActive('Saas')) {
                // If conditions are not met, abort with a 404 error
                abort(404);
            }
        }

    
        

        // Check if the authenticated user's company is deactivated
      
        

        // Proceed to the next middleware or request handler
        return $next($request);
    }
}
