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
use App\Models\coreApp\Relationship\RelationshipTrait;
use Modules\Saas\Entities\EmailTemplate;
use Modules\Saas\Repositories\EmailTemplateRepositoryInterface;

class EmailTemplateRepository implements EmailTemplateRepositoryInterface
{

    use AuthorInfoTrait, RelationshipTrait, RelationCheck, ApiReturnFormatTrait, FileHandler;
    protected $model;
    protected $pricingPlanFeature;
    protected $planFeature;

    public function __construct(EmailTemplate $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function fields()
    {

        return [
            _trans('common.ID'),
            _trans('common.Title'),
            _trans('common.Subject'),
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
        try {
            $model = new $this->model;
            $model->title = $request->title;
            $model->slug = Str::slug($request->title);
            $model->subject = $request->subject;
            $model->content = $request->content;
            $model->status_id = $request->status_id;
            $model->created_by = auth()->id();
            $model->updated_by = auth()->id();
            $model->save();
            return $this->responseWithSuccess(_trans('message.Email Template created successfully.'), $model);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
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
            ->orderBy('id', 'asc')
            ->paginate($request->limit ?? 2);
        return [
            'data' => $data->map(function ($data) {
                $action_button = '';
                // $action_button .= '<a href="' . route('saas.email-template.show', [$data->id]) . '" class="dropdown-item">' . _trans('common.Detail') . '</a>';
                $action_button .= '<a href="' . route('saas.email-template.edit', [$data->id]) . '" class="dropdown-item">' . _trans('common.Edit') . '</a>';
                // $action_button .= actionButton(_trans('common.Delete'), '__globalDelete(' . $data->id . ',`admin/saas/email-template/delete/`)', 'delete');

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
                    'title' => Str::limit(@$data->title, 50),
                    'subject' => Str::limit(@$data->subject, 50),
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

    public function update($request, $id){
        try {
            $model = $this->model->find($id);
            $model->title = $request->title;
            $model->subject = $request->subject;
            $model->content = $request->content;
            $model->status_id = $request->status_id;
            $model->updated_by = auth()->id();
            $model->save();
            return $this->responseWithSuccess(_trans('message.Email Template updated successfully.'), 200);

        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }

    public function destroy($request, $id)
    {
        try {
            if ($id) {
                $model = $this->model->where('id', $id)->delete();
                return $this->responseWithSuccess(_trans('message.Email Template delete successfully.'), $model);
            } else {
                return $this->responseWithError(_trans('message.Item not found'), [], 400);
            }
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
}
