<?php

namespace Modules\Services\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Modules\Services\Entities\ServiceMachine;
use Modules\Services\Entities\ServicePackage;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Http\Requests\ServicePackageReqeust;
use App\Models\coreApp\Relationship\RelationshipTrait;
use Modules\Services\Repositories\PackageRepositoryInterface;

class ServicePackageController extends Controller
{
    use RelationshipTrait,
        ApiReturnFormatTrait;

    protected  $servicePackageRepo;
    protected $title;
    protected $viwPath;
    protected $createPath;
    protected $createModalPath;
    protected $editPath;
    protected $className;

    public function __construct(PackageRepositoryInterface $servicePackageRepo)
    {
        $this->servicePackageRepo = $servicePackageRepo;
        $this->title = _trans('services.Service Package');
        $this->viwPath = "services::backend.package.index";
        $this->createPath = "services::backend.package.create";
        $this->createModalPath = "services::backend.modal.create";
        $this->editPath = "services::backend.model.index.edit";
        $this->className = "service_package_table";
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->servicePackageRepo->table($request);
        }
        $data = [
            'class' => $this->className,
            'fields' => $this->servicePackageRepo->fields(),
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
                'url' => route('packages.store'),
                'attributes' => $this->servicePackageRepo->createAttributes(),
                'button' => _trans('common.Save'),
            ]);
        } catch (\Throwable $th) {
            return response()->json('fail');
        }
    }
    public function store(ServicePackageReqeust $request)
    {
        try {
            if (!$request->ajax()) {
                Toastr::error(_trans('response.Please click on button!'), 'Error');
                return redirect()->back();
            }
            return $this->servicePackageRepo->newStore($request);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
    public function editModal($id)
    {
        $servicePackage = $this->servicePackageRepo->find($id);
        try {
            return view($this->createModalPath, [
                'title' => _trans('common.Edit') . ' ' . $this->title,
                'items' => $servicePackage,
                'url' => route('packages.update', $servicePackage->id),
                'attributes' => $this->servicePackageRepo->editAttributes($servicePackage),
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
            return $this->servicePackageRepo->newUpdate($request, $id);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
    public function delete(ServicePackage $servicePackage)
    {

        return $this->servicePackageRepo->destroy($servicePackage);
    }

    public function statusUpdate(Request $request)
    {
        return $this->servicePackageRepo->statusUpdate($request);
    }

    public function deleteData(Request $request)
    {
        return $this->servicePackageRepo->destroyAll($request);
    }
    public function getBrandsAndModels(Request $request)
    {
        $machineId = $request->input('machine_id');

        $machine = ServiceMachine::find($machineId);
        $brand = $machine->brand()->get(['id', 'name']);
        $model = $machine->model()->get(['id', 'name']);
        $origin = $machine->origin;


        return response()->json([
            'brand' => $brand,
            'model' => $model,
            'origin' => $origin,
        ]);
    }
}
