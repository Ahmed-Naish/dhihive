<?php

namespace Modules\Saas\Repositories;

use Illuminate\Support\Str;
use App\Services\Hrm\DeleteService;
use App\Models\Traits\RelationCheck;
use App\Helpers\CoreApp\Traits\FileHandler;
use App\Helpers\CoreApp\Traits\AuthorInfoTrait;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Models\coreApp\Relationship\RelationshipTrait;
use Modules\Saas\Entities\SubscriptionProduct;
use Modules\Saas\Repositories\SubscriptionProductRepositoryInterface;

class SubscriptionProductRepository implements SubscriptionProductRepositoryInterface
{

    use AuthorInfoTrait, RelationshipTrait, RelationCheck, ApiReturnFormatTrait, FileHandler;
    protected $model;

    public function __construct(SubscriptionProduct $model)
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
            _trans('common.Icon'),
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
                if (hasPermission('model_update')) {
                    $action_button .= '<a href="' . route('saas.subscription_products.edit', [$data->id]) . '" class="dropdown-item">' . _trans('common.Edit') . '</a>';
                }
                if (hasPermission('model_delete')) {
                    $action_button .= actionButton(_trans('common.Delete'), '__globalDelete(' . $data->id . ',`admin/saas/products/delete/`)', 'delete');
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
                $product_icon = '';
                $product_icon .= '<img data-toggle="tooltip" data-placement="top" title="' . $data->icon . '" src="' . uploaded_asset($data->icon_id) . '" class="staff-profile-image-small" >';

                return [
                    'id' => $data->id,
                    // `title`, `code`, `symbol`, `exchange_rate`, `position`, `precision`, `status_id`
                    'title' => @$data->title,
                    'icon' => @$product_icon,
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
                if (hasPermission('subscription_currency_edit')) {
                    $action_button .= '<a href="' . route('saas.subscription_currencies.edit', $data->id) . '" class="dropdown-item"> ' . $edit . '</a>';
                }
                if (hasPermission('designation_delete')) {
                    $action_button .= actionButton($delete, '__globalDelete(' . $data->id . ',`admin/saas/currencies/delete/`)', 'delete');
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
            'slug' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'slug',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Slug'),
            ],
            'icon' => [
                'field' => 'input',
                'type' => 'file',
                'required' => false,
                'id' => 'icon',
                'placeholder' => 'Icon',
                'class' => 'form-control ot_input ot-form-control mt-0',
                'col' => 'col-md-12 form-group  mb-3',
                'label' => _trans('common.Icon'),
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
            'slug' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'slug',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Slug'),
                'value' => @$model->slug,
            ],
            'icon' => [
                'field' => 'input',
                'type' => 'file',
                'required' => false,
                'id' => 'icon',
                'placeholder' => 'Icon',
                'class' => 'form-control ot_input ot-form-control mt-0',
                'col' => 'col-md-12 form-group  mb-3',
                'label' => _trans('common.Icon'),
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
            $model->slug = $request->slug;
            $model->status_id = $request->status;
            if ($request->hasFile('icon')) {
                $model->icon_id = $this->uploadImage($request->icon, 'uploads/icon')->id;
            }

            $model = setCompanyIdBranchId($model);
            $model->save();
            $this->createdBy($model);

            return $this->responseWithSuccess(_trans('message.Store successfully.'), 200);
        } catch (\Throwable $th) {
            dd($th);
            return $this->responseWithError(_trans('message.Something went wrong'), [], 400);
        }
    }

    public function newUpdate($request, $id)
    {

        try {
            $params = [
                'id' => $id,
            ];
            $model = $this->model->where('id', $params)->first();

            if ($this->isExistsWhenUpdate($model, $this->model, 'title', $request->title)) {
                $model = $model;
                $model->title = $request->title;
                $model->slug = $request->slug;
                $model->status_id = $request->status;
                if ($request->hasFile('icon')) {
                    if (@$model->icon_id) {
                        $this->deleteImage(asset_path($model->icon_id));
                    }
                    $model->icon_id = $this->uploadImage($request->icon, 'uploads/icon')->id;
                }
                $model->save();
                $this->updatedBy($model);
                return $this->responseWithSuccess(_trans('message.Update successfully.'), 200);
            } else {
                return $this->responseWithError(_trans('message.Data already exists'), [], 400);
            }
        } catch (\Throwable $th) {
            dd($th);
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
}
