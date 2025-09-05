<?php

namespace Modules\Services\Repositories;

use function route;
use function datatables;
use function actionButton;
use function start_end_datetime;
use App\Models\Traits\RelationCheck;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Hrm\Designation\Designation;
use App\Helpers\CoreApp\Traits\AuthorInfoTrait;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Models\coreApp\Relationship\RelationshipTrait;
use Modules\Services\Entities\ServiceInstitution;
use Modules\Services\Repositories\InstitutionRepositoryInterface;

class InstitutionRepository implements InstitutionRepositoryInterface
{
    use AuthorInfoTrait,
        RelationshipTrait,
        RelationCheck,
        ApiReturnFormatTrait;

    protected  $serviceInstitution;

    public function __construct(ServiceInstitution $serviceInstitution)
    {
        $this->serviceInstitution = $serviceInstitution;
    }
    public function find($id)
    {
        return $this->serviceInstitution->find($id);
    }

    public function getAll()
    {
        return $this->serviceInstitution::query()->get();
    }

    public function getActiveAll()
    {
        return $this->serviceInstitution::query()->where('status_id', 1)->get();
    }

    function fields()
    {
        return [
            _trans('common.ID'),
            _trans('common.Name'),
            _trans('common.Status'),
            _trans('common.Action')
        ];
    }

    public function index()
    {
    }

    public function store($request)
    {
        $company = $this->serviceInstitution->query()->create($request->all());
        $this->createdBy($company);
        return true;
    }

    function table($request)
    {
        // Log::info($request->all());
        $data = $this->serviceInstitution->query()->with('status');
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
                if (hasPermission('institution_update')) {
                    $action_button .= actionButton(_trans('common.Edit'), 'mainModalOpen(`' . route('institutions.edit_modal', $data->id) . '`)', 'modal');
                }
                if (hasPermission('institution_delete')) {
                    $action_button .= actionButton(_trans('common.Delete'), '__globalDelete(' . $data->id . ',`services/institutions/delete/`)', 'delete');
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
                    'name' => $data->name,
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
        $serviceInstitution = $this->serviceInstitution->query()->with('status');
        if (@$request->from && @$request->to) {
            $serviceInstitution = $serviceInstitution->whereBetween('created_at', start_end_datetime($request->from, $request->to));
        }
        if (@$id) {
            $serviceInstitution = $serviceInstitution->where('id', $id);
        }

        return datatables()->of($serviceInstitution->orderBy('id', 'DESC')->get())
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
        return $this->serviceInstitution->find($id);
    }

    public function update($id, array $data): bool
    {
        $updateTable = $this->serviceInstitution->find($id);
        $updateTable->update($data);
        $this->updatedBy($updateTable);
        return true;
    }

    public function destroy($serviceInstitution)
    {
        $table_name = $this->serviceInstitution->getTable();
        $foreign_id = \Illuminate\Support\Str::singular($table_name) . '_id';
        return \App\Services\Hrm\DeleteService::deleteData($table_name, $foreign_id, $serviceInstitution->id);
    }

    // statusUpdate
    public function statusUpdate($request)
    {
        try {

            if (@$request->action == 'active') {
                $serviceInstitution = $this->serviceInstitution->whereIn('id', $request->ids)->update(['status_id' => 1]);
                return $this->responseWithSuccess(_trans('message.Activate successfully.'), $serviceInstitution);
            }
            if (@$request->action == 'inactive') {
                $serviceInstitution = $this->serviceInstitution->whereIn('id', $request->ids)->update(['status_id' => 4]);
                return $this->responseWithSuccess(_trans('message.Inactivate successfully.'), $serviceInstitution);
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
                $serviceInstitution = $this->serviceInstitution->whereIn('id', $request->ids)->update(['deleted_at' => now()]);
                return $this->responseWithSuccess(_trans('message.Delete successfully.'), $serviceInstitution);
            } else {
                return $this->responseWithError(_trans('message.Item not found'), [], 400);
            }
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }

    function createAttributes()
    {
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
        return [
            'name' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'name',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.name'),
                'value' => @$servciemodel->name,
            ],
            'slug' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'slug',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Slug'),
                'value' => @$servciemodel->slug,
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
            $serviceInstitution = new $this->serviceInstitution;
            $serviceInstitution->name = $request->name;

            // Generate slug based on the name
            $serviceInstitution->slug = generateSlug($request->name);

            $serviceInstitution->status_id = $request->status;

            $serviceInstitution->save();
            $this->createdBy($serviceInstitution);
            return $this->responseWithSuccess(_trans('message.Store successfully.'), 200);
        } catch (\Throwable $th) {
            return $this->responseWithError(_trans('message.Something went wrong'), [], 400);
        }
    }



    function newUpdate($request, $serviceInstitution)
    {
        try {
            $serviceInstitution = $this->serviceInstitution->where('id', $serviceInstitution)->first();

            $serviceInstitution = $serviceInstitution;
            $serviceInstitution->name = $request->name;
            $serviceInstitution->slug = $request->slug;
            $serviceInstitution->status_id = $request->status;
            $serviceInstitution->save();
            $this->updatedBy($serviceInstitution);
            return $this->responseWithSuccess(_trans('message.Update successfully.'), 200);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
}
