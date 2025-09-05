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
use App\Helpers\CoreApp\Traits\AuthorInfoTrait;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Models\coreApp\Relationship\RelationshipTrait;
use Modules\Services\Repositories\MachineRepositoryInterface;

class MachineRepository implements MachineRepositoryInterface
{

    use AuthorInfoTrait,
        RelationshipTrait,
        RelationCheck,
        ApiReturnFormatTrait;

    protected  $serviceMachine;

    public function __construct(ServiceMachine $serviceMachine)
    {
        $this->serviceMachine = $serviceMachine;
    }
    public function find($id)
    {
        return $this->serviceMachine->find($id);
    }

    public function getAll()
    {
        return $this->serviceMachine::query()->get();
    }

    public function getActiveAll()
    {
        return $this->serviceMachine::query()->where('status_id', 1)->get();
    }

    function fields()
    {
        return [
            _trans('common.ID'),
            _trans('common.Name'),
            _trans('common.Brand Name'),
            _trans('common.Model Name'),
            _trans('common.Origin'),
            _trans('common.Status'),
            _trans('common.Action')
        ];
    }

    public function index()
    {
    }

    public function store($request)
    {
        $company = $this->serviceMachine->query()->create($request->all());
        $this->createdBy($company);
        return true;
    }

    function table($request)
    {
        // Log::info($request->all());
        $data = $this->serviceMachine->query()->with('status');
        $where = array();

        if ($request->search) {
            $where[] = ['machine_name', 'like', '%' . $request->search . '%'];
        }
        $data = $data
            ->where($where)
            ->orderBy('id', 'desc')
            ->paginate($request->limit ?? 2);

        return [
            'data' => $data->map(function ($data) {
                $action_button = '';
                if (hasPermission('machine_update')) {
                    $action_button .= actionButton(_trans('common.Edit'), 'mainModalOpen(`' . route('machines.edit_modal', $data->id) . '`)', 'modal');
                }
                if (hasPermission('machine_delete')) {
                    $action_button .= actionButton(_trans('common.Delete'), '__globalDelete(' . $data->id . ',`services/machines/delete/`)', 'delete');
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
                    'machine_name' => $data->machine_name,
                    'brand_id' => $data->brand->name,
                    'model_id' => $data->model->name,
                    'origin' => $data->origin,
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
        return $this->serviceMachine->find($id);
    }

    public function update($id, array $data): bool
    {
        $updateTable = $this->serviceMachine->find($id);
        $updateTable->update($data);
        $this->updatedBy($updateTable);
        return true;
    }

    public function destroy($serviceMachine)
    {
        $table_name = $this->serviceMachine->getTable();
        $foreign_id = \Illuminate\Support\Str::singular($table_name) . '_id';
        return \App\Services\Hrm\DeleteService::deleteData($table_name, $foreign_id, $serviceMachine->id);
    }

    // statusUpdate
    public function statusUpdate($request)
    {
        try {

            if (@$request->action == 'active') {
                $serviceMachine = $this->serviceMachine->whereIn('id', $request->ids)->update(['status_id' => 1]);
                return $this->responseWithSuccess(_trans('message.Activate successfully.'), $serviceMachine);
            }
            if (@$request->action == 'inactive') {
                $serviceMachine = $this->serviceMachine->whereIn('id', $request->ids)->update(['status_id' => 4]);
                return $this->responseWithSuccess(_trans('message.Inactivate successfully.'), $serviceMachine);
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
                $serviceMachine = $this->serviceMachine->whereIn('id', $request->ids)->update(['deleted_at' => now()]);
                return $this->responseWithSuccess(_trans('message.Delete successfully.'), $serviceMachine);
            } else {
                return $this->responseWithError(_trans('message.Item not found'), [], 400);
            }
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }

    function createAttributes()
    {
        $brandData = ServiceBrand::where('status_id', 1)->get();

        $brandOptions = [];

        foreach ($brandData as $brand) {
            $brandOptions[] = [
                'text' => $brand['name'],
                'value' => $brand['id'],
                'active' => false,
            ];
        }

        $attributes['brand']['options'] = $brandOptions;

        $modelData = ServiceModel::where('status_id', 1)->get();

        $modelOptions = [];

        foreach ($modelData as $model) {
            $modelOptions[] = [
                'text' => $model['name'],
                'value' => $model['id'],
                'active' => false,
            ];
        }

        $attributes['model']['options'] = $modelOptions;
        return [
            'machine_name' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'machine_name',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Name')
            ],
            'brand' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'brand',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Brand'),
                'options' => $brandOptions,
            ],           
            'origin' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'origin',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Origin')
            ],
            'model' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'model',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Model'),
                'options' => $modelOptions,
            ],
            'status' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'status',
                'class' => 'form-select select2-input ot-input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
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
            ]
        ];
    }

    function editAttributes($servciemodel)
    {
        $brandData = ServiceBrand::where('status_id', 1)->get();

        $brandOptions = [];

        foreach ($brandData as $brand) {
            $brandOptions[] = [
                'text' => $brand['name'],
                'value' => $brand['id'],
                'active' => $brand['id'] == $servciemodel->brand_id, // Preselect the value if it matches the model's currency_id
            ];
        }

        $modelData = ServiceModel::where('status_id', 1)->get();

        $modelOptions = [];

        foreach ($modelData as $model) {
            $modelOptions[] = [
                'text' => $model['name'],
                'value' => $model['id'],
                'active' => $model['id'] == $servciemodel->model_id, // Preselect the value if it matches the model's currency_id
            ];
        }
        return [
            'machine_name' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'machine_name',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Name'),
                'value' => @$servciemodel->machine_name,
            ],
            'brand' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'brand',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Brand'),
                'options' => $brandOptions,
            ],            
            'model' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'model',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Model'),
                'options' => $modelOptions,
            ],
            'origin' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'origin',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Origin'),
                'value' => @$servciemodel->origin,
            ],
            'status' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'status',
                'class' => 'form-select select2-input ot-input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
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
            ]
        ];
    }

    //new functions

    function newStore($request)
    {
        try {

            $serviceMachine = new $this->serviceMachine;
            $serviceMachine->machine_name = $request->machine_name;
            $serviceMachine->brand_id = $request->brand;
            $serviceMachine->model_id = $request->model;
            $serviceMachine->origin = $request->origin;
            $serviceMachine->status_id = $request->status;


            $serviceMachine->save();
            $this->createdBy($serviceMachine);
            return $this->responseWithSuccess(_trans('message.Store successfully.'), 200);
        } catch (\Throwable $th) {
            return $this->responseWithError(_trans('message.Something went wrong'), [], 400);
        }
    }

    function newUpdate($request, $serviceMachine)
    {
        try {
            $serviceMachine = $this->serviceMachine->where('id', $serviceMachine)->first();

            $serviceMachine = $serviceMachine;
           $serviceMachine->machine_name = $request->machine_name;
            $serviceMachine->brand_id = $request->brand;
            $serviceMachine->model_id = $request->model;
            $serviceMachine->origin = $request->origin;
            $serviceMachine->status_id = $request->status;
            $serviceMachine->save();
            $this->updatedBy($serviceMachine);
            return $this->responseWithSuccess(_trans('message.Update successfully.'), 200);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
}
