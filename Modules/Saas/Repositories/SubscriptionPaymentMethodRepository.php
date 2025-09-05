<?php

namespace Modules\Saas\Repositories;

use Illuminate\Support\Str;
use App\Services\Hrm\DeleteService;
use App\Models\Traits\RelationCheck;
use App\Helpers\CoreApp\Traits\AuthorInfoTrait;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Models\coreApp\Relationship\RelationshipTrait;
use Modules\Saas\Entities\SubscriptionCurrency;
use Modules\Saas\Entities\SubscriptionPaymentMethod;
use Modules\Saas\Repositories\SubscriptionPaymentMethodRepositoryInterface;

class SubscriptionPaymentMethodRepository implements SubscriptionPaymentMethodRepositoryInterface
{

    use AuthorInfoTrait, RelationshipTrait, RelationCheck, ApiReturnFormatTrait;
    protected $model;

    public function __construct(SubscriptionPaymentMethod $model)
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

        //id,  `name`,  `charge`,  `test_mode`, `data`, `currency_id`
        return [
            _trans('common.ID'),
            _trans('common.Name'),
            _trans('common.Charge'),
            _trans('common.Mood'),
            _trans('common.Data'),
            _trans('common.Currency'),
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
                    $action_button .= actionButton(_trans('common.Edit'), 'mainModalOpen(`' . route('saas.subscription_payment_methods.edit_modal', $data->id) . '`)', 'modal');
                }
                if (hasPermission('model_delete')) {
                    $action_button .= actionButton(_trans('common.Delete'), '__globalDelete(' . $data->id . ',`admin/saas/payment-methods/delete/`)', 'delete');
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
                    //id,  `name`,  `charge`,  `test_mode`, `data`, `currency_id`
                    'name' => $data->name,
                    'charge' => $data->charge,
                    'test_mode' => $data->test_mode ? '<small class="badge badge-success">Yes</small>' : '<small class="badge badge-danger">No</small>',
                    'data' => $data->data,
                    'currency_id' => $data->currency->title,
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
                if (hasPermission('designation_update')) {
                    $action_button .= '<a href="' . route('designation.edit', $data->id) . '" class="dropdown-item"> ' . $edit . '</a>';
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
        $currencyData = SubscriptionCurrency::where('status_id', 1)->get();

        $currencyOptions = [];

        foreach ($currencyData as $currency) {
            $currencyOptions[] = [
                'text' => $currency['title'] . ' - ' . $currency['symbol'],
                'value' => $currency['id'],
                'active' => false,
            ];
        }

        $attributes['currency']['options'] = $currencyOptions;

        return [
            'name' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'name',
                'class' => 'form-control ot-form-control ot_input',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Name'),
            ],
            'charge' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'charge',
                'class' => 'form-control ot-form-control ot_input',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.charge'),
            ],
            'slug' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'slug',
                'class' => 'form-control ot-form-control ot_input',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Slug'),
            ],
            'is_auto' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'is_auto',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Auto'),
                'options' => [
                    [
                        'text' => _trans('payroll.Active'),
                        'value' => 0,
                        'active' => true,
                    ],
                    [
                        'text' => _trans('payroll.Inactive'),
                        'value' => 1,
                        'active' => false,
                    ],
                ],
            ],
            'test_mode' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'test_mode',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Mode'),
                'options' => [
                    [
                        'text' => _trans('payroll.Active'),
                        'value' => 0,
                        'active' => true,
                    ],
                    [
                        'text' => _trans('payroll.Inactive'),
                        'value' => 1,
                        'active' => false,
                    ],
                ],
            ],
            'data' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'data',
                'class' => 'form-control ot-form-control ot_input',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Data'),
            ],
            'currency' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'currency',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Currency'),
                'options' => $currencyOptions,
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
        $currencyData = SubscriptionCurrency::where('status_id', 1)->get();

        $currencyOptions = [];

        foreach ($currencyData as $currency) {
            $currencyOptions[] = [
                'text' => $currency['title'] . ' - ' . $currency['symbol'],
                'value' => $currency['id'],
                'active' => $currency['id'] == $model->currency_id, // Preselect the value if it matches the model's currency_id
            ];
        }
        return [
            'name' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'name',
                'class' => 'form-control ot-form-control ot_input',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Name'),
                'value' => @$model->name,
            ],
            'charge' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'charge',
                'class' => 'form-control ot-form-control ot_input',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.charge'),
                'value' => @$model->charge,
            ],
            'slug' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'slug',
                'class' => 'form-control ot-form-control ot_input',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Slug'),
                'value' => @$model->slug,
            ],

            'is_auto' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'is_auto',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Auto'),
                'options' => [
                    [
                        'text' => _trans('payroll.Active'),
                        'value' => 1,
                        'active' => $model->is_auto == 1 ? true : false,
                    ],
                    [
                        'text' => _trans('payroll.Inactive'),
                        'value' => 0,
                        'active' => $model->is_auto == 0 ? true : false,
                    ],
                ],
            ],
            'test_mode' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'test_mode',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Mode'),
                'options' => [
                    [
                        'text' => _trans('payroll.Active'),
                        'value' => 1,
                        'active' => $model->test_mode == 1 ? true : false,
                    ],
                    [
                        'text' => _trans('payroll.Inactive'),
                        'value' => 0,
                        'active' => $model->test_mode == 0 ? true : false,
                    ],
                ],
            ],
            'data' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'data',
                'class' => 'form-control ot-form-control ot_input',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Data'),
                'value' => @$model->data,
            ],
            'currency' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'currency',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Currency'),
                'options' => $currencyOptions,
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
            $model->name = $request->name;
            $model->charge = $request->charge;
            $model->slug = $request->slug;
            $model->is_auto = $request->is_auto;
            $model->test_mode = $request->test_mode;
            $model->data = $request->data;
            $model->status_id = $request->status;
            $model->currency_id = $request->currency;
            // $model = setCompanyIdBranchId($model);
            $model->save();
            $this->createdBy($model);
            return $this->responseWithSuccess(_trans('message.Store successfully.'), 200);

        } catch (\Throwable $th) {
            dd($th);
            return $this->responseWithError(_trans('message.Something went wrong'), [], 400);
        }
    }

    public function newUpdate($request, $model)
    {
        try {
            $model = $this->model->where('id', $model)->first();

            $model = $model;
            $model->name = $request->name;
            $model->charge = $request->charge;
            $model->slug = $request->slug;
            $model->is_auto = $request->is_auto;
            $model->test_mode = $request->test_mode;
            $model->data = $request->data;
            $model->status_id = $request->status;
            $model->currency_id = $request->currency;
            $model->save();
            $this->updatedBy($model);
            return $this->responseWithSuccess(_trans('message.Designation update successfully.'), 200);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
}
