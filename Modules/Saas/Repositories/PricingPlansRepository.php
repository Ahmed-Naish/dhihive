<?php

namespace Modules\Saas\Repositories;

use Illuminate\Support\Str;
use App\Models\Traits\RelationCheck;
use Modules\Saas\Entities\PlanFeature;
use Modules\Saas\Entities\PricingPlan;
use App\Helpers\CoreApp\Traits\FileHandler;
use Modules\Saas\Entities\SaasSubscription;
use Modules\Saas\Entities\PricingPlanFeature;
use Modules\Saas\Entities\SubscriptionProduct;
use App\Helpers\CoreApp\Traits\AuthorInfoTrait;
use Modules\Saas\Entities\SubscriptionCurrency;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use Modules\Saas\Repositories\CmsRepositoryInterface;
use App\Models\coreApp\Relationship\RelationshipTrait;

class PricingPlansRepository implements CmsRepositoryInterface
{

    use AuthorInfoTrait, RelationshipTrait, RelationCheck, ApiReturnFormatTrait, FileHandler;
    protected $model;
    protected $pricingPlanFeature;
    protected $planFeature;

    public function __construct(PricingPlan $model, PricingPlanFeature $pricingPlanFeature, PlanFeature $planFeature)
    {
        $this->model = $model;
        $this->pricingPlanFeature = $pricingPlanFeature;
        $this->planFeature = $planFeature;
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

    public function planFeatures()
    {
        return $this->planFeature::query()->select('id', 'title')->get();
    }

    public function fields()
    {

        return [
            _trans('common.ID'),
            _trans('common.Plan Name'),
            _trans('common.Employee Limit'),
            _trans('common.Basic Price'),
            _trans('common.Popular'),
            _trans('common.Status'),
            _trans('common.Action'),
        ];
    }

    public function fieldDetails()
    {

        return [
            _trans('common.ID'),
            _trans('common.Feature'),
            _trans('common.Status'),
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
            $where[] = ['name', 'like', '%' . $request->search . '%'];
        }
        $data = $data
            ->where($where)
            ->orderBy('id', 'asc')
            ->paginate($request->limit ?? 2);
        return [
            'data' => $data->map(function ($data) {
                $action_button = '';

                $action_button .= '<a href="' . route('saas.pricing-plans.show', [$data->id]) . '" class="dropdown-item">' . _trans('common.Detail') . '</a>';
                $action_button .= '<a href="' . route('saas.pricing-plans.edit', [$data->id]) . '" class="dropdown-item">' . _trans('common.Edit') . '</a>';

                $isSubscription = SaasSubscription::query()
                ->whereHas('pricingPlanPrice', function ($q) use ($data) {
                    $q->whereHas('pricingPlan', fn ($q) => $q->where('id', $data->id));
                })
                ->count();

                if (!$isSubscription) {
                    $action_button .= actionButton(_trans('common.Delete'), '__globalDelete(' . $data->id . ',`admin/saas/pricing-plans/delete/`)', 'delete');
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
                $packageImage = '';
                $packageImage .= '<img data-toggle="tooltip" data-placement="top" title="' . $data->image . '" src="' . uploaded_asset($data->attachment_id) . '" class="staff-profile-image-small" >';

                return [
                    'id' => $data->id,
                    'name' => @$data->name,
                    'employee_limit' => @$data->employee_limit,
                    'basic_price' => currency_format(number_format(@$data->basic_price, 2)),
                    'is_popular' => @$data->is_popular == '1' ? 'Yes' : 'No',
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

    
    public function tableDetail($request, $id)
    {
        $data = $this->pricingPlanFeature->query();
        $where = array();
        $data = $data->where($where)->where('pricing_plan_id', $id)->get();

        $plan_feature = $this->planFeature->query();
        $where = array();
        $plan_feature = $plan_feature->where($where)->orderBy('id', 'asc')->paginate(50);

        $status_check = '<i class="fa-solid fa-check text-primary"></i>';
        $status_times = '<i class="fa-solid fa-times text-danger"></i>';
        $key = 0;
        return [
            'data' => $plan_feature->map(function ($plan_feature) use ($data, $status_check, $status_times, $key) {
                return [
                    'id' => $key++,
                    'feature' => $plan_feature->title,
                    'status' => $data->contains('plan_feature_id', $plan_feature->id) ? $status_check : $status_times,
                ];
            }),
            'pagination' => [
                'total' => $plan_feature->total(),
                'count' => $plan_feature->count(),
                'per_page' => $plan_feature->perPage(),
                'current_page' => $plan_feature->currentPage(),
                'total_pages' => $plan_feature->lastPage(),
                'pagination_html' => $plan_feature->links('backend.pagination.custom')->toHtml(),
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
                if (hasPermission('cms_edit')) {
                    $action_button .= '<a href="' . route('saas.cms.edit', $data->id) . '" class="dropdown-item"> ' . $edit . '</a>';
                }
                if (hasPermission('cms_delete')) {
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

    public function destroy($request, $id)
    {
        try {
            if ($id) {
                $model = $this->model->where('id', $id)->delete();
                return $this->responseWithSuccess(_trans('message.Delete successfully.'), $model);
            } else {
                return $this->responseWithError(_trans('message.Item not found'), [], 400);
            }
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
        // $table_name = $this->model->getTable();
        // $foreign_id = \Illuminate\Support\Str::singular($table_name) . '_id';
        // return \App\Services\Hrm\DeleteService::deleteData($table_name, $foreign_id, $model->id);
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
            'title' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'title',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Title'),
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
            'link' => [
                'field' => 'input',
                'type' => 'text',
                'required' => false,
                'id' => 'link',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Link'),
            ],
            'text_color' => [
                'field' => 'input',
                'type' => 'color',
                'required' => true,
                'id' => 'text_color',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Text Color'),
            ],
            'bg_color' => [
                'field' => 'input',
                'type' => 'color',
                'required' => true,
                'id' => 'bg_color',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Background Color'),
            ],
            'order' => [
                'field' => 'input',
                'type' => 'number',
                'required' => true,
                'id' => 'order',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Order'),
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
            'style' => [
                'type' => 'radio',
                'field' => 'input',
                'required' => true,
                'id' => 'style',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Style'),
                'options' => [
                    [
                        'label' => _trans('Style 1'),
                        'value' => '1',
                        'image' => global_asset('vendor/Saas/Assets/images/styles/style-1.png'),
                        'checked' => 'checked',
                    ],
                    [
                        'label' => _trans('Style 2'),
                        'value' => '2',
                        'image' => global_asset('vendor/Saas/Assets/images/styles/style-2.png'),
                        'checked' => null,
                    ], [
                        'label' => _trans('Style 3'),
                        'value' => '3',
                        'image' => global_asset('vendor/Saas/Assets/images/styles/style-3.png'),
                        'checked' => null,
                    ],
                    [
                        'label' => _trans('Style 4'),
                        'value' => '4',
                        'image' => global_asset('vendor/Saas/Assets/images/styles/style-4.png'),
                        'checked' => null,
                    ], [
                        'label' => _trans('Style 5'),
                        'value' => '5',
                        'image' => global_asset('vendor/Saas/Assets/images/styles/style-5.png'),
                        'checked' => null,
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
            'image' => [
                'field' => 'input',
                'type' => 'file',
                'required' => false,
                'id' => 'icon',
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
                'value' => @$model->description,
            ],
            'link' => [
                'field' => 'input',
                'type' => 'text',
                'required' => false,
                'id' => 'link',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Link'),
                'value' => @$model->link,
            ],
            'text_color' => [
                'field' => 'input',
                'type' => 'color',
                'required' => true,
                'id' => 'text_color',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Text Color'),
                'value' => @$model->text_color,
            ],
            'bg_color' => [
                'field' => 'input',
                'type' => 'color',
                'required' => true,
                'id' => 'bg_color',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Background Color'),
                'value' => @$model->bg_color,
            ],
            'order' => [
                'field' => 'input',
                'type' => 'number',
                'required' => true,
                'id' => 'order',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.order'),
                'value' => @$model->order,
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
                        'text' => _trans('common.Active'),
                        'value' => 1,
                        'active' => $model->status_id == 1 ? true : false,
                    ],
                    [
                        'text' => _trans('common.Inactive'),
                        'value' => 4,
                        'active' => $model->status_id == 4 ? true : false,
                    ],
                ],
            ],
            'style' => [
                'type' => 'radio',
                'field' => 'input',
                'required' => true,
                'id' => 'style',
                'class' => 'form-control ot-form-control ot_input mb-3',
                'col' => 'col-md-12 form-group',
                'label' => _trans('common.Style'),
                'options' => [
                    [
                        'label' => _trans('Style 1'),
                        'value' => '1',
                        'image' => global_asset('vendor/Saas/Assets/images/styles/style-1.png'),
                        'checked' => $model->style == 1 ? 'checked' : null,
                    ],
                    [
                        'label' => _trans('Style 2'),
                        'value' => '2',
                        'image' => global_asset('vendor/Saas/Assets/images/styles/style-2.png'),
                        'checked' => $model->style == 2 ? 'checked' : null,
                    ], [
                        'label' => _trans('Style 3'),
                        'value' => '3',
                        'image' => global_asset('vendor/Saas/Assets/images/styles/style-3.png'),
                        'checked' => $model->style == 3 ? 'checked' : null,
                    ],
                    [
                        'label' => _trans('Style 4'),
                        'value' => '4',
                        'image' => global_asset('vendor/Saas/Assets/images/styles/style-4.png'),
                        'checked' => $model->style == 4 ? 'checked' : null,
                    ], [
                        'label' => _trans('Style 5'),
                        'value' => '5',
                        'image' => global_asset('vendor/Saas/Assets/images/styles/style-5.png'),
                        'checked' => $model->style == 5 ? 'checked' : null,
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
            $model->slug = Str::slug($request->title, '_', 'UTF-8');
            $model->description = $request->description;

            // image upload
            if ($request->hasFile('image')) {
                $model->attachment_id = $this->uploadImage($request->image, 'uploads/cms')->id;
            }

            $model->link = $request->link;
            $model->text_color = $request->text_color;
            $model->bg_color = $request->bg_color;
            $model->order = $request->order;
            $model->style = $request->style;
            $model->status_id = $request->status;
            $model->save();
            // $this->createdBy($model);
            return $this->responseWithSuccess(_trans('message.Store successfully.'), 200);
        } catch (\Throwable $th) {
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
            $model->description = $request->description;
            $model->link = $request->link;
            $model->text_color = $request->text_color;
            $model->bg_color = $request->bg_color;
            $model->order = $request->order;
            $model->style = $request->style;
            $model->status_id = $request->status;

            if ($request->hasFile('image')) {
                if (@$model->attachment_id) {
                    $this->deleteImage(asset_path($model->attachment_id));
                }
                $model->attachment_id = $this->uploadImage($request->image, 'uploads/cms')->id;
            }

            $model->save();
            // $this->updatedBy($model);
            return $this->responseWithSuccess(_trans('message.Update successfully.'), 200);

        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
}
