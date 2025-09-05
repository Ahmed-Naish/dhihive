<?php

namespace Modules\Saas\Repositories;

use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Helpers\CoreApp\Traits\AuthorInfoTrait;
use App\Models\coreApp\Relationship\RelationshipTrait;
use App\Models\Traits\RelationCheck;
use Modules\Saas\Entities\SubscriptionModuleCategory;
use Modules\Saas\Entities\SubscriptionModuleTerms;
use Modules\Saas\Repositories\SubscriptionModuleTermRepositoryInterface;

class SubscriptionModuleTermRepository implements SubscriptionModuleTermRepositoryInterface
{

    use AuthorInfoTrait, RelationshipTrait, RelationCheck, ApiReturnFormatTrait;
    protected $model;

    public function __construct(SubscriptionModuleTerms $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function getAll()
    {
        return $this->model::query()->get();
    }

    public function getActiveAll()
    {
        return $this->model::query()->get();
    }

    public function fields()
    {

        return [
            _trans('common.ID'),
            _trans('common.Title'),
            _trans('common.Category'),
            _trans('common.Status'),
            _trans('common.Action'),
        ];
    }

    public function index()
    {
    }

    public function store($request)
    {
        $company = $this->model->query()->create($request->all());
        $this->createdBy($company);
        return true;
    }

    public function table($request)
    {

        $data = $this->model->query()->with('status');
        $where = array();

        if ($request->search) {
            $where[] = ['title', 'like', '%' . $request->search . '%'];
        }
        $data = $data
            ->where($where)
            ->orderBy('id', 'desc')
            ->paginate($request->limit ?? 2);
        return [
            'data' => $data->map(function ($data) {
                $action_button = '';
                if (hasPermission('subscription_module_terms_update')) {
                    $action_button .= actionButton(_trans('common.Edit'), 'mainModalOpen(`' . route('saas.subscription_module_terms.edit_modal', $data->id) . '`)', 'modal');
                }
                if (hasPermission('subscription_module_terms_delete')) {
                    $action_button .= actionButton(_trans('common.Delete'), '__globalDelete(' . $data->id . ',`admin/saas/module-terms/delete/`)', 'delete');
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
                    // `title`, `code`, `symbol`, `exchange_rate`, `position`, `precision`, `status_id`
                    'title' => $data->title,
                    'category_id' => $data->moduleCategory->title,
                    'status' => '<small class="badge badge-' . @$data->status->class . '">' . @$data->status->name . '</small>',
                    'action' => $button,
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
        $model = $this->model->query()->with('status');
        if (@$request->from && @$request->to) {
            $model = $model->whereBetween('created_at', start_end_datetime($request->from, $request->to));
        }
        if (@$id) {
            $model = $model->where('id', $id);
        }

        return datatables()->of($model->orderBy('id', 'DESC')->get())
            ->addColumn('action', function ($data) {
                $action_button = '';
                $edit = _trans('common.Edit');
                $delete = _trans('common.Delete');
                if (hasPermission('subscription_module_terms_edit')) {
                    $action_button .= '<a href="' . route('saas.subscription_module_terms.edit', $data->id) . '" class="dropdown-item"> ' . $edit . '</a>';
                }
                if (hasPermission('subscription_module_terms_delete')) {
                    $action_button .= actionButton($delete, '__globalDelete(' . $data->id . ',`admin/saas/module-terms/delete/`)', 'delete');
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
        return $this->model->find($id);
    }

    public function update($id, array $data): bool
    {
        $updateTable = $this->model->find($id);
        $updateTable->update($data);
        $this->updatedBy($updateTable);
        return true;
    }

    public function destroy($model)
    {
        $table_name = $this->model->getTable();
        $foreign_id = \Illuminate\Support\Str::singular($table_name) . '_id';
        return \App\Services\Hrm\DeleteService::deleteData($table_name, $foreign_id, $model->id);
    }

    // statusUpdate
    public function statusUpdate($request)
    {
        try {

            if (@$request->action == 'active') {
                $model = $this->model->whereIn('id', $request->ids)->update(['status_id' => 1]);
                return $this->responseWithSuccess(_trans('message.Activate successfully.'), $model);
            }
            if (@$request->action == 'inactive') {
                $model = $this->model->whereIn('id', $request->ids)->update(['status_id' => 4]);
                return $this->responseWithSuccess(_trans('message.Inactivate successfully.'), $model);
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
                $model = $this->model->whereIn('id', $request->ids)->update(['deleted_at' => now()]);
                return $this->responseWithSuccess(_trans('message.Delete successfully.'), $model);
            } else {
                return $this->responseWithError(_trans('message.Item not found'), [], 400);
            }
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }

    public function createAttributes()
    {
        $moduleCategoryData = SubscriptionModuleCategory::where('status_id', 1)->get();

        $moduleCategoryOptions = [];

        foreach ($moduleCategoryData as $moduleCategory) {
            $moduleCategoryOptions[] = [
                'text' => $moduleCategory['title'],
                'value' => $moduleCategory['id'],
                'active' => false,
            ];
        }

        $attributes['category_id']['options'] = $moduleCategoryOptions;
        return [
            'title' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'title',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Title'),
            ],
            'category_id' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'category_id',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Category'),
                'options' => $moduleCategoryOptions,
            ],
            'status' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'status',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
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
                    ],
                ],
            ],
        ];
    }

    public function editAttributes($model)
    {

        $moduleCategoryData = SubscriptionModuleCategory::where('status_id', 1)->get();

        $moduleCategoryOptions = [];

        foreach ($moduleCategoryData as $moduleCategory) {
            $moduleCategoryOptions[] = [
                'text' => $moduleCategory['title'],
                'value' => $moduleCategory['id'],
                'active' => $moduleCategory['id'] == $model->category_id, // Preselect the value if it matches the model's category_id
            ];
        }
        return [
            'title' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'title',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Title'),
                'value' => @$model->title,
            ],
            'category_id' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'category_id',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Category'),
                'options' => $moduleCategoryOptions,
            ],
            'status' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'status',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Status'),
                'options' => [
                    [
                        'text' => _trans('payroll.Active'),
                        'value' => 1,
                        'active' => $model->status_id == 1 ? true : false,
                    ],
                    [
                        'text' => _trans('payroll.Inactive'),
                        'value' => 4,
                        'active' => $model->status_id == 4 ? true : false,
                    ],
                ],
            ],
        ];
    }

    //new functions

    public function newStore($request)
    {

        try {

            $model = new $this->model;
            $model->title = $request->title;
            $model->category_id = $request->category_id;
            $model->status_id = $request->status;

            $model->save();
            return $this->responseWithSuccess(_trans('message.Store successfully.'), 200);

        } catch (\Throwable $th) {

            return $this->responseWithError(_trans('message.Something went wrong'), [], 400);
        }
    }

    public function newUpdate($request, $model)
    {
        try {
            $model = $this->model->where('id', $model)->first();

            $model = $model;
            $model->title = $request->title;
            $model->category_id = $request->category_id;
            $model->status_id = $request->status;
            $model->save();
            // $this->updatedBy($model);
            return $this->responseWithSuccess(_trans('message.Update successfully.'), 200);

        } catch (\Throwable $th) {
            dd($th);
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
}
