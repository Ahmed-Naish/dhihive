<?php

namespace Modules\Services\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Modules\Services\Entities\ServiceMachine;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Http\Requests\ServiceMachineReqeust;
use App\Models\coreApp\Relationship\RelationshipTrait;
use Modules\Services\Repositories\MachineRepositoryInterface;

class ServiceMachineController extends Controller
{
    use RelationshipTrait,
        ApiReturnFormatTrait;

    protected  $serviceMachineRepo;
    protected $title;
    protected $viwPath;
    protected $createPath;
    protected $createModalPath;
    protected $editPath;
    protected $className;

    public function __construct(MachineRepositoryInterface $serviceMachineRepo)
    {
        $this->serviceMachineRepo = $serviceMachineRepo;
        $this->title = _trans('services.Service Machine');
        $this->viwPath = "services::backend.machine.index";
        $this->createPath = "services::backend.model.index.create";
        $this->createModalPath = "services::backend.modal.create";
        $this->editPath = "services::backend.model.index.edit";
        $this->className = "service_machine_table";
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->serviceMachineRepo->table($request);
        }
        $data = [
            'class' => $this->className,
            'fields' => $this->serviceMachineRepo->fields(),
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
                'url' => route('machines.store'),
                'attributes' => $this->serviceMachineRepo->createAttributes(),
                'button' => _trans('common.Save'),
            ]);
        } catch (\Throwable $th) {
            return response()->json('fail');
        }
    }
    public function store(ServiceMachineReqeust $request)
    {
        try {
            if (!$request->ajax()) {
                Toastr::error(_trans('response.Please click on button!'), 'Error');
                return redirect()->back();
            }
            return $this->serviceMachineRepo->newStore($request);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
    public function editModal( $id)
    {
        $serviceMachine = $this->serviceMachineRepo->find($id);
        try {
            return view($this->createModalPath, [
                'title' => _trans('common.Edit') . ' ' . $this->title,
                'items' => $serviceMachine,
                'url' => route('machines.update', $serviceMachine->id),
                'attributes' => $this->serviceMachineRepo->editAttributes($serviceMachine),
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
            return $this->serviceMachineRepo->newUpdate($request, $id);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
    public function delete(ServiceMachine $serviceMachine)
    {

        return $this->serviceMachineRepo->destroy($serviceMachine);
    }

    public function statusUpdate(Request $request)
    {
        return $this->serviceMachineRepo->statusUpdate($request);
    }

    public function deleteData(Request $request)
    {
        return $this->serviceMachineRepo->destroyAll($request);
    }
}
