<?php

namespace Modules\Services\Http\Controllers;

use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Models\coreApp\Relationship\RelationshipTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Services\Entities\ServiceMachine;
use Modules\Services\Entities\ServicePackage;
use Modules\Services\Entities\ServicePackageDetail;
use Modules\Services\Repositories\PackageDetailsRepositoryInterface;

class ServicePackageDetailController extends Controller
{
    use RelationshipTrait,
        ApiReturnFormatTrait;

    protected $servicePackageRepo;
    protected $title;
    protected $viwPath;
    protected $createPath;
    protected $createModalPath;
    protected $editPath;
    protected $className;

    public function __construct(PackageDetailsRepositoryInterface $servicePackageRepo)
    {
        $this->servicePackageRepo = $servicePackageRepo;
        $this->title = _trans('services.Package List');
        $this->viwPath = "services::backend.package_details.index";
        $this->createPath = "services::backend.package_details.create";
        $this->createModalPath = "services::backend.modal.create";
        $this->editPath = "services::backend.model.index.edit";
        $this->className = "service_package_details_table";
    }

    public function singleView($id, Request $request)
    {
        $request['id'] = $id;
        if ($request->ajax()) {
            return $this->servicePackageRepo->table($request);
        }
        $data = [
            'class' => $this->className,
            'fields' => $this->servicePackageRepo->fields(),
            'checkbox' => true,
            'title' => $this->title,
            'package' => ServicePackage::find($id),
            'id' => $id
        ];
        return view($this->viwPath, compact('data'));
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
    public function create()
    {

        try {
            $data['title'] = _trans('common.Create') . ' ' . $this->title;
            $data['machines'] = ServiceMachine::where('status_id', 1)->with('model', 'brand')->get();
            return view($this->createPath, compact('data'));

        } catch (\Throwable $th) {
            dd($th);
            return response()->json('fail');
        }
    }
    public function store(Request $request)
    {
        try {
            $servicePackage = new ServicePackage();
            $servicePackage->name = $request->input('name');
            $servicePackage->package_no = 'SP-' . time();
            $servicePackage->contract_date = $request->input('contract_date');
            $servicePackage->save();

            foreach ($request->machine_ids as $key => $machine) {
                $mh = ServiceMachine::find($machine);

                $s = ServicePackageDetail::create([
                    'package_id' => $servicePackage->id,
                    'machine_id' => $machine,
                    'brand_id' => $mh->brand_id,
                    'model_id' => $mh->model_id,
                    'origin' => $mh->origin,
                    'quantity' => $request->qty[$key],
                    'warranty_period' => $request->warranty_period[$key],
                ]);
            }

            return redirect()->route('packageDetails.singleView', $servicePackage->id)->with('success', 'Service package created successfully.');
        } catch (\Throwable $th) {
            dd($th);
            return redirect()->route('package.index')->with('error', 'Something went wrong.');
        }
    }
    public function createModal($id)
    {
        try {
            return view($this->createModalPath, [
                'title' => _trans('common.Create') . ' ' . $this->title,
                'url' => route('packageDetails.newStore',$id),
                'attributes' => $this->servicePackageRepo->createAttributes(),
                'button' => _trans('common.Save')
            ]);
        } catch (\Throwable $th) {
            return response()->json('fail');
        }
    }
    public function newStore($id,Request $request)
    {
        $request['id'] = $id;
   
        try {
            if (!$request->ajax()) {
                Toastr::error(_trans('response.Please click on button!'), 'Error');
                return redirect()->back();
            }
           
            return $this->servicePackageRepo->newStore($id,$request);
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
                'url' => route('packageDetails.update', $servicePackage->id),
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
            dd($th);
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
    public function delete(ServicePackageDetail $servicePackageDetails)
    {

        return $this->servicePackageRepo->destroy($servicePackageDetails);
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
