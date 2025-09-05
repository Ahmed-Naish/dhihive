<?php

namespace Modules\Services\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Modules\Services\Entities\ServiceModel;
use Modules\Services\Repository\ModelRepository;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Http\Requests\ServiceReqeust;
use App\Models\coreApp\Relationship\RelationshipTrait;
use Modules\Services\Repositories\ModelRepositoryInterface;

class ServiceModelController extends Controller
{
    use RelationshipTrait,
        ApiReturnFormatTrait;

    protected  $serviceModelRepo;
    protected $title;
    protected $viwPath;
    protected $createPath;
    protected $createModalPath;
    protected $editPath;
    protected $className;

    public function __construct(ModelRepositoryInterface $serviceModelRepo)
    {
        $this->serviceModelRepo = $serviceModelRepo;
        $this->title = _trans('services.Service Model');
        $this->viwPath = "services::backend.model.index";
        $this->createPath = "services::backend.model.index.create";
        $this->createModalPath = "services::backend.modal.create";
        $this->editPath = "services::backend.model.index.edit";
        $this->className = "service_model_table";
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->serviceModelRepo->table($request);
        }
        $data = [
            'class' => $this->className,
            'fields' => $this->serviceModelRepo->fields(),
            'checkbox' => true,
            'title' => $this->title,
        ];
        return view($this->viwPath, compact('data'));
    }
    public function createModal()
    {
        try {
            return view($this->createModalPath, [
                'title' => _trans('common.Create') . ' ' . $this->title,
                'url' => route('models.store'),
                'attributes' => $this->serviceModelRepo->createAttributes(),
                'button' => _trans('common.Save'),
            ]);
        } catch (\Throwable $th) {
            return response()->json('fail');
        }
    }
    public function store(ServiceReqeust $request)
    {
        try {
            if (!$request->ajax()) {
                Toastr::error(_trans('response.Please click on button!'), 'Error');
                return redirect()->back();
            }
            return $this->serviceModelRepo->newStore($request);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
    public function editModal( $id)
    {
        $serviceModel = $this->serviceModelRepo->find($id);
        try {
            return view($this->createModalPath, [
                'title' => _trans('common.Edit') . ' ' . $this->title,
                'items' => $serviceModel,
                'url' => route('models.update', $serviceModel->id),
                'attributes' => $this->serviceModelRepo->editAttributes($serviceModel),
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
            return $this->serviceModelRepo->newUpdate($request, $id);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
    public function delete(ServiceModel $serviceModel)
    {

        return $this->serviceModelRepo->destroy($serviceModel);
    }

    public function statusUpdate(Request $request)
    {
        return $this->serviceModelRepo->statusUpdate($request);
    }

    public function deleteData(Request $request)
    {
        return $this->serviceModelRepo->destroyAll($request);
    }
}
