<?php

namespace Modules\Saas\Repositories;

use Dotenv\Util\Str;
use App\Services\Hrm\DeleteService;
use App\Models\Traits\RelationCheck;
use App\Helpers\CoreApp\Traits\FileHandler;
use App\Helpers\CoreApp\Traits\AuthorInfoTrait;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Models\coreApp\Relationship\RelationshipTrait;
use Modules\Saas\Entities\SubscriptionProduct;
use Modules\Saas\Entities\SubscriptionCurrency;
use Modules\Saas\Entities\SubscriptionPackagePlan;
use Modules\Saas\Repositories\SubscriptionPackagePlanRepositoryInterface;

class SubscriptionPackagePlanRepository implements SubscriptionPackagePlanRepositoryInterface
{

    use AuthorInfoTrait, RelationshipTrait, RelationCheck, ApiReturnFormatTrait, FileHandler;
    protected $model;

    public function __construct(SubscriptionPackagePlan $model)
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
            _trans('common.Subscription Plan'),
            _trans('common.Subscription Package'),
            _trans('common.Amount'),
            _trans('common.Discount Type'),
            _trans('common.Discount Amount'),
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

                if (hasPermission('subscription_packages_update')) {
                    $action_button .= '<a href="' . route('saas.subscription_packages.edit', [$data->id]) . '" class="dropdown-item">' . _trans('common.Edit') . '</a>';
                }
                if (hasPermission('subscription_packages_delete')) {
                    $action_button .= actionButton(_trans('common.Delete'), '__globalDelete(' . $data->id . ',`admin/saas/modules/packages/`)', 'delete');
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

                    'subscription_plan_id' => @$data->plan->title,
                    'subscription_package_id' => @$data->package->name,
                    'amount' => @$data->amount,
                    'discount_type' => @$data->discount_type,
                    'discount_amount' => @$data->discount_amount,
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
        $currencyData = SubscriptionCurrency::where('status_id', 1)->get();
        $productData = SubscriptionProduct::where('status_id', 1)->get();

        $productOptions = [];

        foreach ($productData as $product) {
            $productOptions[] = [
                'text' => $product['title'],
                'value' => $product['id'],
                'active' => false,
            ];
        }
        $attributes['product']['options'] = $productOptions;

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
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Name'),
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
            'image' => [
                'field' => 'input',
                'type' => 'file',
                'required' => false,
                'id' => 'image',
                'placeholder' => 'image',
                'class' => 'form-control ot_input ot-form-control mt-0',
                'col' => 'col-md-12 form-group  mb-3',
                'label' => _trans('common.Image'),
            ],

            'description' => [
                'field' => 'textarea',
                'type' => 'text',
                'required' => true,
                'id' => 'description',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Description'),
            ],

            'price' => [
                'field' => 'number',
                'type' => 'text',
                'required' => true,
                'id' => 'price',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Price'),
            ],
            'period' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'period',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Period'),
                'options' => [
                    [
                        'text' => _trans('subscription.Monthly'),
                        'value' => 1,
                        'active' => true,
                    ],
                    [
                        'text' => _trans('subscription.Yearly'),
                        'value' => 2,
                        'active' => false,
                    ],
                    [
                        'text' => _trans('subscription.Quarterly'),
                        'value' => 3,
                        'active' => false,
                    ],
                    [
                        'text' => _trans('subscription.Lifetime'),
                        'value' => 4,
                        'active' => false,
                    ],
                ],
            ],
            'term_wise_price' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'term_wise_price',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Term Wise Price'),
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
            'product' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'product',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Product'),
                'options' => $productOptions,
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
            'name' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'name',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Name'),
                'value' => @$model->name,
            ],
            'description' => [
                'field' => 'textarea',
                'type' => 'text',
                'required' => true,
                'id' => 'description',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Description'),
                'value' => @$model->description,
            ],

            'price' => [
                'field' => 'number',
                'type' => 'text',
                'required' => true,
                'id' => 'price',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Price'),
                'value' => @$model->price,
            ],
            'period' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'period',
                'class' => 'form-select select2-input ot_input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3 mt-3',
                'label' => _trans('common.Period'),
                'options' => [
                    [
                        'text' => _trans('subscription.Monthly'),
                        'value' => 1,
                        'active' => $model->period == 'monthly' ? true : false,
                    ],
                    [
                        'text' => _trans('payroll.Yearly'),
                        'value' => 2,
                        'active' => $model->period == 'yearly' ? true : false,
                    ],
                    [
                        'text' => _trans('subscription.Quarterly'),
                        'value' => 3,
                        'active' => $model->period == 'quarterly' ? true : false,
                    ],
                    [
                        'text' => _trans('payroll.Lifetime'),
                        'value' => 4,
                        'active' => $model->period == 'lifetime' ? true : false,
                    ],
                ],
            ],
            'term_wise_price' => [
                'field' => 'number',
                'type' => 'text',
                'required' => true,
                'id' => 'term_wise_price',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Term Wise Price'),
                'value' => @$model->term_wise_price,
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
            $model->description = $request->description;
            $model->price = $request->price;
            $model->period = $request->period;
            $model->term_wise_price = $request->term_wise_price;
            $model->status_id = $request->status;
            $model->currency_id = $request->currency;
            $model->product_id = $request->product;
            if ($request->hasFile('icon')) {
                $model->icon_id = $this->uploadImage($request->icon, 'uploads/icon')->id;
            }

            // $model = setCompanyIdBranchId($model);
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

            $model = $model;
            $model->title = $request->title;
            $model->slug = $request->slug;
            $model->name = $request->name;
            $model->description = $request->description;
            $model->price = $request->price;
            $model->period = $request->period;
            $model->term_wise_price = $request->term_wise_price;
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

        } catch (\Throwable $th) {
            dd($th);
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
}
