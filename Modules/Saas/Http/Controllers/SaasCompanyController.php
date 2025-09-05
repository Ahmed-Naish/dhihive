<?php

namespace Modules\Saas\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\Company\Company;
use Illuminate\Routing\Controller;
use App\Models\Hrm\Country\Country;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Modules\Saas\Entities\PlanFeature;
use Modules\Saas\Entities\PlanDurationType;
use Modules\Saas\Entities\PricingPlanPrice;
use Illuminate\Contracts\Support\Renderable;
use Modules\Saas\Http\Requests\CompanyRequest;
use Modules\Saas\Enums\PricingPlanDurationType;
use Modules\Saas\Repositories\CompanyRepository;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use Modules\Saas\Http\Requests\CheckoutStoreRequest;

class SaasCompanyController extends Controller
{
    use ApiReturnFormatTrait;

    protected $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function companyList()
    {
        $data = Company::where(['status_id' => 1])->get();
        $collection = $data->map(function ($data) {
            $subdomain = $data->subdomain;

            if ($subdomain) {
                $url = 'http://' . $subdomain . '/api/V11/';
            } else {
                $url = url('/api/V11/');
            }
            return [
                'id' => $data->id,
                'company_name' => $data->company_name,
                'subdomain' => $subdomain ?? env('APP_URL'),
                'url' => $url,
            ];
        });
        return $this->responseWithSuccess('Company List', $collection, 200);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->companyRepository->table($request);
        }

        $data['title']          = _trans('common.Companies');
        $data['class']          = 'company_table';
        $data['fields']         = $this->companyRepository->fields();
        $data['checkbox']       = true;
        $data['trashListCount'] = $this->companyRepository->trashListCount();

        return view('saas::company.index', compact('data'));
    }

    public function trashList(Request $request)
    {
        if ($request->ajax()) {
            return $this->companyRepository->trashListTable($request);
        }

        $data['title']  = _trans('common.Company trash list');
        $data['class']  = 'company_table_trash_list';
        $data['fields'] = $this->companyRepository->fields();

        return view('saas::company.trash_list', compact('data'));
    }

    public function create()
    {
        try {
            if (request()->filled('pricing_plan_price_id')) {
                $data['pricingPlanPrice'] = PricingPlanPrice::with('pricingPlan', 'planDurationType')->where('id', request('pricing_plan_price_id'))->where('status_id', 1)->first();
            
                if (!$data['pricingPlanPrice']) {
                    Toastr::warning(_trans('response.Please select a plan first'), 'Warning');
                    return redirect()->route('saas.company.create');
                }
            }

            $data['title']          = _trans('common.Create Company');
            $data['url']            = route('saas.company.store');
            $data['planFeatures']   = PlanFeature::where('status_id', 1)->pluck('title', 'id');

            $data['pricingPlans']   = PlanDurationType::query()
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
                                    
            $data['countries']      = Country::pluck('name', 'id');

            if (request()->filled('pricing_plan_price_id')) {
                $data['startDate']  = Carbon::now()->format('M d, Y');

                if (PricingPlanDurationType::MONTHLY == @$data['pricingPlanPrice']->planDurationType->name) {
                    $data['endDate'] = Carbon::now()->addMonths(1)->format('M d, Y');
                } elseif (PricingPlanDurationType::QUARTERLY == @$data['pricingPlanPrice']->planDurationType->name) {
                    $data['endDate'] = Carbon::now()->addMonths(3)->format('M d, Y');
                } elseif (PricingPlanDurationType::HALF_YEARLY == @$data['pricingPlanPrice']->planDurationType->name) {
                    $data['endDate'] = Carbon::now()->addMonths(6)->format('M d, Y');
                } elseif (PricingPlanDurationType::YEARLY == @$data['pricingPlanPrice']->planDurationType->name) {
                    $data['endDate'] = Carbon::now()->addMonths(12)->format('M d, Y');
                } elseif (PricingPlanDurationType::LIFETIME == @$data['pricingPlanPrice']->planDurationType->name) {
                    $data['endDate'] = Carbon::now()->addMonths(600)->format('M d, Y');
                }
            }
            
            return view('saas::company.create', compact('data'));
        } catch (\Throwable $th) {
            return response()->json('fail');
        }
    }

    public function createModal()
    {
        try {
            $data['title'] = _trans('common.Create Company');
            $data['url'] = route('saas.company.store');
            $data['attributes'] = $this->companyRepository->createAttributes();
            @$data['button'] = _trans('common.Save');
            return view('backend.modal.create_company', compact('data'));
        } catch (\Throwable $th) {
            return response()->json('fail');
        }
    }

    public function store(CheckoutStoreRequest $request)
    {
        try {

            $this->companyRepository->storeCompany($request);
            
            $message = _trans('message.Company has been created successfully.') . ' ' . _trans('message.Company approval is currently in progress; kindly allow some time for the final approval to be processed.');
            
            Toastr::success($message, 'Success');
            return redirect()->route('saas.company.list');

        } catch (\Throwable $th) {
            Toastr::error(_trans('response.Something went wrong.'), 'Error');
            return redirect()->back();
        }
    }

    public function show($id)
    {
        return view('saas::show');
    }

    public function edit(Company $company)
    {
        try {
            $data['title'] = _trans('common.Edit Company');
            $data['url'] = route('saas.company.update', $company->id);
            $data['attributes'] = $this->companyRepository->editAttributes($company);
            @$data['button'] = _trans('common.Update');
            return view('backend.modal.create', compact('data'));
        } catch (\Throwable $th) {
            return response()->json('fail');
        }
    }

    // status change
    public function statusUpdate(Request $request)
    {
        if (demoCheck()) {
            return $this->responseWithError(_trans('message.You cannot do it for demo'), [], 400);
        }
        return $this->companyRepository->statusUpdate($request);
    }

    public function update(Request $request, $id)
    {
        try {
            $this->companyRepository->update($request, $id);
            return $this->responseWithSuccess('Company Updated Successfully', [], 200);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }

    public function delete($id)
    {
        try {
            $this->companyRepository->destroy($id);

            Toastr::success("Company deleted successfully", 'Success');
            return redirect()->route('saas.company.list');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

            Toastr::error(_trans('response.Something went wrong.'), 'Error');
            return redirect()->back();
        }
    }

    public function restore($id)
    {
        try {
            $this->companyRepository->restore($id);

            Toastr::success("Company restore successfully", 'Success');
            return redirect()->route('saas.company.list');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

            Toastr::error(_trans('response.Something went wrong.'), 'Error');
            return redirect()->back();
        }
    }

    public function permanentDelete($id)
    {
        try {
            $this->companyRepository->permanentDelete($id);

            Toastr::success("Company permanently deleted successfully", 'Success');
            return redirect()->route('saas.company.trash.list');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

            Toastr::error(_trans('response.Something went wrong.'), 'Error');
            return redirect()->back();
        }
    }
}
