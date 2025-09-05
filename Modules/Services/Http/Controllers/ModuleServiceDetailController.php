<?php

namespace Modules\Services\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Models\coreApp\Relationship\RelationshipTrait;
use Modules\Services\Entities\ModuleService;
use Modules\Services\Entities\ModuleServiceDetail;
use Modules\Services\Repositories\ServiceDetailsRepositoryInterface;

class ModuleServiceDetailController extends Controller
{
    use RelationshipTrait,
        ApiReturnFormatTrait;

    protected $moduleServiceRepo;
    protected $title;
    protected $viwPath;
    protected $createPath;
    protected $createModalPath;
    protected $editPath;
    protected $className;
    protected $classViewName;

    public function __construct(ServiceDetailsRepositoryInterface $moduleServiceRepo)
    {
        $this->moduleServiceRepo = $moduleServiceRepo;
        $this->title = _trans('services.Module Service Details');
        $this->viwPath = "services::backend.service_details.index";
        $this->createPath = "services::backend.service.create";
        $this->createModalPath = "services::backend.modal.create";
        $this->editPath = "services::backend.model.index.edit";
        $this->className = "service_module_service_details_table";
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->moduleServiceRepo->table($request);
        }
        $data = [
            'class' => $this->className,
            'fields' => $this->moduleServiceRepo->fields(),
            'checkbox' => true,
            'title' => $this->title,
        ];
        return view($this->viwPath, compact('data'));
    }
    public function view(Request $request, $id)
    {
        $request['id'] = $id;
        if ($request->ajax()) {
            return $this->moduleServiceRepo->table($request);
        }
        $data = [
            'class' => $this->className,
            'fields' => $this->moduleServiceRepo->fields(),
            'checkbox' => true,
            'title' => $this->title,
            'service' => ModuleService::find($id),
            'id' => $id
        ];
        return view($this->viwPath, compact('data'));
    }
    public function createModal($id)
    {
        try {
            return view($this->createModalPath, [
                'title' => _trans('common.Create') . ' ' . $this->title,
                'url' => route('serviceDetails.newStore', $id),
                'attributes' => $this->moduleServiceRepo->createAttributes($id),
                'button' => _trans('common.Save'),
            ]);
        } catch (\Throwable $th) {
            return response()->json('fail');
        }
    }
    public function store(ServiceModuleReqeust $request)
    {

        try {
            $moduleService = new ModuleService();
            $moduleService->institution_id = $request->institution;
            $moduleService->package_id = $request->package;
            $moduleService->save();

            foreach ($request->machine_ids as $key => $machine) {

                $moduleServiceDetails = new ModuleServiceDetail();
                $moduleServiceDetails->module_service_id = $moduleService->id;
                $moduleServiceDetails->machine_id = $machine;
                $moduleServiceDetails->installation_date = $request->installation_date[$key];
                $moduleServiceDetails->contract_person_id = $request->user_ids[$key];
                $moduleServiceDetails->contract_date = $request->contract_date[$key];
                $moduleServiceDetails->serial_number = $request->serial_number[$key];
                $moduleServiceDetails->supply_date = $request->supply_date[$key];
                $moduleServiceDetails->save();
            }

            return redirect()->route('services.index')->with('success', 'Service package created successfully.');
        } catch (\Throwable $th) {
            dd($th);
            return redirect()->route('services.index')->with('error', 'Something went wrong.');
        }
    }
    public function create()
    {

        try {
            $data['title'] = _trans('common.Create') . ' ' . $this->title;
            $data['institutions'] = ServiceInstitution::where('status_id', 1)->get();
            $data['packages'] = ServicePackage::where('status_id', 1)->get();
            $data['users'] = User::where('status_id', 1)->get();
            return view($this->createPath, compact('data'));
        } catch (\Throwable $th) {
            dd($th);
            return response()->json('fail');
        }
    }
    public function editModal($id)
    {
        $servicePackage = $this->moduleServiceRepo->find($id);
        try {
            return view($this->createModalPath, [
                'title' => _trans('common.Edit') . ' ' . $this->title,
                'items' => $servicePackage,
                'url' => route('serviceDetails.update', $servicePackage->id),
                'attributes' => $this->moduleServiceRepo->editAttributes($servicePackage),
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
            return $this->moduleServiceRepo->newUpdate($request, $id);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
    public function delete(ModuleServiceDetail $moduleService)
    {

        return $this->moduleServiceRepo->destroy($moduleService);
    }

    public function statusUpdate(Request $request)
    {
        return $this->moduleServiceRepo->statusUpdate($request);
    }

    public function deleteData(Request $request)
    {
        return $this->moduleServiceRepo->destroyAll($request);
    }
    public function getModels(Request $request)
    {
        $packageId = $request->input('package_id');

        $machines = ServicePackageDetail::where('package_id', $packageId)->get();

        // Create an array to store the machine names and IDs
        $machineData = [];
        foreach ($machines as $machine) {
            $machineId = $machine->machine_id;
            $machineName = ServiceMachine::where('id', $machineId)->value('machine_name');
            $machineData[] = [
                'machine_id' => $machineId,
                'machine_name' => $machineName,
            ];
        }


        return response()->json([
            'machine_data' => $machineData
        ]);
    }
    public function newStore($id, Request $request)
    {
   
        $request['id'] = $id;

        try {
            if (!$request->ajax()) {
                Toastr::error(_trans('response.Please click on button!'), 'Error');
                return redirect()->back();
            }
           
            return $this->moduleServiceRepo->newStore($id, $request);
        } catch (\Throwable $th) {
            dd($th);
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }


}
