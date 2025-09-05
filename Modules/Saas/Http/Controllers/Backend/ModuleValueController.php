<?php

namespace Modules\Saas\Http\Controllers\Backend;

use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Repositories\SubscriptionModuleTermRepositoryInterface;

class ModuleValueController extends Controller
{
    use ApiReturnFormatTrait;

    protected $modelRepository;
    protected $title;
    protected $viwPath;
    protected $createPath;
    protected $createModalPath;
    protected $editPath;
    protected $className;

    public function __construct(SubscriptionModuleTermRepositoryInterface $modelRepository)
    {
        $this->modelRepository = $modelRepository;
        $this->title = _trans('subscription.Subscription Module Value');
        $this->viwPath = "saas::backend.subscription.module_values.index";
        $this->createPath = "saas::backend.subscription.module_values.create";
        $this->createModalPath = "saas::backend.modal.create";
        $this->editPath = "saas::backend.subscription.module_values.edit";
        $this->className = "subscription_module_values_table";
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
        return view($this->createPath, [
            'title' => _trans('common.Add') . ' ' . $this->title,
        ]);
    }
    public function edit(Request $currency)
    {
        return view($this->editPath, [
            'title' => _trans('common.Edit') . ' ' . $this->title,
            'items' => $currency,
        ]);
    }

    public function delete(Request $moduleValue)
    {

        return $this->modelRepository->destroy($moduleValue);
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
                'url' => route('saas.subscription_module_values.store'),
                'attributes' => $this->modelRepository->createAttributes(),
                'button' => _trans('common.Save'),
            ]);
        } catch (\Throwable $th) {
            return response()->json('fail');
        }
    }

    public function store(Request $request)
    {
        try {
            if (!$request->ajax()) {
                Toastr::error(_trans('response.Please click on button!'), 'Error');
                return redirect()->back();
            }
            return $this->modelRepository->newStore($request);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }

    public function editModal($id)
    {
        $moduleCategory = $this->modelRepository->find($id);
        try {
            return view($this->createModalPath, [
                'title' => _trans('common.Edit') . ' ' . $this->title,
                'items' => $moduleCategory,
                'url' => route('saas.subscription_module_values.update', $moduleCategory->id),
                'attributes' => $this->modelRepository->editAttributes($moduleCategory),
                'button' => _trans('common.Update'),
            ]);
        } catch (\Throwable $th) {

            return response()->json('failed', 400);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            if (!$request->ajax()) {
                Toastr::error(_trans('response.Please click on button!'), 'Error');
                return redirect()->back();
            }
            return $this->modelRepository->newUpdate($request, $id);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
}
