<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Saas\Entities\SaasCms;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Modules\Saas\Entities\PlanFeature;
use Modules\Saas\Entities\PricingPlan;
use Modules\Saas\Http\Requests\CmsRequest;
use Modules\Saas\Entities\PlanDurationType;
use Modules\Saas\Entities\PricingPlanPrice;
use Modules\Saas\Entities\PricingPlanFeature;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use Modules\Saas\Repositories\PricingPlansRepository;

class PricingPlanController extends Controller
{
    use ApiReturnFormatTrait;

    protected $modelRepository;
    protected $title;
    protected $viwPath;
    protected $showPath;
    protected $createPath;
    protected $createModalPath;
    protected $createDetailsPath;
    protected $createFormPath;
    protected $editPath;
    protected $className;

    public function __construct(PricingPlansRepository $modelRepository)
    {
        $this->modelRepository = $modelRepository;
        $this->title = _trans('saas.Pricing Plan');
        $this->viwPath = "saas::backend.pricing-plan.index";
        $this->showPath = "saas::backend.pricing-plan.show";
        $this->createPath = "saas::backend.pricing-plan.module.create";
        $this->createModalPath = "saas::backend.modal.create";
        $this->createFormPath = "saas::backend.pricing-plan.form";
        $this->editPath = "saas::backend.pricing-plan.module.edit";
        $this->className = "pricing_plan_datatable";
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->modelRepository->table($request);
        }
        $data = [
            'class' => $this->className,
            'fields' => $this->modelRepository->fields(),
            'checkbox' => false,
            'title' => $this->title,
        ];
        return view($this->viwPath, compact('data'));
    }

    public function create()
    {
        try{
            return view($this->createFormPath, [
                'title' => _trans('common.Add') . ' ' . $this->title,
                'url' => route('saas.pricing-plans.store'),
                'button' => _trans('common.Submit'),
                'planDurationTypes' => PlanDurationType::pluck('name', 'id'),
                'planFeatures' => PlanFeature::where('status_id', 1)->pluck('title', 'id'),
            ]);
        } catch (\Throwable $th) {
            Toastr::error(_trans('response.Something went wrong.'), 'Error');
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $employeeLimit = $request->employee_limit != '' ? $request->employee_limit : 0;

                if ($request->is_popular == 1) {
                    PricingPlan::query()->update(['is_popular' => 0]);
                }

                $pricingPlan = PricingPlan::updateOrCreate([
                    'id' => $request->pricing_plan_id
                ], [
                    'name'              => $request->name,
                    'employee_limit'    => $employeeLimit,
                    'is_employee_limit' => $employeeLimit ? 1 : 0,
                    'basic_price'       => $request->basic_price,
                    'is_popular'        => $request->is_popular != '' ? $request->is_popular : 0,
                    'status_id'         => $request->status_id,
                ]);

                $priceDurationTypes = PlanDurationType::pluck('id');
    
                foreach($priceDurationTypes as $plan_duration_type_id) {

                    $status_id = in_array($plan_duration_type_id, $request->plan_duration_type_id) ? 1 : 4;
                    
                    $price = $status_id == 1 ? @$request->pricing_plan_prices[$plan_duration_type_id] : 0;

                    $pricingPlan->pricingPlanPrices()->updateOrCreate([
                        'plan_duration_type_id' => $plan_duration_type_id,
                    ], [
                        'price'                 => $price,
                        'status_id'             => $status_id,
                    ]);
                }

                $pricingPlan->pricingPlanFeatures()->delete();

                foreach ($request->feature_ids as $feature_id) {
                    $pricingPlan->pricingPlanFeatures()->create([
                        'plan_feature_id' => $feature_id
                    ]);
                }
            });

            $message = $request->pricing_plan_id ? _trans('response.Pricing Plan has been updated successfully!') : _trans('response.Pricing Plan has been created successfully!');
            Toastr::success($message, 'Success');
            return redirect()->route('saas.pricing-plans.index');

        } catch (\Throwable $th) {
            Toastr::error(_trans('response.Something went wrong.'), 'Error');
            return redirect()->back();
        }
    }
  
    public function show(Request $request, $id)
    {
        $data['title']                  = _trans('saas.Pricing Plan Detail');
        $data['planFeatures']           = PlanFeature::where('status_id', 1)->pluck('title', 'id');
        $data['pricingPlan']            = PricingPlan::with(['pricingPlanPrices' => fn ($q) => $q->with('planDurationType', 'status')])->find($id);
        $data['pricingPlanFeatures']    = PricingPlanFeature::where('pricing_plan_id', $id)->pluck('plan_feature_id')->toArray();

        return view($this->showPath, compact('data'));
    }

    public function edit($id)
    {
        try {
            return view('saas::backend.pricing-plan.form', [
                'title' => _trans('common.Edit') . ' ' . $this->title,
                'url' => route('saas.pricing-plans.store'),
                'button' => _trans('common.Update'),
                'pricingPlan' => PricingPlan::with(['pricingPlanPrices' => fn ($q) => $q->with('planDurationType', 'status'), 'pricingPlanFeatures'])->find($id),
                'planDurationTypes' => PlanDurationType::pluck('name', 'id'),
                'planFeatures' => PlanFeature::where('status_id', 1)->pluck('title', 'id'),
            ]);
        } catch (\Throwable $th) {
            return response()->json('failed', 400);
        }
    }


    public function delete(Request $request, $id)
    {
        try {
            $result = $this->modelRepository->destroy($request, $id);
            if ($result->original['result']) {
                Toastr::success($result->original['message'], 'Success');
                return redirect()->route('saas.pricing-plans.index');
            } else {
                Toastr::error($result->original['message'], 'Error');
                return redirect()->back();
            }
        } catch (\Throwable $th) {
            Toastr::error(_trans('response.Something went wrong.'), 'Error');
            return redirect()->back();
        }
    }


    public function isPlanNameExist(Request $request)
    {
        if ($request->ajax()) {
            $pricingPlan = PricingPlan::where('name', $request->name)->first();

            if ($pricingPlan && $request->prev_name == '') {
                return response()->json(['status' => 1]);
            } elseif ($pricingPlan && $request->prev_name != '' && $request->prev_name != $pricingPlan->name) {
                return response()->json(['status' => 1]);
            } else {
                return response()->json(['status' => 0]);
            }
        }
    }
}
