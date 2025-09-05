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
use Illuminate\Support\Facades\Artisan;
use Modules\Saas\Entities\EmailTemplate;
use Modules\Saas\Repositories\EmailTemplateRepository;

class EmailTemplateController extends Controller
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

    public function __construct(EmailTemplateRepository $modelRepository)
    {
        $this->modelRepository = $modelRepository;
        $this->title = _trans('saas.Email Template');
        $this->viwPath = "saas::backend.email-template.index";
        $this->showPath = "saas::backend.email-template.show";
        $this->createPath = "saas::backend.email-template.module.create";
        $this->createModalPath = "saas::backend.modal.create";
        $this->createFormPath = "saas::backend.email-template.create";
        $this->editPath = "saas::backend.email-template.edit";
        $this->className = "email_template_datatable";
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
                'url' => route('saas.email-template.store'),
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
            $result = $this->modelRepository->store($request);
            if ($result->original['result']) {
                Toastr::success($result->original['message'], 'Success');
                return redirect()->route('saas.email-template.index');
            } else {
                Toastr::error($result->original['message'], 'Error');
                return redirect()->back();
            }
        } catch (\Throwable $th) {
            Toastr::error(_trans('response.Something went wrong.'), 'Error');
            return redirect()->back();
        }
    }
  
    public function show(Request $request, $id)
    {
        $data['title']                  = _trans('saas.Email Template Detail');
        $data['template']               = EmailTemplate::find($id);
        return view($this->showPath, compact('data'));
    }

    public function edit($id)
    {
        try {
            return view('saas::backend.email-template.edit', [
                'title' => _trans('common.Edit') . ' ' . $this->title,
                'url' => route('saas.email-template.update', $id),
                'button' => _trans('common.Update'),
                'template' => EmailTemplate::find($id),
            ]);
        } catch (\Throwable $th) {
            return response()->json('failed', 400);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $result = $this->modelRepository->update($request, $id);
            
            Artisan::call('optimize:clear');

            if ($result->original['result']) {
                Toastr::success($result->original['message'], 'Success');
                return redirect()->route('saas.email-template.index');
            } else {
                Toastr::error($result->original['message'], 'Error');
                return redirect()->back();
            }
        } catch (\Throwable $th) {
            Toastr::error(_trans('response.Something went wrong.'), 'Error');
            return redirect()->back();
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $result = $this->modelRepository->destroy($request, $id);
            if ($result->original['result']) {
                Toastr::success($result->original['message'], 'Success');
                return redirect()->route('saas.email-template.index');
            } else {
                Toastr::error($result->original['message'], 'Error');
                return redirect()->back();
            }
        } catch (\Throwable $th) {
            Toastr::error(_trans('response.Something went wrong.'), 'Error');
            return redirect()->back();
        }
    }
}
