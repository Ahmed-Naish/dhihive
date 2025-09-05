<?php

namespace Modules\Saas\Http\Controllers;

use App\Models\Company\Company;
use Illuminate\Routing\Controller;
use Modules\Saas\Entities\SaasSubscription;

class ReportController extends Controller
{
    public function transactionReport()
    {
        $data['title']          = _trans('common.Transaction Report');
        $data['companies']      = Company::where('id', '!=', 1)->pluck('company_name', 'id');
        $data['transactions']   = SaasSubscription::query()
                                ->with('company', 'paymentStatus')
                                ->when(request()->filled('search'), fn ($q) => $q->where('invoice_no', request('search')))
                                ->when(request()->filled('company_id'), fn ($q) => $q->where('company_id', request('company_id')))
                                ->when(request()->filled('payment_status_id'), fn ($q) => $q->where('payment_status_id', request('payment_status_id')))
                                ->paginate(10);

        return view('saas::backend.report.transactions', compact('data'));
    }
}
