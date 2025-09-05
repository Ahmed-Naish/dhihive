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
use Modules\Services\Entities\ServicePackage;
use App\Helpers\CoreApp\Traits\AuthorInfoTrait;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Models\coreApp\Relationship\RelationshipTrait;
use Modules\Services\Repositories\PackageRepositoryInterface;

class PackageRepository implements PackageRepositoryInterface
{

    use AuthorInfoTrait,
        RelationshipTrait,
        RelationCheck,
        ApiReturnFormatTrait;

    protected  $servicePackage;

    public function __construct(ServicePackage $servicePackage)
    {
        $this->servicePackage = $servicePackage;
    }
    public function find($id)
    {
        return $this->servicePackage->find($id);
    }

    public function getAll()
    {
        return $this->servicePackage::query()->get();
    }

    public function getActiveAll()
    {
        return $this->servicePackage::query()->where('status_id', 1)->get();
    }

    function fields()
    {
        return [
            _trans('common.ID'),
            _trans('common.Package No'),
            _trans('common.Name'),
            _trans('common.Contract Date'),
            _trans('common.Status'),
            _trans('common.Action')
        ];
    }

    public function index()
    {
    }

    public function store($request)
    {
        $company = $this->servicePackage->query()->create($request->all());
        $this->createdBy($company);
        return true;
    }

    function table($request)
    {
        // Log::info($request->all());
        $data = $this->servicePackage->query()->with('status');
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
                if (hasPermission('package_update')) {
                $action_button .= '<a href="' . route('packageDetails.singleView', $data->id) . '" class="dropdown-item"> ' . _trans('services.Package Details') . '</a>';

                    $action_button .= actionButton(_trans('common.Edit'), 'mainModalOpen(`' . route('packages.edit_modal', $data->id) . '`)', 'modal');
                }
                if (hasPermission('package_delete')) {
                    $action_button .= actionButton(_trans('common.Delete'), '__globalDelete(' . $data->id . ',`services/packages/delete/`)', 'delete');
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
                    'package_no' => $data->package_no,
                    'name' => $data->name,
                    'contract_date' => $data->contract_date,
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
                $action_button .= '<a href="' . route('packageDetails.singleView', $data->id) . '" class="dropdown-item"> ' . _trans('services.Package Details') . '</a>';
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
        return $this->servicePackage->find($id);
    }

    public function update($id, array $data): bool
    {
        $updateTable = $this->servicePackage->find($id);
        $updateTable->update($data);
        $this->updatedBy($updateTable);
        return true;
    }

    public function destroy($servicePackage)
    {
        $table_name = $this->servicePackage->getTable();
        $foreign_id = \Illuminate\Support\Str::singular($table_name) . '_id';
        return \App\Services\Hrm\DeleteService::deleteData($table_name, $foreign_id, $servicePackage->id);
    }

    // statusUpdate
    public function statusUpdate($request)
    {
        try {

            if (@$request->action == 'active') {
                $servicePackage = $this->servicePackage->whereIn('id', $request->ids)->update(['status_id' => 1]);
                return $this->responseWithSuccess(_trans('message.Activate successfully.'), $servicePackage);
            }
            if (@$request->action == 'inactive') {
                $servicePackage = $this->servicePackage->whereIn('id', $request->ids)->update(['status_id' => 4]);
                return $this->responseWithSuccess(_trans('message.Inactivate successfully.'), $servicePackage);
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
                $servicePackage = $this->servicePackage->whereIn('id', $request->ids)->update(['deleted_at' => now()]);
                return $this->responseWithSuccess(_trans('message.Delete successfully.'), $servicePackage);
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
            'name' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'name',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Name')
            ],

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
            'contract_date' => [
                'field' => 'input',
                'type' => 'date',
                'required' => true,
                'id' => 'contract_date',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Contract Date')
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
            'expiration_date' => [
                'field' => 'input',
                'type' => 'date',
                'required' => true,
                'id' => 'expiration_date',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Expiration Date')
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
            'name' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'name',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Name'),
                'value' => @$servciemodel->name,
            ],
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
                'label' => _trans('common.Quantity'),
                'value' => @$servciemodel->quantity,
            ],
            'contract_date' => [
                'field' => 'input',
                'type' => 'date',
                'required' => true,
                'id' => 'contract_date',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Contract Date'),
                'value' => @$servciemodel->contract_date,
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
            'expiration_date' => [
                'field' => 'input',
                'type' => 'date',
                'required' => true,
                'id' => 'expiration_date',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Expiration Date'),
                'value' => @$servciemodel->expiration_date,
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

    function newStore($request)
    {


        try {

            $servicePackage = new $this->servicePackage;
            $servicePackage->machine_id = $request->machine_name;
            $servicePackage->brand_id = $request->brandInput;
            $servicePackage->model_id = $request->modelInput;
            $servicePackage->origin = $request->origin;
            $servicePackage->name = $request->name;
            $servicePackage->quantity = $request->quantity;
            $servicePackage->contract_date = $request->contract_date;
            $servicePackage->warranty_period = $request->warranty_period;
            $servicePackage->expiration_date = $request->expiration_date;
            $servicePackage->status_id = $request->status;


            $servicePackage->save();
            $this->createdBy($servicePackage);
            return $this->responseWithSuccess(_trans('message.Store successfully.'), 200);
        } catch (\Throwable $th) {
            return $this->responseWithError(_trans('message.Something went wrong'), [], 400);
        }
    }

    function newUpdate($request, $servicePackage)
    {
        try {
            $servicePackage = $this->servicePackage->where('id', $servicePackage)->first();

            $servicePackage = $servicePackage;
            $servicePackage->machine_id = $request->machine_name;
            $servicePackage->brand_id = $request->brandInput;
            $servicePackage->model_id = $request->modelInput;
            $servicePackage->origin = $request->origin;
            $servicePackage->name = $request->name;
            $servicePackage->quantity = $request->quantity;
            $servicePackage->contract_date = $request->contract_date;
            $servicePackage->warranty_period = $request->warranty_period;
            $servicePackage->expiration_date = $request->expiration_date;
            $servicePackage->status_id = $request->status;
            $servicePackage->save();
            $this->updatedBy($servicePackage);
            return $this->responseWithSuccess(_trans('message.Update successfully.'), 200);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
}
