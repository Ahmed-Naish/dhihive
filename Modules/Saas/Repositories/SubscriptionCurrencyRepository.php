<?php

namespace Modules\Saas\Repositories;

use Illuminate\Support\Str;
use App\Services\Hrm\DeleteService;
use App\Models\Traits\RelationCheck;
use App\Helpers\CoreApp\Traits\AuthorInfoTrait;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Models\coreApp\Relationship\RelationshipTrait;
use Modules\Saas\Entities\SubscriptionCurrency;
use Modules\Saas\Repositories\SubscriptionCurrencyRepositoryInterface;

class SubscriptionCurrencyRepository implements SubscriptionCurrencyRepositoryInterface
{

    use AuthorInfoTrait, RelationshipTrait, RelationCheck, ApiReturnFormatTrait;
    protected $model;

    public function __construct(SubscriptionCurrency $model)
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
            _trans('common.Code'),
            _trans('common.Symbol'),
            _trans('common.Exchange Rate'),
            _trans('common.Position'),
            _trans('common.Precision'),
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
                    $action_button .= actionButton(_trans('common.Edit'), 'mainModalOpen(`' . route('saas.subscription_currencies.edit_modal', $data->id) . '`)', 'modal');
                }
                if (hasPermission('model_delete')) {
                    $action_button .= actionButton(_trans('common.Delete'), '__globalDelete(' . $data->id . ',`admin/saas/currencies/delete/`)', 'delete');
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
                    'code' => $data->code,
                    'symbol' => $data->symbol,
                    'exchange_rate' => $data->exchange_rate,
                    'position' => $data->position,
                    'precision' => $data->precision,
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
            'code' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'code',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Code'),
            ],
            'symbol' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'symbol',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Symbol'),
            ],
            'exchange_rate' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'exchange_rate',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Exchange Rate'),
            ],
            'position' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'position',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Position'),
                'options' => [
                    [
                        'text' => _trans('subscription.Left'),
                        'value' => 1,
                        'active' => true,
                    ],
                    [
                        'text' => _trans('subscription.Right'),
                        'value' => 2,
                        'active' => false,
                    ],
                ],
            ],
            'precision' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'precision	',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Precision	'),
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
            'code' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'code',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Code'),
                'value' => @$model->code,
            ],
            'symbol' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'symbol',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Symbol'),
                'value' => @$model->symbol,
            ],
            'exchange_rate' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'exchange_rate',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Exchange Rate'),
                'value' => @$model->exchange_rate,
            ],
            'position' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'position',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Position'),
                'options' => [
                    [
                        'text' => _trans('subscription.Left'),
                        'value' => 1,
                        'active' => $model->position == 'left' ? true : false,
                    ],
                    [
                        'text' => _trans('subscription.Right'),
                        'value' => 2,
                        'active' => $model->position == 'right' ? true : false,
                    ],
                ],
            ],
            'precision' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'precision	',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Precision	'),
                'value' => @$model->precision,
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
            $model->code = $request->code;
            $model->symbol = $request->symbol;
            $model->exchange_rate = $request->exchange_rate;
            $model->position = $request->position;
            $model->precision = $request->precision;
            $model->status_id = $request->status;

            $model->save();
            $this->createdBy($model);
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
            $model->code = $request->code;
            $model->symbol = $request->symbol;
            $model->exchange_rate = $request->exchange_rate;
            $model->position = $request->position;
            $model->precision = $request->precision;
            $model->status_id = $request->status;
            $model->save();
            $this->updatedBy($model);
            return $this->responseWithSuccess(_trans('message.Update successfully.'), 200);

        } catch (\Throwable $th) {
            dd($th);
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
}
