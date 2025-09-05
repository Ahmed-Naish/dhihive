<?php

namespace Modules\Saas\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use App\Http\Requests\SubscriptionModuleRequest;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use Modules\Saas\Entities\SubscriptionModule;
use Modules\Saas\Entities\SubscriptionModuleTerms;
use Modules\Saas\Entities\SubscriptionModuleValue;
use Modules\Saas\Repositories\SubscriptionModuleRepositoryInterface;
use Modules\Saas\Repositories\SubscriptionModuleDetailsRepositoryInterface;
use Modules\Saas\Entities\SubscriptionModuleDetails;

class ModuleController extends Controller
{
    use ApiReturnFormatTrait;

    protected $modelRepository;
    protected $modelDetailsRepository;
    protected $title;
    protected $viwPath;
    protected $createPath;
    protected $createModalPath;
    protected $createDetailsPath;
    protected $createFormPath;
    protected $editPath;
    protected $className;

    public function __construct(SubscriptionModuleRepositoryInterface $modelRepository, SubscriptionModuleDetailsRepositoryInterface $modelDetailsRepository)
    {
        $this->modelRepository = $modelRepository;
        $this->modelDetailsRepository = $modelDetailsRepository;
        $this->title = _trans('subscription.Subscription Module');
        $this->viwPath = "saas::backend.subscription.module.index";
        $this->createPath = "saas::backend.subscription.module.create";
        $this->createDetailsPath = "saas::backend.subscription.module.details";
        $this->createModalPath = "saas::backend.modal.create";
        $this->createFormPath = "saas::backend.form.create";
        $this->editPath = "saas::backend.subscription.module.edit";
        $this->className = "subscription_module_datatable";
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->modelRepository->table($request);
        }
        $data = [
            'class' => $this->className,
            'fields' => $this->modelRepository->fields(),
            'checkbox' => true,
            'title' => $this->title,
        ];
        return view($this->viwPath, compact('data'));
    }

    public function create()
    {

        try {
            return view($this->createFormPath, [
                'title' => _trans('common.Add') . ' ' . $this->title,
                'url' => route('saas.subscription_modules.store'),
                'attributes' => $this->modelRepository->createAttributes(),
                'button' => _trans('common.Save'),
            ]);
        } catch (\Throwable $th) {
            Toastr::error(_trans('response.Something went wrong.'), 'Error');
            return redirect()->back();
        }
    }


    public function edit($id)
    {
        $module = $this->modelRepository->find($id);
        try {
            return view($this->createFormPath, [
                'title' => _trans('common.Edit') . ' ' . $this->title,
                'items' => $module,
                'url' => route('saas.subscription_modules.update', $module->id),
                'attributes' => $this->modelRepository->editAttributes($module),
                'button' => _trans('common.Update'),
            ]);
        } catch (\Throwable $th) {

            return response()->json('failed', 400);
        }
    }
    public function assign($id)
    {
        $data['module_details'] = SubscriptionModuleDetails::where('module_id', $id)->first();
        $data['module'] = SubscriptionModule::where('id', $id)->first();
        // dd($data['module']->id);
        $data['title'] = _trans('subscription.Module Details');
        $data['module-terms'] = SubscriptionModuleTerms::where('status_id', 1)->get();
        $data['module-values'] = SubscriptionModuleValue::where('status_id', 1)->get();
        // dd($data);
        return view('saas::backend.subscription.module.details', compact('data'));
    }
    public function assignStore(Request $request, $id)
    {
        $module_id = $id;
        $moduleTermIds = $request->input('module_term_id');
        $moduleValueIds = $request->input('module_value_id');

        foreach ($moduleTermIds as $key => $moduleTermId) {

            $moduleDetails = new SubscriptionModuleDetails();
            $moduleDetails->module_id = $module_id;
            $moduleDetails->module_term_id = $moduleTermIds[$key];
            $moduleDetails->module_value_id = $moduleValueIds[$key];
            $moduleDetails->save();
        }

        Toastr::success(_trans('response.Module details stored successfully'), 'Success');
        return redirect()->route('saas.subscription_modules.index');
    }


    public function delete(SubscriptionModule $module)
    {

        return $this->modelRepository->destroy($module);
    }

    public function statusUpdate(Request $request)
    {
        return $this->modelRepository->statusUpdate($request);
    }

    public function deleteData(Request $request)
    {
        return $this->modelRepository->destroyAll($request);
    }

    public function createModal()
    {
        try {
            return view($this->createModalPath, [
                'title' => _trans('common.Create') . ' ' . $this->title,
                'url' => route('saas.subscription_products.store'),
                'attributes' => $this->modelRepository->createAttributes(),
                'button' => _trans('common.Save'),
            ]);
        } catch (\Throwable $th) {
            return response()->json('fail');
        }
    }

    public function store(SubscriptionModuleRequest $request)
    {
        try {

            $result = $this->modelRepository->newStore($request);

            if ($result->original['result']) {
                Toastr::success($result->original['message'], 'Success');
                return redirect()->route('saas.subscription_modules.index');
            } else {
                Toastr::error($result->original['message'], 'Error');
                return redirect()->back();
            }
        } catch (\Throwable $th) {
            Toastr::error(_trans('response.Something went wrong.'), 'Error');
            return redirect()->back();
        }
    }

    public function editModal($id)
    {
        $module = $this->modelRepository->find($id);
        try {
            return view($this->createModalPath, [
                'title' => _trans('common.Edit') . ' ' . $this->title,
                'items' => $module,
                'url' => route('saas.subscription_modules.update', $module->id),
                'attributes' => $this->modelRepository->editAttributes($module),
                'button' => _trans('common.Update'),
            ]);
        } catch (\Throwable $th) {

            return response()->json('failed', 400);
        }
    }

    public function update(SubscriptionModuleRequest $request, $id)
    {

        try {

            $result = $this->modelRepository->newUpdate($request, $id);
            if ($result->original['result']) {
                Toastr::success($result->original['message'], 'Success');
                return redirect()->route('saas.subscription_modules.index');
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
