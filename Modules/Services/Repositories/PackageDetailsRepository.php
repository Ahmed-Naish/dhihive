<?php

namespace Modules\Services\Repositories;

use function route;
use function datatables;
use function actionButton;
use function start_end_datetime;
use App\Models\Traits\RelationCheck;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Hrm\Designation\Designation;
use Modules\Services\Entities\ServiceBrand;
use Modules\Services\Entities\ServiceModel;
use Modules\Services\Entities\ServiceMachine;
use Modules\Services\Entities\ServicePackageDetails;
use App\Helpers\CoreApp\Traits\AuthorInfoTrait;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Models\coreApp\Relationship\RelationshipTrait;
use Modules\Services\Entities\ServicePackageDetail;
use Modules\Services\Repositories\PackageDetailsRepositoryInterface;

class PackageDetailsRepository implements PackageDetailsRepositoryInterface
{

    use AuthorInfoTrait,
        RelationshipTrait,
        RelationCheck,
        ApiReturnFormatTrait;

    protected  $servicePackageDetails;

    public function __construct(ServicePackageDetail $servicePackageDetails)
    {
        $this->servicePackageDetails = $servicePackageDetails;
    }
    public function find($id)
    {
        return $this->servicePackageDetails->find($id);
    }

    public function getAll()
    {
        return $this->servicePackageDetails::query()->get();
    }

    public function getActiveAll()
    {
        return $this->servicePackageDetails::query()->where('status_id', 1)->get();
    }

    function fields()
    {
        return [
            _trans('common.ID'),
            _trans('common.Machine '),
            _trans('common.Model '),
            _trans('common.Brand '),
            _trans('common.Origin'),
            _trans('common.Quantity'),
            _trans('common.Warranty Period'),
            _trans('common.Status'),
            _trans('common.Action')
        ];
    }

    public function index()
    {
    }

    public function store($request)
    {
        $company = $this->servicePackageDetails->query()->create($request->all());
        $this->createdBy($company);
        return true;
    }

    function table($request)
    {
        $data = $this->servicePackageDetails->query()->with('status');
        if($request->id != ""){
            $data = $data->where('package_id', $request->id);
        }
        $where = array();

        if ($request->search) {
            $where[] = ['name', 'like', '%' . $request->search . '%'];
        }
        $data = $data
            ->where($where)
            ->orderBy('id', 'desc')
            ->paginate($request->limit ?? 2);



        return [
            'data' => $data->map(function ($data) {
                $action_button = '';
                if (hasPermission('designation_update')) {
                    $action_button .= actionButton(_trans('common.Edit'), 'mainModalOpen(`' . route('packageDetails.edit_modal', $data->id) . '`)', 'modal');
                }
                if (hasPermission('designation_delete')) {
                    $action_button .= actionButton(_trans('common.Delete'), '__globalDelete(' . $data->id . ',`services/package-details/delete/`)', 'delete');
                }
                $button = ' <div class="dropdown dropdown-action">
                        <button type="button" class="btn-dropdown" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-ellipsis"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                        ' . $action_button . '
                        </ul>
                    </div>';

                return [
                    'id' => $data->id,
                    'machine_id' => @$data->machine->machine_name,
                    'model_id' => @$data->model->name,
                    'brand_id' => @$data->brand->name,
                    'origin' => $data->origin,
                    'quantity' => $data->quantity,
                    'warranty_period' => $data->warranty_period,
                    'status' => '<small class="badge badge-' . @$data->status->class . '">' . @$data->status->name . '</small>',
                    'action' => $button
                ];
            }),
            'pagination' => [
                'total' => $data->total(),
                'count' => $data->count(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'total_pages' => $data->lastPage(),
                'pagination_html' => $data->links('backend.pagination.custom')->toHtml(),
            ],
        ];
    }

    public function dataTable($request, $id = null)
    {
        $serviceBrand = $this->serviceBrand->query()->with('status');
        if (@$request->from && @$request->to) {
            $serviceBrand = $serviceBrand->whereBetween('created_at', start_end_datetime($request->from, $request->to));
        }
        if (@$id) {
            $serviceBrand = $serviceBrand->where('id', $id);
        }

        return datatables()->of($serviceBrand->orderBy('id', 'DESC')->get())
            ->addColumn('action', function ($data) {
                $action_button = '';
                $edit = _trans('common.Edit');
                $delete = _trans('common.Delete');
                if (hasPermission('designation_update')) {
                    $action_button .= '<a href="' . route('models.edit', $data->id) . '" class="dropdown-item"> ' . $edit . '</a>';
                }
                if (hasPermission('designation_delete')) {
                    $action_button .= actionButton($delete, '__globalDelete(' . $data->id . ',`hrm/designation/delete/`)', 'delete');
                }
                $button = getActionButtons($action_button);
                return $button;
            })
            ->addColumn('title', function ($data) {
                return @$data->title;
            })
            ->addColumn('status', function ($data) {
                return '<span class="badge badge-' . @$data->status->class . '">' . @$data->status->name . '</span>';
            })
            ->rawColumns(array('title', 'status', 'action'))
            ->make(true);
    }

    public function show($id)
    {
        return $this->servicePackageDetails->find($id);
    }

    public function update($id, array $data): bool
    {
        $updateTable = $this->servicePackageDetails->find($id);
        $updateTable->update($data);
        $this->updatedBy($updateTable);
        return true;
    }

    public function destroy($servicePackageDetails)
    {
        $table_name = $this->servicePackageDetails->getTable();
        $foreign_id = \Illuminate\Support\Str::singular($table_name) . '_id';
        return \App\Services\Hrm\DeleteService::deleteData($table_name, $foreign_id, $servicePackageDetails->id);
    }

    // statusUpdate
    public function statusUpdate($request)
    {
        try {

            if (@$request->action == 'active') {
                $servicePackageDetails = $this->servicePackageDetails->whereIn('id', $request->ids)->update(['status_id' => 1]);
                return $this->responseWithSuccess(_trans('message.Activate successfully.'), $servicePackageDetails);
            }
            if (@$request->action == 'inactive') {
                $servicePackageDetails = $this->servicePackageDetails->whereIn('id', $request->ids)->update(['status_id' => 4]);
                return $this->responseWithSuccess(_trans('message.Inactivate successfully.'), $servicePackageDetails);
            }
            return $this->responseWithError(_trans('message.Operation failed'), [], 400);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }

    public function destroyAll($request)
    {
        try {
            if (@$request->ids) {
                $servicePackageDetails = $this->servicePackageDetails->whereIn('id', $request->ids)->update(['deleted_at' => now()]);
                return $this->responseWithSuccess(_trans('message.Delete successfully.'), $servicePackageDetails);
            } else {
                return $this->responseWithError(_trans('message.Item not found'), [], 400);
            }
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }

    function createAttributes()
    {

        $machineData = ServiceMachine::where('status_id', 1)->get();

        $machineOptions = [];

        foreach ($machineData as $machine) {
            $machineOptions[] = [
                'text' => $machine['machine_name'],
                'value' => $machine['id'],
                'active' => false,
            ];
        }

        $attributes['machine']['options'] = $machineOptions;
        return [
  
            'machine_name' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'machineSelect',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Machine'),
                'options' => $machineOptions,
            ],
            'brandNameInput' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'brandInput',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Brand Name'),
                'readonly' => true
            ],
            'modelNameInput' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'modelInput',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.model Name'),
                'readonly' => true
            ],
            'origin' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'origin',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Origin'),
                'readonly' => true
            ],
            'quantity' => [
                'field' => 'input',
                'type' => 'number',
                'required' => true,
                'id' => 'quantity',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Quantity')
            ],
            'warranty_period' => [
                'field' => 'input',
                'type' => 'number',
                'required' => true,
                'id' => 'warranty_period',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Warranty Period'),
            ],
            'status' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'status',
                'class' => 'form-select select2-input ot-input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mt-3',
                'label' => _trans('common.Status'),
                'options' => [
                    [
                        'text' => _trans('payroll.Active'),
                        'value' => 1,
                        'active' => true,
                    ],
                    [
                        'text' => _trans('payroll.Inactive'),
                        'value' => 4,
                        'active' => false,
                    ]
                ]
            ],
            'brandInput' => [
                'field' => 'input',
                'type' => 'hidden',
            ],
            'modelInput' => [
                'field' => 'input',
                'type' => 'hidden',
            ]
        ];
    }

    function editAttributes($servciemodel)
    {

        $machineData = ServiceMachine::where('status_id', 1)->get();

        $machineOptions = [];

        foreach ($machineData as $machine) {
            $machineOptions[] = [
                'text' => $machine['machine_name'],
                'value' => $machine['id'],
                'active' => $machine['id'] == $servciemodel->machine_id, // Preselect the value if it matches the model's currency_id
            ];
        }
        return [
            'machine_id' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'machineSelect',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Machine'),
                'options' => $machineOptions,
            ],
            'brandNameInput' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'brandInput',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Brand Name'),
                'readonly' => true
            ],
            'modelNameInput' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'modelInput',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.model Name'),
                'readonly' => true
            ],
            'origin' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'origin',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Origin'),
                'readonly' => true
            ],
            'quantity' => [
                'field' => 'input',
                'type' => 'number',
                'required' => true,
                'id' => 'quantity',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Quantity'),
                'value' => @$servciemodel->quantity,
            ],
            'warranty_period' => [
                'field' => 'input',
                'type' => 'number',
                'required' => true,
                'id' => 'warranty_period',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Warranty Period'),
                'value' => @$servciemodel->warranty_period,
            ],
            'status' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'status',
                'class' => 'form-select select2-input ot-input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mt-3',
                'label' => _trans('common.Status'),
                'options' => [
                    [
                        'text' => _trans('payroll.Active'),
                        'value' => 1,
                        'active' => $servciemodel->status_id == 1 ? true : false,
                    ],
                    [
                        'text' => _trans('payroll.Inactive'),
                        'value' => 4,
                        'active' => $servciemodel->status_id == 4 ? true : false,
                    ]
                ]
            ],
            'brandInput' => [
                'field' => 'input',
                'type' => 'hidden',
            ],
            'modelInput' => [
                'field' => 'input',
                'type' => 'hidden',
            ]
        ];
    }

    //new functions

    function newStore($id,$request)
    {
        try {

            $servicePackageDetails = new $this->servicePackageDetails;
            $servicePackageDetails->package_id = $id;
            $servicePackageDetails->machine_id = $request->machine_name;
            $servicePackageDetails->brand_id = $request->brandInput;
            $servicePackageDetails->model_id = $request->modelInput;
            $servicePackageDetails->origin = $request->origin;
            $servicePackageDetails->quantity = $request->quantity;
            $servicePackageDetails->warranty_period = $request->warranty_period;
            $servicePackageDetails->status_id = $request->status;
   

            $servicePackageDetails->save();

            $this->createdBy($servicePackageDetails);
            return $this->responseWithSuccess(_trans('message.Store successfully.'), 200);
        } catch (\Throwable $th) {
            return $this->responseWithError(_trans('message.Something went wrong'), [], 400);
        }
    }

    function newUpdate($request, $servicePackageDetails)
    {
        try {
            $servicePackageDetails = $this->servicePackageDetails->where('id', $servicePackageDetails)->first();

            $servicePackageDetails = $servicePackageDetails;
            $servicePackageDetails->machine_id = $request->machine_id;
            $servicePackageDetails->brand_id = $request->brandInput;
            $servicePackageDetails->model_id = $request->modelInput;
            $servicePackageDetails->origin = $request->origin;
            $servicePackageDetails->quantity = $request->quantity;
            $servicePackageDetails->warranty_period = $request->warranty_period;
            $servicePackageDetails->status_id = $request->status;
            $servicePackageDetails->save();
            $this->updatedBy($servicePackageDetails);
            return $this->responseWithSuccess(_trans('message.Update successfully.'), 200);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
}
