<?php

namespace Modules\Services\Repositories;

use function route;
use App\Models\User;
use function datatables;
use function actionButton;
use function start_end_datetime;
use Illuminate\Support\Facades\DB;
use App\Models\TaskManagement\Task;
use App\Models\Traits\RelationCheck;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Hrm\Designation\Designation;
use Modules\Services\Entities\ModuleService;
use Modules\Services\Entities\ServicePackage;
use App\Helpers\CoreApp\Traits\AuthorInfoTrait;
use Modules\Services\Entities\ServiceInstitution;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Models\coreApp\Relationship\RelationshipTrait;
use Modules\Services\Repositories\ServiceRepositoryInterface;

class ServiceRepository implements ServiceRepositoryInterface
{

    use AuthorInfoTrait,
        RelationshipTrait,
        RelationCheck,
        ApiReturnFormatTrait;

    protected  $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }
    public function find($id)
    {
        return $this->moduleService->find($id);
    }

    public function getAll()
    {
        return $this->moduleService::query()->get();
    }

    public function getActiveAll()
    {
        return $this->moduleService::query()->where('status_id', 1)->get();
    }

    function fields()
    {
        return [
            _trans('common.ID'),
            _trans('common.Institution Name'),
            _trans('common.Package Name'),
            _trans('common.Date'),
            _trans('common.Status'),
            _trans('common.Action')
        ];
    }

    public function index()
    {
    }

    public function store($request)
    {
        $company = $this->moduleService->query()->create($request->all());
        $this->createdBy($company);
        return true;
    }

    function table($request)
    {
        // Log::info($request->all());
        $data = $this->moduleService->query()->with('status');

        if ($request->search) {
            $data = $data
                ->whereRelation('institution', 'name', 'Like', '%' . $request->search . '%');
        }
        $data = $data
            ->orderBy('id', 'desc')
            ->paginate($request->limit ?? 2);
        return [
            'data' => $data->map(function ($data) {
                $action_button = '';
                if (hasPermission('service_update')) {
                    $action_button .= '<a href="' . route('serviceDetails.view', $data->id) . '" class="dropdown-item"> ' . _trans('services.Service Details') . '</a>';
                    $action_button .= actionButton(_trans('common.Edit'), 'mainModalOpen(`' . route('services.edit_modal', $data->id) . '`)', 'modal');
                }
                if (hasPermission('service_delete')) {
                    $action_button .= actionButton(_trans('common.Delete'), '__globalDelete(' . $data->id . ',`services/module-services/delete/`)', 'delete');
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
                    'institution_id' => @$data->institution->name,
                    'package_id' => @$data->package->name,
                    'date' => @$data->date,

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
        $moduleService = $this->moduleService->query()->with('status');
        if (@$request->from && @$request->to) {
            $moduleService = $moduleService->whereBetween('created_at', start_end_datetime($request->from, $request->to));
        }
        if (@$id) {
            $moduleService = $moduleService->where('id', $id);
        }

        return datatables()->of($moduleService->orderBy('id', 'DESC')->get())
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
        return $this->moduleService->find($id);
    }

    public function update($id, array $data): bool
    {
        $updateTable = $this->moduleService->find($id);
        $updateTable->update($data);
        $this->updatedBy($updateTable);
        return true;
    }

    public function destroy($moduleService)
    {
        $table_name = $this->moduleService->getTable();
        $foreign_id = \Illuminate\Support\Str::singular($table_name) . '_id';

        return \App\Services\Hrm\DeleteService::deleteData($table_name, $foreign_id, $moduleService->id);
    }

    // statusUpdate
    public function statusUpdate($request)
    {
        try {

            if (@$request->action == 'active') {
                $moduleService = $this->moduleService->whereIn('id', $request->ids)->update(['status_id' => 1]);
                return $this->responseWithSuccess(_trans('message.Activate successfully.'), $moduleService);
            }
            if (@$request->action == 'inactive') {
                $moduleService = $this->moduleService->whereIn('id', $request->ids)->update(['status_id' => 4]);
                return $this->responseWithSuccess(_trans('message.Inactivate successfully.'), $moduleService);
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
                $moduleService = $this->moduleService->whereIn('id', $request->ids)->update(['deleted_at' => now()]);
                return $this->responseWithSuccess(_trans('message.Delete successfully.'), $moduleService);
            } else {
                return $this->responseWithError(_trans('message.Item not found'), [], 400);
            }
        } catch (\Throwable $th) {

            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }

    function createAttributes()
    {
        $packageData = ServicePackage::where('status_id', 1)->get();

        $packageOptions = [];

        foreach ($packageData as $package) {
            $packageOptions[] = [
                'text' => $package['name'],
                'value' => $package['id'],
                'active' => false,
            ];
        }

        $attributes['package']['options'] = $packageOptions;

        $userData = User::where('status_id', 1)->get();

        $userOptions = [];

        foreach ($userData as $user) {
            $userOptions[] = [
                'text' => $user['name'],
                'value' => $user['id'],
                'active' => false,
            ];
        }

        $attributes['user']['options'] = $userOptions;


        $institutionData = ServiceInstitution::where('status_id', 1)->get();

        $institutionOptions = [];

        foreach ($institutionData as $institution) {
            $institutionOptions[] = [
                'text' => $institution['name'],
                'value' => $institution['id'],
                'active' => false,
            ];
        }

        $attributes['institution']['options'] = $institutionOptions;

        return [
            'institution' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'institution',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Institution'),
                'options' => $institutionOptions,
            ],
            'package' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'package',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Package'),
                'options' => $packageOptions,
            ],
            'contract_person_id' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'contract_person_id',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.user'),
                'options' => $userOptions,
            ],
            'installation_date' => [
                'field' => 'input',
                'type' => 'date',
                'required' => true,
                'id' => 'installation_date',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Installation Date')
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
        $packageData = ServicePackage::where('status_id', 1)->get();

        $packageOptions = [];

        foreach ($packageData as $package) {
            $packageOptions[] = [
                'text' => $package['name'],
                'value' => $package['id'],
                'active' => $package['id'] == $servciemodel->package_id, // Preselect the value if it matches the model's currency_id
            ];
        }
        $institutionData = ServiceInstitution::where('status_id', 1)->get();

        $institutionOptions = [];

        foreach ($institutionData as $institution) {
            $institutionOptions[] = [
                'text' => $institution['name'],
                'value' => $institution['id'],
                'active' => $institution['id'] == $servciemodel->institution_id, // Preselect the value if it matches the model's currency_id
            ];
        }

        $userData = User::where('status_id', 1)->get();

        $userOptions = [];

        foreach ($userData as $user) {
            $userOptions[] = [
                'text' => $user['name'],
                'value' => $user['id'],
                'active' => $user['id'] == $servciemodel->contract_person_id, // Preselect the value if it matches the model's currency_id
            ];
        }
        return [
            'institution' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'institution',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Institution'),
                'options' => $institutionOptions,
            ],
            'package' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'package',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Package'),
                'options' => $packageOptions,
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
            $moduleService = new $this->moduleService;
            $moduleService->institution_id = $request->institution;
            $moduleService->package_id = $request->package;
            $moduleService->date = $request->date;
            $moduleService->status_id = $request->status;

            $moduleService->save();

            // Create a new task record
            $task = new Task();
            $task->service_id = $moduleService->id;
            $task->name = "The " . $moduleService->package->name . " 's installation date is " . $moduleService->installation_date . " at the " . $moduleService->institution->name;
            $task->date                     = date('Y-m-d');
            $task->start_date               = $moduleService->installation_date;
            $task->end_date                 = $moduleService->installation_date;
            $task->company_id               = getCurrentCompany();
            $task->created_by               = auth()->id();

            $task->save();
            // for assign member task
            DB::table('task_members')->insert([
                'task_id' => $task->id,
                'company_id' => getCurrentCompany(),
                'user_id' => $moduleService->contract_person_id,
                'added_by' => auth()->id(),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $this->responseWithSuccess(_trans('message.Store successfully'), 200);
        } catch (\Throwable $th) {
            return $this->responseWithError(_trans('message.Something went wrong'), [], 400);
        }
    }

    function newUpdate($request, $moduleServiceId)
    {
        try {

            // Retrieve the moduleService record to update
            $moduleService = ModuleService::find($moduleServiceId);

            // Update the moduleService with the new data
            $moduleService->institution_id = $request->institution;
            $moduleService->package_id = $request->package;
            $moduleService->date = $request->date;
            $moduleService->status_id = $request->status;
            $moduleService->save();


            return $this->responseWithSuccess(_trans('message.Update successfully.'), 200);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
}
