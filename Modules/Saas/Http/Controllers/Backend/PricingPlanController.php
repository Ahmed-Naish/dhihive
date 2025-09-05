<?php

namespace Modules\Saas\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Models\PricingPlan;
use Modules\Saas\Repositories\PricingPlanRepository;

class PricingPlanController extends Controller
{
    use ApiReturnFormatTrait;
    protected $pricingPlanRepository;

    public function __construct(PricingPlanRepository $pricingPlanRepository)
    {
        $this->pricingPlanRepository = $pricingPlanRepository;
    }

    // list
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->pricingPlanRepository->table($request);
        }
        $data['title'] = _trans('common.Pricing Plans');
        $data['class'] = 'pricingPlan_table';
        $data['fields'] = $this->pricingPlanRepository->fields();
        $data['checkbox'] = true;
        return view('saas::price_plan.index', compact('data'));
    }

    // create
    public function create()
    {

        $data['title']         = _trans('common.Pricing Plan Create');
        $data['url']           = (hasPermission('pricePlan_store')) ? route('saas.pricePlan.store') : '';
        return view('saas::price_plan.create', compact('data'));
    }

    // store
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'subtitle' => 'required',
            'price' => 'required',
            'duration_type' => 'required',
            'is_popular' => 'required',
            'status_id' => 'required',
        ]);

        try {
            $result = $this->pricingPlanRepository->store($request);
            if ($result) {
                Toastr::success('Success message', 'Success');
                return redirect()->route('saas.pricePlan.list');
            } else {
                Toastr::error('Error message', 'Error');
                return redirect()->back();
            }
        } catch (\Throwable $th) {
            
            Toastr::error('Something went wrong.', 'Error');
            return redirect()->back();
        }
    }

    // edit
    public function edit($id)
    {
        try {
            $pricePlan = PricingPlan::findOrFail($id); // Fetch the price plan data

            $data['title'] = _trans('payroll.Edit Price Plan');
            $data['url'] = route('saas.pricePlan.update', $id);
            $data['is_permission'] = hasPermission('pricePlan_update');
            $data['edit'] = $pricePlan; // Pass the fetched price plan data to the view
            
            return view('saas::price_plan.create', compact('data'));
        } catch (\Throwable $th) {
            Toastr::error(_trans('validation.Something went wrong!'), 'Error');
            return redirect()->back();
        }
    }

    // update
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'subtitle' => 'required',
            'price' => 'required',
            'duration_type' => 'required',
            'is_popular' => 'required',
            'status_id' => 'required',
        ]);
        
        try {
            $result = $this->pricingPlanRepository->update($request, $id);
            if ($result->original['result']) {
                Toastr::success($result->original['message'], 'Success');
                return redirect()->route('saas.pricePlan.list');
            } else {
                Toastr::error($result->original['message'], 'Error');
                return redirect()->back();
            }
        } catch (\Throwable $th) {
            Toastr::error(_trans('response.Something went wrong.'), 'Error');
            return redirect()->back();
        }
    }
    
    // delete
    public function delete($id)
    {
        try {
            $result = $this->pricingPlanRepository->destroy($id);
            if ($result->original['result']) {
                Toastr::success($result->original['message'], 'Success');
                return redirect()->route('saas.pricePlan.list');
            } else {
                Toastr::error($result->original['message'], 'Error');
                return redirect()->route('saas.pricePlan.list');
            }
        } catch (\Throwable $th) {
            Toastr::error(_trans('validation.Something went wrong!'), 'Error');
            return redirect()->route('saas.pricePlan.list');
        }
    }
    
    // status change
    public function statusUpdate(Request $request)
    {
        if (demoCheck()) {
            return $this->responseWithError(_trans('message.You cannot do it for demo'), [], 400);
        }
        return $this->pricingPlanRepository->statusUpdate($request);
    }
}
