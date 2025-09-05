<?php

namespace Modules\Services\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\TaskManagement\Task;
use App\Http\Requests\ServiceModuleReqeust;
use Illuminate\Contracts\Support\Renderable;
use Modules\Services\Entities\ModuleService;
use Modules\Services\Entities\ServiceMachine;
use Modules\Services\Entities\ServicePackage;
use Modules\Services\Entities\ServiceInstitution;
use Modules\Services\Entities\ModuleServiceDetail;
use Modules\Services\Entities\ServicePackageDetail;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Models\coreApp\Relationship\RelationshipTrait;
use Modules\Services\Repositories\ServiceRepositoryInterface;

class ModuleServiceController extends Controller
{
    use RelationshipTrait,
        ApiReturnFormatTrait;

    protected  $moduleServiceRepo;
    protected $title;
    protected $viwPath;
    protected $createPath;
    protected $createModalPath;
    protected $editPath;
    protected $className;
    protected $classViewName;
    protected $detailsViwPath;

    public function __construct(ServiceRepositoryInterface $moduleServiceRepo)
    {
        $this->moduleServiceRepo = $moduleServiceRepo;
        $this->title = _trans('services.Module Service');
        $this->viwPath = "services::backend.service.index";
        $this->detailsViwPath = "services::backend.service.service_details";
        $this->createPath = "services::backend.service.create";
        $this->createModalPath = "services::backend.modal.create";
        $this->editPath = "services::backend.model.index.edit";
        $this->className = "service_module_service_table";
        $this->classViewName = "service_module_service_details_table";
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
    public function serviceView(Request $request)
    {
        if ($request->ajax()) {
            return $this->moduleServiceRepo->table($request);
        }
        $data = [
            'class' => $this->classViewName,
            'fields' => $this->moduleServiceRepo->fields(),
            'checkbox' => true,
            'title' => $this->title,
        ];
        return view($this->detailsViwPath, compact('data'));
    }
    public function createModal()
    {
        try {
            return view($this->createModalPath, [
                'title' => _trans('common.Create') . ' ' . $this->title,
                'url' => route('services.store'),
                'attributes' => $this->moduleServiceRepo->createAttributes(),
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
            $moduleService->date = $request->date;
            $moduleService->save();

            foreach ($request->machine_ids as $key => $machine) {

                $moduleServiceDetails = new ModuleServiceDetail();
                $moduleServiceDetails->module_service_id = $moduleService->id;
                $moduleServiceDetails->machine_id = $machine;
                $moduleServiceDetails->installation_date = $request->installation_date[$key];
                $moduleServiceDetails->contract_person_id = $request->user_ids[$key];
                $moduleServiceDetails->serial_number = $request->serial_number[$key];
                $moduleServiceDetails->supply_date = $request->supply_date[$key];
                $moduleServiceDetails->save();

                
                // Create a new task associated with the module service
                $task = new Task();
                $task->service_id = $moduleServiceDetails->id; 
                $task->name =
                "The " . $moduleServiceDetails->service->package->name . " 's installation date is " . $moduleServiceDetails->installation_date . " at the " . $moduleServiceDetails->service->institution->name . " and the machine is " . $moduleServiceDetails->machine->machine_name;

                $task->date = $moduleServiceDetails->installation_date; 
                $task->save();
                // for assign member task
                DB::table('task_members')->insert([
                    'task_id' => $task->id,
                    'company_id' => getCurrentCompany(),
                    'user_id' => $moduleServiceDetails->contract_person_id,
                    'added_by' => auth()->id(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            


            return redirect()->route('serviceDetails.view', $moduleServiceDetails->module_service_id)->with('success', 'Service package created successfully.');
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
                'url' => route('services.update', $servicePackage->id),
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
    public function delete(ModuleService $moduleService)
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


}
