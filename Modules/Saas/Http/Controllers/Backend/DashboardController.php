<?php

namespace Modules\Saas\Http\Controllers\Backend;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Company\Company;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Saas\Entities\PricingPlan;
use Modules\Saas\Entities\SaasSubscription;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = _trans('common.Dashboard');
        $data['totalCompany'] = Company::where('id', '!=', 1)->count();
        $data['totalSubscription'] = SaasSubscription::count();
        $data['totalActiveSubscription'] = SaasSubscription::where('status_id', 5)->count();
        $data['totalPlan'] = PricingPlan::count();

        $data['total_subscription_status_pending'] = SaasSubscription::where('status_id', 2)->count();
        $data['total_subscription_status_approve'] = SaasSubscription::where('status_id', 5)->count();
        $data['total_subscription_status_reject'] = SaasSubscription::where('status_id', 6)->count();

        $currentYear = Carbon::now()->year;
        $data['monthly_company_earnings'] = DB::table(function ($query) use ($currentYear) {
            $query->select(
                DB::raw('COALESCE(SUM(price), 0) as total_earnings'),
                DB::raw('MONTH(created_at) as month_number')
            )
            ->from('saas_subscriptions')
            ->whereYear('created_at', $currentYear)
            ->groupBy(DB::raw('MONTH(created_at)'));
        }, 'monthly_data')
        ->rightJoin(DB::raw('(SELECT 1 as month_number UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12) as months'), function ($join) {
            $join->on('monthly_data.month_number', '=', 'months.month_number');
        })
        ->select('months.month_number', 'monthly_data.total_earnings')
        ->orderBy('months.month_number')
        ->get();

        // dd($data['monthly_company_earnings']);
        return view('saas::backend.dashboard.index', compact('data'));
    }
}
